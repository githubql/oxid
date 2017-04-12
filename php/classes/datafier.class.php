<?php
/**
 * Created by PhpStorm.
 * User: mareike
 * Date: 12.04.2017
 * Time: 15:45
 */
class datafier
{

    private static $instance = null;
    public static function getInstance()
    {
        if(null==self::$instance)
        {
            self::$instance = new datafier();
        }
        return self::$instance;
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
    public function getDataToLogDatabase($data)
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
    public function getDataToLogEcho($data)
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
}