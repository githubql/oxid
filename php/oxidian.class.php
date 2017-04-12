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
     * methgod to get average
     * @param $data
     * @return int
     */
    public function getValue($data,$field)
    {
        $return=0;
        if(isset($data->$field))$return=$data->$field;
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
        elseif('echo'==$mode) return;
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
        return $this->$strMethodName($data);
    }

    /**
     * method to get json string for logging
     * @param $data
     * @return string
     */
    public function getDataToLogFilejson($data)
    {
        return json_encode($data);
    }

    /**
     * method to get csv compatible data = array
     * @param $data
     * @return array
     */
    public function getDataToLogFilecsv($data)
    {
        $csv=array();
        $csv[]=$data->date;
        $csv[]=$data->average;
        $csv[]=json_encode($data->data);
        //print_r($csv);die;
        return $csv;
    }

    /**
     * method to get database query string for inserting into database
     * @param $data
     * @return array
     */
    private function getDataToLogDatabase($data)
    {
        $queries=array();
        $queries[]=$this->getDataToLogDatabaseL1($data->average,$data->date);
        $queries[]=$this->getDataToLogDatabaseL2($data->data,'#INSERTID#');
        return $queries;
    }
    /**
     * method to insert data into l1
     * @param float $average
     * @return string
     */
    public function getDataToLogDatabaseL1($average,$date)
    {
        $query='INSERT INTO `l1` (`date`, `average`) VALUES (\''.$date.'\','.$average.')';
        return $query;
    }

    /**
     * method to get query 1 inserting data to l2
     * @param $data
     * @param $id
     * @return string
     */
    public function getDataToLogDatabaseL2($data,$id)
    {
        $query='INSERT INTO `l2` (`l1_id`, `articleNr`,`price`) VALUES';
        $arrValues=array();
        foreach($data as $k=>$v)
        {
            $arrValues[]='('.$id.','.$v['OXARTNUM'].','.$v['OXPRICE'].')';
        }
        $values =implode(',',$arrValues);
        $query.=$values;
        return $query;
    }

    /**
     * method to get data for echo = string
     * @param $data
     * @return string
     */
    private function getDataToLogEcho($data)
    {
        $html=array();
        $html[]='===================';
        $html[]='Date: '.$data->date;
        $html[]='===================';
        $html[]='Average: '.$data->average;
        //echo '<pre>';print_r($data);die;
        while(list($k,$v)=each($data->data))
        //foreach($data as $k=>$v)
        {
            $html[]='#'.($k+1).': '.$v['OXARTNUM'].' | '.$v['OXTITLE'].' | '.$v['OXPRICE'];
        }
        $html[]='';
        reset($data->data);
        return implode('<br />',$html);
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

    public function createTablesL1L2($create)
    {
        if(true==$create)
        {
            $query=$this->getQueryCreateTables();
            $this->setQuery($query);
        }
    }

}