<?php


namespace core;


final class Request
{
    private string $base_path;
    private string $path;
    private string $method;
    private array $http_methods = array('get', 'post', 'put', 'patch', 'delete');

    public function __construct(string $base_path = '')
    {
        $this->base_path = $base_path;
        $this->path = $this->path();
        $this->method = $this->method();
    }

    /**
     * base_path getter
     *
     * @return string
     */
    public function getBasePath(): string
    {
        return $this->base_path;
    }

    /**
     * Get request URI and set the path to everything before the question mark,
     * or first slash, removing training slashes
     *
     * @return string
     */
    public function path(): string
    {
        $path = $_SERVER['REQUEST_URI'] ?? '/';
        $pos = strpos($path, '?');
        if ($pos) {
            $path = substr($path, 0, $pos);
        }
        return rtrim($path, '/');
    }

    /**
     * path getter
     *
     * @return string
     */
    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * Get the request method, defaulting to GET
     *
     * @return string
     */
    public function method(): string
    {
        if (array_key_exists('REQUEST_METHOD', $_SERVER)) {
            $method = strtolower($_SERVER['REQUEST_METHOD']);

            if (in_array($method, $this->http_methods)) {
                return $method;
            }
        }

        return 'get';
    }

    /**
     * method getter
     *
     * @return string
     */
    public function getMethod(): string
    {
        return $this->method;
    }

    /**
     * @return bool
     */
    public function isGet(): bool
    {
        return $this->method === 'get';
    }

    /**
     * @return bool
     */
    public function isPost(): bool
    {
        return $this->method === 'post';
    }

    /**
     * Get input values from request
     *
     * @return array
     */
    public function getBodyData(): array
    {
        $body = [];

        if ($this->isGet()) {
            foreach ($_GET as $key => $value) {
                $body[$key] = filter_input(INPUT_GET, $key, FILTER_SANITIZE_SPECIAL_CHARS);
            }
        }

        if ($this->isPost()) {
            foreach ($_POST as $key => $value) {
                $body[$key] = filter_input(INPUT_POST, $key, FILTER_SANITIZE_SPECIAL_CHARS);
            }
        }

        return $body;
    }
}