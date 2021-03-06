<?php

namespace phpws;

/**
 * @version $Id$
 * @author Matthew McNaney <mcnaney at gmail dot com>
 */
require_once 'PEAR.php';

/**
 * Error logging presets
 * If you cannot secure your log directory, this should be changed
 * to FALSE
 */
define('PHPWS_LOG_ERRORS', TRUE);

class PHPWS_Error
{

    /**
     * Replacement functions for PEAR's isError function. Prevents
     * strict php 5 errors
     */
    public static function isError($item)
    {
        $pear = \phpws\PHPWS_Error::getPear();
        return $pear->isError($item);
    }

    public static function getPear()
    {
        static $pear = null;
        if (empty($pear)) {
            $pear = new \PEAR;
        }
        return $pear;
    }

    /**
     * Replacement functions for PEAR's raiseError function. Prevents
     * strict php 5 errors
     */
    public static function raiseError($message = null, $code = null,
            $mode = null, $options = null, $userinfo = null,
            $error_class = null, $skipmsg = false)
    {
        $pear = \phpws\PHPWS_Error::getPear();
        return $pear->raiseError($message, $code, $mode, $options, $userinfo,
                        $error_class, $skipmsg);
    }

    public static function logIfError($item)
    {
        if (\phpws\PHPWS_Error::isError($item)) {
            \phpws\PHPWS_Error::log($item);
            return true;
        } else {
            return false;
        }
    }

    public static function get($value, $module, $funcName = NULL,
            $extraInfo = NULL)
    {
        if (empty($module)) {
            return \phpws\PHPWS_Error::get(PHPWS_NO_MODULE, 'core',
                            '\phpws\PHPWS_Error::get',
                            'Value: ' . $value . ', Function: ' . $funcName);
        }

        try {
            $errorFile = \phpws\PHPWS_Core::getConfigFile($module, 'error.php');
        } catch (\Exception $e) {
            $errorFile = null;
        }

        if (empty($errorFile)) {
            // Error file not found in local config directory. Checking inc/ source directory
            if ($module == 'core') {
                $errorFile = PHPWS_SOURCE_DIR . 'src-phpws-legacy/config/ErrorCodeDefines.php';
            } else {
                $errorFile = PHPWS_SOURCE_DIR . 'mod/' . $module . '/inc/error.php';
            }

            if (!is_file($errorFile)) {
                // prevent infinite loop
                if ($module == 'core') {
                    echo _('Core could not locate its error.php file.');
                    die;
                }
                return \phpws\PHPWS_Error::get(PHPWS_NO_ERROR_FILE, 'core',
                                '\phpws\PHPWS_Error::get', 'Module: ' . $module);
            }
        }

        include $errorFile;

        if (!isset($errors))
            return FALSE;

        if (\phpws\PHPWS_Error::isError($value)) {
            $value = $value->getCode();
        }

        if ($module != 'core') {
            $fullError[] = $module;
        } else {
            $fullError[] = 'Core';
        }

        if (isset($funcName)) {
            $fullError[] = " - $funcName()";
        }

        if (isset($errors[$value])) {
            $message = $errors[$value];
        } else {
            $message = _('Unknown error code.');
        }

        $fullError[] = ' - ' . $message;

        if (isset($extraInfo)) {
            if (is_array($extraInfo)) {
                $message = vsprintf($message, $extraInfo);
            } else {
                $fullError[] = ' [' . $extraInfo . ']';
            }
        }

        $error = \phpws\PHPWS_Error::raiseError($message, $value, NULL, NULL,
                        implode('', $fullError));
        return $error;
    }

    public static function log($value, $module = NULL, $funcName = NULL,
            $extraInfo = NULL)
    {
        if ((bool) PHPWS_LOG_ERRORS == FALSE) {
            return;
        }
        if (!\phpws\PHPWS_Error::isError($value)) {
            $error = \phpws\PHPWS_Error::get($value, $module, $funcName,
                            $extraInfo);
        } else {
            $error = $value;
        }
        $final = \phpws\PHPWS_Error::printError($error);
        \phpws2\Error::logError($final);
    }

    public static function printError($error)
    {
        $code = $error->getcode();
        $message = $error->getUserInfo();

        if (!isset($message)) {
            $message = $error->getmessage();
        }

        $final = '[' . $code . '] ' . $message;

        return $final;
    }

}
