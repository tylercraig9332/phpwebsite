<?php

namespace phpws2\Http;

/**
 * Description
 * @author Jeff Tickle <jtickle at tux dot appstate dot edu>
 */
class NotAcceptableException extends Exception
{

    protected function createResponse(\Canopy\Request $request, \Exception $previous = null)
    {
        return new NotAcceptableResponse($request, $previous);
    }

}
