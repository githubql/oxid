<?php
/**
 * Created by PhpStorm.
 * User: mareike
 * Date: 12.04.2017
 * Time: 08:25
 */
class logFile implements log
{
    protected $logfile = null;

    private static $instance = null;

    public function __construct($logfile)
    {
        $this->logfile = $logfile;
    }
    private function __clone(){}

    public static function getInstance($logfile)
    {
        if(null==self::$instance)
        {
            self::$instance[$logfile]=new logFile($logfile);
        }
        return self::$instance[$logfile];
    }

    public function log($data)
    {
        if(false!==strpos($this->logfile,'csv'))
        {
            $handle = fopen($this->logfile, 'a');
            fputcsv($handle,$data);
            fclose($handle);
        }
        else
        {
            error_log($data."\n",3,$this->logfile);
        }
    }
}