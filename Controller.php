<?php


namespace core;


use core\middleware\Middleware;

final class Controller
{
    public string $layout = 'main';
    public string $action = '';

    /**
     * @var Middleware[]
     */
    protected array $middleware = [];

    /**
     * @param $layout
     */
    public function setLayout(string $layout): void
    {
        $this->layout = $layout;
    }

    public function render(string $view, array $params = []): string
    {
        return Application::$app->view->render($view, $params);
    }

    public function registerMiddleware(Middleware $middleware): void
    {
        $this->middleware[] = $middleware;
    }

    /**
     * @return Middleware[]
     */
    public function getMiddleware(): array
    {
        return $this->middleware;
    }

}