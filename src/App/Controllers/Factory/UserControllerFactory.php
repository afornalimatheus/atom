<?php

namespace App\Controllers\Factory;

use Silex\Application;

use App\Service\UserService;
use App\Controllers\UserController;

class UserControllerFactory
{
    /**
     * @var UserController
     */
    private $controller;

    public function __construct(Application $app)
    {
        $payInfoService = new UserService($app['orm.em']);
        $this->controller = new UserController($payInfoService);
    }

    public function getController()
    {
        return $this->controller;
    }
}