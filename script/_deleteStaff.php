<?php
session_start();
//error_reporting(0);
header('Content-Type: application/json');
require_once("_access.php");
access([1,2]);
$id= $_REQUEST['id'];
$success = 0;
$msg="";
require_once("dbconnection.php");

use Violin\Violin;
require_once('../validator/autoload.php');
$v = new Violin;

$v->validate([
    'branch_id'    => [$id,'required|int']
    ]);

if($v->passes()){

         $sql = "delete from staff where id = ? and developer = 0";
         $result = setData($con,$sql,[$id]);
         if($result > 0){
            $success = 1;
            $sql = "delete from driver_towns where driver_id = ?";
            setData($con,$sql,[$id]);
         }else{
            $msg = "فشل الحذف";
         }

}else{
  $msg = "فشل الحذف";
  $success = 0;
}
echo json_encode(['success'=>$success, 'msg'=>$msg]);
?>