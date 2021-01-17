<?php


namespace app\exception;


use Exception;

class ForbiddenException extends Exception
{
    protected $message = "You do not have permission to access this page";
    protected $code = 403;
}