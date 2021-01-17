<?php


namespace core;


final class Response
{
    /**
     * @param int $code
     */
    public function setStatusCode(int $code): void
    {
        http_response_code($code);
    }

    /**
     * @param string $url
     */
    public function redirect(string $url): void
    {
        header('Location: ' . $url);
    }
}