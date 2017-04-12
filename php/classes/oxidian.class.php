<?php
/**
 * Created by PhpStorm.
 * User: mareike
 * Date: 11.04.2017
 * Time: 15:00
 */

class oxidian
{
    function __construct(){}

    /**
     * method to initiate connection to database
     * @param $host
     * @param $database
     * @param $username
     * @param $password
     */
    public function initiateDatabase($host,$database,$username,$password)
    {
        $this->db = new database($host,$database,$username,$password);
    }

    /**
     * method to get select statement
     * @param $array
     * @param string $tableShorty
     * @return string
     */
    public function getSelect($array,$tableShorty='')
    {
        if(''!=$tableShorty)$tableShorty.='.';
        while (list($k,$v) = each($array))
        {
            $array[$k]=$tableShorty.'`'.$v.'`';
        }
        return implode(',',$array);
    }

    /**
     * method to set query
     * @param $str
     * @return mixed
     */
    public function setQuery($str)
    {
        return $this->db->connection->query($str);
    }

    /**
     * method to get query based on name of category
     * @param string $category
     * @param bool $average
     * @return string
     */
    public function getQueryCategory($category,$average=false)
    {
        $t1=array('oxarticles','art');
        $t2=array('oxobject2category','art2cat');
        $t3=array('oxcategories','cat');
        $select=$this->getSelect(array('OXARTNUM','OXTITLE','OXSHORTDESC','OXPRICE'),$t1[1]);
        if(true==$average)$select.=', AVG(art.OXPRICE) AS average';
        $from='`'.$t1[0].'` AS '.$t1[1].',`'.$t2[0].'` AS '.$t2[1].',`'.$t3[0].'` AS '.$t3[1];
        $where=$t3[1].'.`OXTITLE`=\''.$category.'\' AND '.$t3[1].'.`OXID`='.$t2[1].'.`OXCATNID` AND '.$t2[1].'.`OXOBJECTID`='.$t1[1].'.`OXID`';
        $where.=' AND '.$t1[1].'.`OXACTIVE`=\'1\'';
        $query='SELECT '.$select.' FROM '.$from.' WHERE '.$where.' ';
        return $query;
    }

    /**
     * method to get gvalue from $dara array
     * @param $data
     * @return int
     */
    public function getValue($data,$field)
    {
        $return=0;
        if(isset($data[$field]))$return=$data[$field];
        return $return;
    }

    /**
     * method to get last insert id
     * @return mixed
     */
    public function getLastInsertId()
    {
        return $this->db->getLastInsertId();
    }

    /**
     * method to get destination of data according to type of logging
     * @param $mode - logg mode
     * @return mixed - destination
     */
    public function getDestination($mode)
    {
        if('database'==$mode) return $this->db;
        elseif('filejson'==$mode) return $this->filejson;
        elseif('filecsv'==$mode) return $this->filecsv;
        //elseif('echo'==$mode) return;
    }

    /**
     * method to get destination of data according to type of logging
     * reigning submethods
     * @param $data
     * @param string $mode
     * @return mixed
     */
    public function getDataToLog($data,$mode='echo')
    {
        $strMethodName='getDataToLog'.ucwords($mode);
        $datafier= datafier::getInstance();
        return $datafier->$strMethodName($data);
    }

    /**
     * method to set class variables
     * @param $varName
     * @param $value
     */
    public function set($varName,$value)
    {
        $this->$varName=$value;
    }

    /**
     * method to create tables l1 and l2 in case they don't exist; to be activated manually
     * @param $create
     */
    public function createTablesL1L2($create)
    {
        if(true==$create)
        {
            $query=$this->getQueryCreateTables();
            $this->setQuery($query);
        }
    }

    /**
     * method to log data into storage according to log_mode
     * @param object $data
     * @param string $log_mode echo|file|database
     */
    public function loggData($data,$log_mode)
    {
        $destination=$this->getDestination($log_mode);
        $obj_logger=logFactory::createLogger($log_mode,$destination);
        $message=$this->getDataToLog($data,$log_mode);
        if('database'!=$log_mode)
        {
            $obj_logger->log($message);
            return;
        }
        $obj_logger->log($message[0]);
        $insertId=$obj_logger->getLastInsertId();
        $query=str_replace('#INSERTID#',$insertId,$message[1]);
        $obj_logger->log($query);
    }

    /**
     * function to get query for generatin special tables for datainserts
     * @return string
     */
    function getQueryCreateTables()
    {
        $query="CREATE TABLE IF NOT EXISTS `l1` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
          `date` datetime NOT NULL,
          `average` float NOT NULL,
          PRIMARY KEY (`id`)
        ) ENGINE=InnoDB  DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci AUTO_INCREMENT=25 ;

        CREATE TABLE IF NOT EXISTS `l2` (
            `l1_id` int(11) NOT NULL,
          `articleNr` varchar(50) COLLATE latin1_general_ci NOT NULL,
          `price` float NOT NULL,
          PRIMARY KEY (`l1_id`,`articleNr`)
        ) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;";
        return $query;
    }
}