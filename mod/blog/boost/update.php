<?php

  /**
   * @author Matthew McNaney <mcnaney at gmail dot com>
   * @version $Id$
   */

function blog_update(&$content, $currentVersion)
{
    switch ($currentVersion) {

    case version_compare($currentVersion, '1.2.2', '<'):
        $content[] = 'This package will not update versions prior to 1.2.2.';
        return false;

    case version_compare($currentVersion, '1.2.3', '<'):
        $content[] = '<pre>
1.2.3 Changes
-------------
+ Make call to resetKeywords in search to prevent old search word retention.
</pre>';

    case version_compare($currentVersion, '1.4.1', '<'):
        $content[] = '<pre>';

        $db = new PHPWS_DB('blog_entries');
        $result = $db->addTableColumn('image_id', 'int NOT NULL default 0');
        if (PEAR::isError($result)) {
            PHPWS_Error::log($result);
            $content[] = 'Unable to add image_id colume to blog_entries table.</pre>';
            return false;
        }

        $files = array('templates/edit.tpl',
                       'templates/settings.tpl',
                       'templates/view.tpl',
                       'templates/submit.tpl',
                       'templates/user_main.tpl',
                       'templates/list_view.tpl');

        $content[] = '+ Updated template files';
        if (PHPWS_Boost::updateFiles($files, 'blog')) {
            $content[] = " o Files copied successfully:";

        } else {
            $content[] = " o Failed to copy files:";
        }

        $content[] = "    " . implode("\n    ", $files);
        $content[] = '
1.4.1 Changes
-------------
+ Added missing category tags to entry listing.
+ Added ability for anonymous and users without blog permission to
  submit entries for later approval.
+ Added setting to allow anonymous submission.
+ Added ability to place images on Blog entries without editor.
+ Added pagination to Blog view.
+ Added link to reset the view cache.
+ Added ability to add images to entry without editor.
+ Added missing translate calls.
+ Changed edit form layout.
</pre>';
    case version_compare($currentVersion, '1.4.2', '<'):
        $content[] = '<pre>';
        $files = array('templates/list.tpl');
        if (PHPWS_Boost::updateFiles($files, 'blog')) {
            $content[] = '+ Updated templates/blog/list.tpl file.';
        } else {
            $content[] = '+ Unable to update templates/blog/list.tpl file.';
        }
        $content[] = '1.4.2 Changes
-------------
+ Fixed bug causing error message when Blog listing moved off front page.
+ Changes "Entry" column to "Summary" on admin list. Was not updated since summary was added.
</pre>';

    case version_compare($currentVersion, '1.4.3', '<'):
        $content[] = '<pre>1.4.2 Changes
-------------';
        $files = array('img/blog.png');
        if (PHPWS_Boost::updateFiles($files, 'blog')) {
            $content[] = '+ Updated control panel icon.';
        } else {
            $content[] = '+ Unable to update control panel icon.';
        }
        $content[] = '+ Priviledged blog entries now forward to login page.
</pre>';

    } // end of switch
    return true;
}

?>