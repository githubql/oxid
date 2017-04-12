<?php
/**
 * Created by PhpStorm.
 * User: mareike
 * Date: 12.04.2017
 * Time: 08:25
 */
class logEcho implements log
{
    private static $instance = null;

    //protected function __construct(){}
    private function __clone(){}

    public static function getInstance()
    {
        if(null==self::$instance)
        {
            self::$instance=new logEcho();
        }
        return self::$instance;
    }

    public function log($message)
    {
        echo $message."\n";
    }
}