<?php

namespace App\Service;

use Doctrine\ORM\EntityManager;
use \Symfony\Component\HttpFoundation\FileBag;

/**
 *
 * Class UserService
 * @package App\Service
 */
class UserService extends BaseService
{

    /**
     * @var UserService
     */

    private $userService;

    private $cont = 0;

    public function __construct(EntityManager $em)
    {
        parent::__construct($em);
    }

    public function createUser($values)
    {
        try {
            $sql = "INSERT INTO users (name, email, active) VALUES (:name, :email, :active)";

            $stm = $this->em->getConnection()->prepare($sql);

            $stm->bindValue(':name', $values['name']);
            $stm->bindValue(':email', $values['email']);
            $stm->bindValue(':active', $values['active']);
			
            $returnStm = $stm->execute();
			
            return $returnStm ? true : false;
        } catch (\Exception $e) {
			var_dump($e->getMessage());
            die('ERROR');
            return false;
        }
    }

    public function getUsers() {
        $sql = "SELECT users.id, name, email, active, last_login FROM atom.users INNER JOIN atom.users_access ON atom.users.id = atom.users_access.user_id ORDER BY id DESC LIMIT 500;";

        $stm = $this->em->getConnection()->prepare($sql);

        $stm->execute();

        $allUsers = $stm->fetchAll();
        
        return $allUsers;
    }

    public function populateUsers() {
        $names = $this->getNames();

        for ($i = 0; $i < 500; $i++) { 
            try {
                $positionName = rand(1, 50);

                $name = $names[$positionName];

                $sql = "INSERT INTO users (name, email, active) VALUES (:name, :email, :active)";
    
                $stm = $this->em->getConnection()->prepare($sql);
    
                $stm->bindValue(':name', $name);
                $stm->bindValue(':email', $name . '@gmail.com');
                $stm->bindValue(':active', 1);
                
                $returnStm = $stm->execute();
            } catch (\Exception $e) {
                var_dump($e->getMessage());
                die('ERROR');
                return false;
            }
        }

        return $returnStm;
    }

    public function populateLogins() {
        for ($i = 0; $i < 5000; $i++) { 
            try {
                $randUserId = rand(1, 500);

                $sql = "INSERT INTO users_access (last_login, user_id) VALUES (:last_login, :user_id)";
    
                $stm = $this->em->getConnection()->prepare($sql);
    
                $stm->bindValue(':last_login', date("Y-m-d H:i:s"));
                $stm->bindValue(':user_id', $randUserId);
                
                $returnStm = $stm->execute();
            } catch (\Exception $e) {
                var_dump($e->getMessage());
                die('ERROR');
                return false;
            }
        }

        return $returnStm;
    }

    public function searchUser($user) {
        try {
            $sql = "SELECT users.id, name, email, active, last_login FROM atom.users INNER JOIN atom.users_access ON atom.users.id = atom.users_access.user_id WHERE name LIKE '%" . $user . "%'" . " LIMIT 500;";

            $stm = $this->em->getConnection()->prepare($sql);

            $stm->execute();

            $allUsers = $stm->fetchAll();
            
            return $allUsers;
        }catch (\Exception $e) {
            var_dump($e->getMessage(), $user);
            die('ERROR');
            return false;
        }
    }

    public function getNames() {
        $names = [
            1 => 'Miguel',
            2 => 'Alice',
            3 => 'Arthur',
            4 => 'Bruna',
            5 => 'Laura',
            6 => 'Heitor',
            7 => 'Maria Luiza',
            8 => 'Lucas',
            9 => 'Antonio',
            10 => 'Euzebio',
            11 => 'Lurdes',
            12 => 'Thiago',
            13 => 'Ana',
            14 => 'Rafael',
            15 => 'Andreia',
            16 => 'Adir',
            17 => 'Andressa',
            18 => 'Matheus',
            19 => 'Rosana',
            20 => 'Leandro',
            21 => 'Giovanna',
            22 => 'Heloisa',
            23 => 'Nicolas',
            24 => 'Mariana',
            25 => 'Joaquim',
            26 => 'Isadora',
            27 => 'Rafaela',
            28 => 'Guilherme',
            29 => 'Ana Júlia',
            30 => 'Daniel',
            31 => 'Nicole',
            32 => 'Caio',
            33 => 'Vinicius',
            34 => 'Alícia',
            35 => 'Emanuel',
            36 => 'Agatha',
            37 => 'Calebe',
            38 => 'Larissa',
            39 => 'Otavio',
            40 => 'Bianca',
            41 => 'Bruno',
            42 => 'Isabella',
            43 => 'Raul',
            44 => 'Eduarda',
            45 => 'Ronaldo',
            46 => 'Camilla',
            47 => 'Adriano',
            48 => 'Valquiria',
            49 => 'Igor',
            50 => 'Luana',
        ];

        return $names;
    }
}
