<?php

namespace App\Controllers;

use App\Service\UserService;
use Silex\Api\ControllerProviderInterface;
use Silex\Application;
use \Symfony\Component\HttpFoundation\FileBag;
use Symfony\Component\HttpFoundation\JsonResponse;

class UserController extends BaseController implements ControllerProviderInterface
{

    /**
     * @var UserService
     */
    protected $service;

    /**
     * PayInfoController constructor.
     * @param UserService $service
     */
    public function __construct(UserService $service)
    {
        $this->service = $service;
    }

    /**
     * @param Application $app
     * @return mixed
     */
    public function connect(Application $app)
    {
        $this->controllers = $app['controllers_factory'];

        $this->get('/', 'index');
        $this->get('/users', 'getUsers');
        $this->post('/new-user', 'createUser');
        $this->post('/search-user', 'searchUser');
        $this->get('/populate-database-users', 'populateDatabaseUsers');
        $this->get('/populate-database-logins', 'populateDatabaseLogins');

        return $this->controllers;
    }

    public function index() {

        return $this->render('/base.twig', $data = []);
    }

    public function createUser() {

        $data = $this->getParameters();

        $userData = [
            'name' => $data['name'],
            'email' => $data['email'],
            'active' => $data['active']
        ];
        
        $returnSave = $this->service->createUser($userData);

        return new JsonResponse($returnSave);
    }

    public function getUsers() {

        $allUsers = $this->service->getUsers();

        $data = [
            'users' => $allUsers
        ];

        return $this->render('/users.twig', $data);
    }

    public function populateDatabaseUsers() {
        $returnPopulate = $this->service->populateUsers();

        return $returnPopulate;
    }

    public function populateDatabaseLogins() {
        $returnPopulate = $this->service->populateLogins();

        return $returnPopulate;
    }

    public function searchUser() {
        $data = $this->getParameters();

        $user = $data['user'];

        $resultSearch = $this->service->searchUser($user);

        $data = [ 
            'users' => $resultSearch
        ];

        return new JsonResponse($data);
    }
}
