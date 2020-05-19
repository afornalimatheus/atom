<?php

namespace App;

use Dflydev\Provider\DoctrineOrm\DoctrineOrmServiceProvider;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Silex\Application;
use Silex\Provider\DoctrineServiceProvider;
use Silex\Provider\ServiceControllerServiceProvider;
use Silex\Provider\SessionServiceProvider;
use Silex\Provider\TwigServiceProvider;
use Symfony\Component\Debug\ExceptionHandler;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Whoops\Handler\PrettyPageHandler;
use Whoops\Run;

class LocalApplication extends Application
{

    /**
     * LocalApplication constructor.
     * @param array $values
     */
    public function __construct(array $values = [])
    {
        parent::__construct($values);

        ExceptionHandler::register(false);

        $this->before(function (Request $request) {
        });

        $this->init();
        $self = $this;

        //handling CORS response with right headers
        $this->after(function (Request $request, Response $response) use ($self) {

        });
    }

    public function init()
    {
        $whoops = new Run();
        $whoops->pushHandler(new PrettyPageHandler());
        $whoops->register();

        $this->registerProviders();

        $this->error(function (\Exception $e, Request $request, $code) {

            $request->getBaseUrl();
            $request->getMethod();
            $request->getClientIp();
            $data = [
                'data_layer' => '{}',
                'logon' => false,
                'type' => '',
                'title' => 'Houve um erro no sistema!',
                'msg' => 'Volte para',
                'link' => true
            ];
            var_dump($e->getMessage());
            die("paro");
            return $this['twig']->render('site/users.twig', $data);
        });

        $this->mountController();
    }

    public function registerProviders()
    {
        $configDoctrine = include __DIR__ . '/../../app/config/doctrine.php';

        $this->register(new DoctrineServiceProvider(), $configDoctrine['db.options']);
        $this->register(new DoctrineOrmServiceProvider(), $configDoctrine);

        $this->register(new ServiceControllerServiceProvider());

        $sessionProvider = new SessionServiceProvider();
        $this->register($sessionProvider);

        $this->register(new TwigServiceProvider(), [
            'twig.path' => __DIR__ . '/../../views', 
            'debug' => true,
        ]);
    }

    public function mountController()
    {
        /**
         * Config Controllers
         */
        $routesControllers = [
            '/' => '\App\Controllers\Factory\UserControllerFactory',
        ];

        $route = $this->getUrlRoute();
        
        if (array_key_exists($route, $routesControllers)) {
            $controllerRoute = $routesControllers[$route];
            $this->mount($route, (new $controllerRoute($this))->getController());
        } else {
            $route = '/';
            $controllerRoute = $routesControllers[$route];
            $this->mount($route, (new $controllerRoute($this))->getController());
        }
    }

    /**
     * @return string
     */
    public function getUrlRoute()
    {
        $requestUri = filter_input(INPUT_SERVER, 'REQUEST_URI');
        $partsUri = explode('/', $requestUri);

        if ($partsUri[1] == 'admin') {
            $route = '/admin';

            if (isset($partsUri[2]) && $partsUri[2] != 'access') {
                $route .= '/' . $partsUri[2];
            } else {
                $route .= '/';
            }
        } else {
            $route = '/' . $partsUri[1];
        }

        return $route;
    }
}
