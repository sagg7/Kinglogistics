<?php

namespace App\Exceptions;

use Exception;

class ShiftNotActiveException extends Exception
{
    /**
     * Render the exception into an HTTP response.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function render($request)
    {
        return response(['status' => 'error', 'message' => 'You don\'t have an active shift to perform this action'], 400);
    }
}
