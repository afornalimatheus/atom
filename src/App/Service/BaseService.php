<?php

namespace App\Service;

use App\Entities\Client as ClientEntity;
use Doctrine\ORM\EntityManager;
use GuzzleHttp\Client;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;

class BaseService
{
    /**
     * @var EntityManager
     */
    protected $em;

    /**
     * BaseService constructor.
     * @param EntityManager $em
     */

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    /**
     * @return EntityManager
     */
    public function getEm()
    {
        return $this->em;
    }
    public function setClientData(ClientEntity $client)
    {
        return $this->client = $client;
    }
    /**
     * @return string
     */
    public function getLastId()
    {
        return $this->em->getConnection()->lastInsertId();
    }

    public function setHeadersEmail()
    {
        $this->headersCallApiEmail['Content-Type'] = 'application/json';
    }

    /**
     * @param $sql
     * @param string $get
     * @return array|bool|mixed
     * @throws \Doctrine\DBAL\DBALException
     */
    public function executeSql($sql, $get = 'unique')
    {
        $stmt = $this->em->getConnection()->prepare($sql);
        try {
            $stmt->execute();
        } catch (\Exception $e) {
            $this->logError(__class__, __METHOD__, $e->getMessage());
        }

        if ($get == 'all') {
            return $stmt->fetchAll();
        }

        if ($get == null) {
            return true;
        }

        return $stmt->fetch();
    }

    /**
     * @param $table
     * @param $data
     * @return bool
     */
    public function insert($table, $data)
    {
        $status = true;
        try {
            $this->em->getConnection()->insert($table, $data);
        } catch (\Exception $e) {
            $this->logError(__class__, __METHOD__, $e->getMessage());
            $status = false;
        }
        return $status;
    }

    /**
     * @param $table
     * @param $data
     * @param $cond
     * @return bool
     */
    public function updateData($table, $data, $cond)
    {
        $status = true;
        try {
            $this->em->getConnection()->update($table, $data, $cond);
        } catch (\Exception $e) {
            $this->logError(__class__, __METHOD__, $e->getMessage());
            $status = false;
        }
        return $status;
    }
}
