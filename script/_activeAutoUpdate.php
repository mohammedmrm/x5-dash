<?php
session_start();
//error_reporting(0);
header('Content-Type: application/json');
require_once("_access.php");
access([1]);
$id= $_REQUEST['id'];
$success = 0;
$msg="";
require_once("dbconnection.php");
use Violin\Violin;
require_once('../validator/autoload.php');
$v = new Violin;

$v->validate([
    'id'    => [$id,'required|int']
    ]);

if($v->passes()){
         $sql = "update auto_update set active=1 where id = ?";
         $res = setData($con,$sql,[$id]);
         if($res){
           $success =1;
         }
}else{
  $msg = "فشل التأكيد";
  $success = 0;
}
echo json_encode([$sql,$id,'success'=>$success, 'msg'=>$msg]);
?>