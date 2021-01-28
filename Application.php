<?php

namespace core;

use core\database\Database;
use Exception;

/**
 * Class Application
 * @package app
 */
final class Application
{
    public static string $ROOT;

    public string $layout = 'main';
    public Router $router;
    public Request $request;
    public Response $response;
    public Session $session;
    public Database $db;
    public ?UserModel $user = null;
    public static Application $app;
    public ?Controller $controller = null;
    public UserModel $userClass;
    public View $view;
    public array $config;

    public function __construct(string $root, array $conf)
    {
        self::$ROOT = $root;
        self::$app = $this;
        $this->request = new Request();
        $this->response = new Response();
        $this->session = new Session();
        $this->router = new Router($this->request, $this->response);
        $this->view = new View();
        $this->config = $conf;
        $this->db = new Database($conf);
        $this->userClass = new $this->config['userClass'];
        $primaryValue = $this->session->get('user');

        if ($primaryValue) {
            $primaryKey = $this->userClass->primaryKey();
            $this->user = $this->userClass->findOne([$primaryKey => $primaryValue]);
        } else {
            $this->user = null;
        }
    }

    /**
     * @return bool
     */
    public static function isGuest(): bool
    {
        return !self::$app->user;
    }

    /**
     * Run Application
     */
    public function run(): void
    {
        try {
            echo $this->router->resolve();
        } catch (Exception $e) {
            $this->response->setStatusCode($e->getCode());
            echo $this->view->render('_error', [
                'exception' => $e
            ]);
        }
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

    /**
     * @param UserModel $user
     * @return bool
     */
    public function login(UserModel $user): bool
    {
        $this->user = $user;
        $key = $user->primaryKey();
        $value = $user->{$key};
        $this->session->set('user', $value);
        return true;
    }

    /**
     * @return bool
     */
    public function logout(): bool
    {
        $this->user = null;
        $this->session->remove('user');
        return true;
    }
}