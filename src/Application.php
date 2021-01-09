<?php

namespace app\src;

/**
 * Class Application
 * @package app\src
 */
class Application
{
    public static string $ROOT;
    public Router $router;
    public Request $request;
    public Response $response;
    public static Application $app;
    public Controller $controller;

    public function __construct($root)
    {
        self::$ROOT = $root;
        self::$app = $this;
        $this->request = new Request();
        $this->response = new Response();
        $this->router = new Router($this->request, $this->response);
    }

    /**
     *
     */
    public function run()
    {
        echo $this->router->resolve();
    }

    /**
     * @return Controller
     */
    public function getController(): Controller
    {
        return $this->controller;
    }

    /**
     * @param Controller $controller
     */
    public function setController(Controller $controller): void
    {
        $this->controller = $controller;
    }
}