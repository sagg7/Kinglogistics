<?php

namespace App\Exceptions;

use Exception;

class DriverHasUnfinishedLoadsException extends Exception
{
    /**
     * Report the exception.
     *
     * @return bool|null
     */
    public function report()
    {
        //
    }

    /**
     * Render the exception into an HTTP response.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function render($request)
    {
        return response(['status' => 'error', 'message' => 'You have unfinished loads'], 400);
    }
}
