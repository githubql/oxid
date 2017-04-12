<?php
/**
 * Created by PhpStorm.
 * User: mareike
 * Date: 12.04.2017
 * Time: 08:25
 */
class logDatabase implements log
{
    protected $db= null;
    private static $instance = null;

    function __construct($db)
    {
        $this->db=$db;
    }
    private function __clone(){}

    public static function getInstance($db)
    {
        if(null==self::$instance)
        {
            self::$instance[$db]=new logDatabase($db);
        }
        return self::$instance[$db];
    }

    public function log($data)
    {
        $this->db->connection->query($data);
    }
    public function getLastInsertId()
    {
        return $this->db->getLastInsertId();
    }
}