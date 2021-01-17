<?php


namespace core;


use core\middleware\Middleware;

class Controller
{
    public string $layout = 'main';
    public string $action = '';

    /**
     * @var Middleware[]
     */
    protected array $middleware = [];

    /**
     * @param string $layout
     */
    final public function setLayout(string $layout): void
    {
        $this->layout = $layout;
    }

    /**
     * @param string $view
     * @param array $params
     * @return string
     */
    final public function render(string $view, array $params = []): string
    {
        return Application::$app->view->render($view, $params);
    }

    /**
     * @param Middleware $middleware
     */
    final public function registerMiddleware(Middleware $middleware): void
    {
        $this->middleware[] = $middleware;
    }

    /**
     * @return Middleware[]
     */
    final public function getMiddleware(): array
    {
        return $this->middleware;
    }

}