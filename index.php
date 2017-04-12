<?php
/**
 * Created by PhpStorm.
 * User: mareike
 * Date: 11.04.2017
 * Time: 14:52
 */

/*SET DEFAULTS START*/
$db_host='localhost';
$db_database='cmsopencms';
$db_user='cmsopencms';
$db_password='cmsopencms';
$category='Kites';
$logfilejson=__DIR__.'/logs/log.json';
$logfilecsv=__DIR__.'/logs/log.csv';
$createTables=false;
/*SET DEFAULTS STOP*/

/*load classes automatically*/
spl_autoload_register(function ($class) { include 'php/classes/' . $class . '.class.php';});
/*include helper class oxidian*/
include 'php/oxidian.class.php';
$obj_helper= new oxidian();

/*setting some defaults within helper*/
$obj_helper->initiateDatabase($db_host,$db_database,$db_user,$db_password);
$obj_helper->set('filejson',$logfilejson);
$obj_helper->set('filecsv',$logfilecsv);
$obj_helper->createTablesL1L2($createTables);

/*getting all articles being in defined cetagory*/
$query=$obj_helper->getQueryCategory($category);
$result=$obj_helper->setQuery($query);
$arrayObject=$result->fetchAll();

/*getting average price*/
$query=$obj_helper->getQueryCategory($category,true);
$result=$obj_helper->setQuery($query);
$average=$obj_helper->getValue($result,'average');

/*prepare data for storage*/
$data=new stdClass();
$data->date=date('Y-m-d H:i:s');
$data->average=$average;
$data->data=$arrayObject;

/*Displaying via echo*/
$log_mode='echo';
$destination=$obj_helper->getDestination($log_mode);
$obj_logger=logFactory::createLogger($log_mode,$destination);
$message=$obj_helper->getDataToLog($data,$log_mode);
$obj_logger->log($message);

/*Logging to database*/
$log_mode='database';
$destination=$obj_helper->getDestination($log_mode);
$obj_logger=logFactory::createLogger($log_mode,$destination);
$message=$obj_helper->getDataToLog($data,$log_mode);
$obj_logger->log($message[0]);
$insertId=$obj_logger->getLastInsertId();
$query=str_replace('#INSERTID#',$insertId,$message[1]);
$obj_logger->log($query);

/*Logging to json file*/
$log_mode='filejson';
$destination=$obj_helper->getDestination($log_mode);
$obj_logger=logFactory::createLogger($log_mode,$destination);
$message=$obj_helper->getDataToLog($data,$log_mode);
$obj_logger->log($message);

/*Logging to csv file*/
$log_mode='filecsv';
$destination=$obj_helper->getDestination($log_mode);
$obj_logger=logFactory::createLogger($log_mode,$destination);
$message=$obj_helper->getDataToLog($data,$log_mode);
$obj_logger->log($message,'csv');
