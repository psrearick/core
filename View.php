<?php


namespace app;


final class View
{
    public string $title = "";

    /**
     * @param $view
     * @param array $params
     * @return string|string[]
     */
    public function render(string $view, array $params = []): string
    {
        $viewContent = $this->renderView($view, $params);
        $layoutContent = $this->layoutContent();
        return str_replace('{{content}}', $viewContent, $layoutContent);
    }


    /**
     * @return false|string
     * cache and return current layout
     */
    private function layoutContent(): string
    {
        $layout = Application::$app->layout;
        if (Application::$app->controller){
            $layout = Application::$app->controller->layout;
        }
        ob_start();
        include_once Application::$ROOT . "/views/layouts/$layout.php";
        return ob_get_clean();
    }


    /**
     * @param $view
     * @param $params
     * @return false|string
     */
    private function renderView(string $view, array $params): string
    {
        foreach ($params as $key => $value) {
            $$key = $value;
        }
        ob_start();
        include_once Application::$ROOT . "/views/$view.php";
        return ob_get_clean();
    }
}