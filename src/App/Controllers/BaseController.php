<?php

namespace App\Controllers;

use App\Messages\FlashMessenger;
use App\Service\BaseService;
use App\Storage\StorageClass;
use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Twig_Environment;
class BaseController
{
    const KEY_CLIENT = 'client';

    /**
     * @var \Silex\ControllerCollection
     */
    public $controllers;

    /**
     * @var BaseService
     */
    protected $service;

    /**
     * @var Session
     */
    protected $session;

    /**
     * @var Twig_Environment
     */
    protected $view;

    /**
     * @var Request
     */
    protected $request;

    /**
     * @var FlashMessenger
     */
    protected $flashMessenger;

    /**
     * @var string
     */
    protected $style = '';

    /**
     * @var string
     */
    protected $js = '';

    /**
     * BaseController constructor.
     *
     * @param BaseService $baseService
     */
    public function __construct(BaseService $baseService)
    {
        $this->service = $baseService;
    }

    /**
     * @param BaseService $service
     */
    public function setBaseService(BaseService $service)
    {
        $this->service = $service;
    }

    /**
     * @param Twig_Environment $view
     */
    public function setTemplateControl(Twig_Environment $view)
    {
        $this->view = $view;
    }

    /**
     * @param FlashMessenger $flashMessenger
     */
    public function setFlashMessenger($flashMessenger)
    {
        $this->flashMessenger = $flashMessenger;
    }

    /**
     * @return FlashMessenger
     */
    public function getFlashMessenger()
    {
        return $this->flashMessenger;
    }

    /**
     * @param Session $session
     */
    public function setSession(Session $session)
    {
        $this->session = $session;

        $client = $this->getClassInSession(self::KEY_CLIENT);

        if ($client != null && !is_array($client)) {
            $this->service->setClientData($client);
        }
    }

    /**
     * @return Session
     */
    public function getSession()
    {
        return $this->session;
    }

    /**
     * @param $route
     * @param $method
     */
    public function get($route, $method)
    {
        $this->controllers->get($route, function (Application $app, Request $request) use ($method) {
            $this->setRequest($request);
            $this->setTemplateControl($app['twig']);
            $this->setSession($app['session']);

            $flashMessenger = new FlashMessenger($app['session']);
            $this->setFlashMessenger($flashMessenger);

            return $this->$method();
        });
    }

    /**
     * @param $route
     * @param $method
     */
    public function post($route, $method)
    {
        $this->controllers->post($route, function (Application $app, Request $request) use ($method) {
            $this->setRequest($request);
            $this->setTemplateControl($app['twig']);
            $this->setSession($app['session']);

            $flashMessenger = new FlashMessenger($app['session']);
            $this->setFlashMessenger($flashMessenger);

            return $this->$method();
        });
    }

    /**
     * @param $route
     * @param $method
     */
    public function put($route, $method)
    {
        $this->controllers->put($route, function (Application $app, Request $request) use ($method) {
            $this->setRequest($request);
            $this->setTemplateControl($app['twig']);
            $this->setSession($app['session']);

            $flashMessenger = new FlashMessenger($app['session']);
            $this->setFlashMessenger($flashMessenger);

            return $this->$method();
        });
    }

    /**
     * @param $route
     * @param $method
     */
    public function delete($route, $method)
    {
        $this->controllers->delete($route, function (Application $app, Request $request) use ($method) {
            $this->setRequest($request);
            $this->setTemplateControl($app['twig']);
            $this->setSession($app['session']);

            $flashMessenger = new FlashMessenger($app['session']);
            $this->setFlashMessenger($flashMessenger);

            return $this->$method();
        });
    }

    /**
     * @return array
     */
    public function getParameters()
    {
        $requestVars = [];
        if ($_SERVER['REQUEST_METHOD'] == 'PUT') {
            parse_str(file_get_contents("php://input"), $requestVars);
        } else if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $requestVars = $_POST;
        } else if ($_SERVER['REQUEST_METHOD'] == 'GET') {
            $requestVars = $_GET;
        }
		
        return  $this->filterRequest($requestVars);
    }
	
	private function filterRequest($pars) {
        $removeItems = [
            "--",
            "/*",
            "char",
            "alter",
            "begin",
            "cast",
            "create",
            "cursor",
            "declare",
            "delete",
            "drop",
            "end",
            "exec",
            "execute",
            "fetch",
            "insert",
            "kill",
            "open",
            "select",
            "sys",
            "table",
            "update"
        ];

        foreach ($pars as $key => $value) {
            $pars[$key] = str_ireplace($removeItems, '', $value);
        }
        return $pars;
    }

    /**
     * @param $name
     * @return string
     */
    public function getParameter($name)
    {
        $pars = $this->getParameters();
        return isset($pars[$name]) ? $pars[$name] : '';
    }

    /**
     * @param Request $request
     */
    public function setRequest(Request $request)
    {
        $this->request = $request;
    }

    /**
     * @return null|Request
     * @throws \Exception
     */
    public function getRequest()
    {
        $request = null;
        if ($this->request instanceof Request) {
            $request = $this->request;
        } else {
            throw new \Exception("Request object not set!");
        }
        return $request;
    }

    /**
     * @return string
     */
    public function getQueryString()
    {
        return $_SERVER['QUERY_STRING'];
    }

    /**
     * @param $key
     * @return mixed|null
     * @throws \Exception
     */
    public function getClassInSession($key)
    {

        if ($this->getSession()->has($key)) {
            try {
                $content = $this->getSession()->get($key);
                if (!is_string($content)) {
                    return $content;
                }
                return unserialize($content);
            } catch (\Exception $e) {
                d($e->getMessage());
            }
        }

        return null;
    }

    /**
     * @param $key
     * @param $clazz
     */
    public function setClassInSession($key, $clazz)
    {
        $this->addRegister($key, $clazz);
        $this->getSession()->set($key, serialize($clazz));
    }

    /**
     * @param $key
     * @param $object
     * @return bool
     */
    public function addRegister($key, $object)
    {

        $storage = new StorageClass();
        return $storage->store($key, $object);
    }


    protected function getClientData()
    {
        $client = $this->service->getClientData();
        $this->getSession()->set('client', $client);

        $name = $client['name'];
        $partsName = explode(' ', $name);
        if (isset($partsName[0])) {
            $client['name'] = $partsName[0];
        }

        return $client;
    }

    public function redirect($url, $go = true)
    {
        if ($go) {
            header('Location:' . $url);
            exit();
        }

        echo 'redirect for : ' . $url;
        exit();
    }

    /**
     * @param $tpl
     * @param array $data
     * @param string $prefix
     * @return string
     */
    public function render($tpl, $data = [], $prefix = 'site')
    {

        $data['header_contract'] = false;
        if ($tpl == 'enter/index.twig') {
            $data['header_contract'] = true;
        }

        $data['data_layer'] = '{}';

        $data['type'] = '';
        $clientData = $this->getClassInSession(self::KEY_CLIENT);
        $data['consultant'] = '';
        $data['logon'] = ($clientData != null);

        $data['contact_menu'] = $this->session->get('link_contact');

        if (!is_null($clientData)) {
            $name = Convert::formatNameWithoutSpace($clientData->getName());
        }

        $template = $prefix . '/' . $tpl;
        $data['day'] = date('d/m/Y');
        return $this->view->render($template, $data);
    }

    public function display()
    {
        echo get_class($this);
        echo '<br/>';
    }

    public function removeSessionVars(Session $session)
    {
        $session->remove(self::KEY_CLIENT);

        $session->clear();
    }

    public function __destruct()
    {
        if ($this->service->getEm()->getConnection()->isConnected()) {
            $this->service->getEm()->getConnection()->close();
        }
    }

}
