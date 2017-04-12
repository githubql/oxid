<?php
/**
 * Created by PhpStorm.
 * User: mareike
 * Date: 11.04.2017
 * Time: 14:53
 */

class database
{
    public $db;
    protected $query;

    /**
     * constructor to connect to database
     * @param $host
     * @param $database
     * @param $username
     * @param $password
     */
    function __construct($host,$database,$username,$password)
    {
        try
        {
            $this->connection = new PDO('mysql:host='.$host.';dbname='.$database.';', $username, $password);
            $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        }
        catch(PDOException $e)
        {
            echo 'Connection failed: ' . $e->getMessage();
        }
        return $this->connection;
    }

    /**
     * method to set query
     * @param $str
     */
    public function setQuery($str)
    {
        $this->query=$str;
    }

    /**
     * method to get protected query
     * @return mixed
     */
    public function getQuery()
    {
        return $this->query;
    }

    /**
     * method to get last id inserted
     * @return string
     */
    function getLastInsertId()
    {
        return $this->connection->lastInsertId();
    }


}