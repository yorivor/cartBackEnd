<?php

namespace App\Exceptions;

use Exception;

class ApplicationException extends Exception
{
    /**
     * Report the exception.
     *
     * @return void
     */
    public function report()
    {
    }


    public function render($request)
    {
    }
}