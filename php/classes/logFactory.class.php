<?php
/**
 * Created by PhpStorm.
 * User: mareike
 * Date: 12.04.2017
 * Time: 08:25
 */
class logFactory
{
    static public function createLogger ($type='echo',$destination='')
    {
        switch ($type)
        {
            case 'echo':
                $logger = new logEcho();
                break;
            case 'filejson':
            case 'filecsv':
                $logger = new logFile($destination);
                //echo $destination.'<br />';
                break;
            case 'database':
                $logger = new logDatabase($destination);
                break;
            default :
                throw new Exception('logFactory:: noLoggerFound');
        }
        return $logger;
    }
}