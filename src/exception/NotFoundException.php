<?php


namespace app\src\exception;


class NotFoundException extends \Exception
{
    protected $message = 'Page not found';
    protected $code = 404;
}