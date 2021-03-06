<?php
session_start();
error_reporting(0);
header('Content-Type: application/json');
require_once("_access.php");
access([1,2,5,7,8,9]);
require_once("dbconnection.php");
require_once("../config.php");

$branch = $_REQUEST['branch'];
$to_branch = $_REQUEST['to_branch'];
$city = $_REQUEST['city'];
$town= $_REQUEST['town'];
$customer = $_REQUEST['customer'];
$order = $_REQUEST['order_no'];
$store= $_REQUEST['store'];
$client= $_REQUEST['client'];
$invoice= $_REQUEST['invoice'];
$status = $_REQUEST['orderStatus'];
$storageStatus = $_REQUEST['storageStatus'];
$callcenter = $_REQUEST['callcenter'];
$storage = $_REQUEST['storage'];
$storage_invoice = $_REQUEST['storage_invoice'];
$driver = $_REQUEST['driver'];
$repated = $_REQUEST['repated'];
$confirm = $_REQUEST['confirm'];
$start = trim($_REQUEST['start']);
$end = trim($_REQUEST['end']);
$limit = trim($_REQUEST['limit']);
if(empty($limit)){
  $limit = 10;
}
$sort ="";
$page = trim($_REQUEST['p']);
if(empty($page) || $page <=0){
  $page =1;
}
$total = [];
$money_status = trim($_REQUEST['money_status']);
if(!empty($end)) {
   $end .=" 23:59:59";
}else{
   $end =date('Y-m-d', strtotime(' + 1 day'));
   $end .=" 23:59:59";
}
if(!empty($start)) {
   $start .=" 00:00:00";
}

try{
  $count = "select count(*) as count from orders
            left join invoice on invoice.id = orders.invoice_id
            left join storage_invoice on storage_invoice.id = orders.storage_invoice_id
            left join (
             select order_no,count(*) as rep from orders
              GROUP BY order_no
              HAVING COUNT(orders.id) > 1
            ) b on b.order_no = orders.order_no";

  $query = "select orders.*, date_format(orders.date,'%Y-%m-%d') as date,
            clients.name as client_name,clients.phone as client_phone,storage.name as storage_name,
            stores.name as store_name,a.nuseen_msg,callcenter.name as callcenter_name,
            if(order_status_id <> 4 ,if(orders.storage_id =0,'عند المندوب',if(orders.storage_id =-1,'عند العميل',storage.name)),'') as storage_status,
            cites.name as city,towns.name as town,branches.name as branch_name,to_branch.name as to_branch_name,
            order_status.status as status_name,staff.name as staff_name,b.rep as repated , driver.name as driver_name,
            orders.invoice_id as invoice_id,invoice.path as invoice_path,invoice.invoice_status as invoice_status,
            storage_invoice.path as storage_invoice_path
            from orders left join
            clients on clients.id = orders.client_id
            left join cites on  cites.id = orders.to_city
            left join stores on  orders.store_id = stores.id
            left join towns on  towns.id = orders.to_town
            left join branches on  branches.id = orders.from_branch
            left join branches as to_branch on  to_branch.id = orders.to_branch
            left join staff on  staff.id = orders.manager_id
            left join staff as driver on  driver.id = orders.driver_id
            left join staff as callcenter on  callcenter.id = orders.callcenter_id
            left join order_status on  order_status.id = orders.order_status_id
            left join storage on  storage.id = orders.storage_id
            left join invoice on invoice.id = orders.invoice_id
            left join storage_invoice on storage_invoice.id = orders.storage_invoice_id
            left JOIN client_dev_price on client_dev_price.client_id = orders.client_id AND client_dev_price.city_id = orders.to_city
            left join (
             select count(*) as nuseen_msg, max(order_id) as order_id from message
             where is_client = 0 and admin_seen = 0
             group by message.order_id
            ) a on a.order_id = orders.id

            left join (
             select order_no,count(*) as rep from orders  where confirm = 1 or  confirm = 4
              GROUP BY order_no
              HAVING COUNT(orders.id) > 1
            ) b on b.order_no = orders.order_no

            ";

    $where = "where (order_status_id=6 or order_status_id=9 or order_status_id=5) and ";
    if(($_SESSION['role'] == 1 || $_SESSION['role'] == 5 || $_SESSION['role'] == 8) &&  $_SESSION['user_details']['branch_id'] == 1){
      $filter .= "";
    }else{
       $filter .= " and (from_branch = '".$_SESSION['user_details']['branch_id']."' or to_branch = '".$_SESSION['user_details']['branch_id']."') ";
    }
   if($confirm == 1 || $confirm == 4){
    $filter .= " and orders.confirm ='".$confirm."'";
   }else{
    $filter .= " and (orders.confirm =1 or orders.confirm =4)";
   }
  if($branch >= 1){
   $filter .= " and from_branch =".$branch;
  }
  if($storage_invoice == 1){
   $filter .= " and orders.storage_invoice_id = 0";
  }else if($storage_invoice == 2){
   $filter .= " and orders.storage_invoice_id <> 0";
  }
  if($to_branch >= 1){
   $filter .= " and to_branch =".$to_branch;
  }
  if($driver >= 1){
   $filter .= " and orders.driver_id =".$driver;
  }

  if($storageStatus == 1){
   $filter .= " and orders.invoice_id = 0";
  }else if($storageStatus == 2){
   $filter .= " and orders.invoice_id <> 0 and invoice.invoice_status = 0";
  }else if($storageStatus == 3){
   $filter .= " and orders.invoice_id <> 0 and invoice.invoice_status = 1";
  }

  if($callcenter == 1){
   $filter .= " and orders.callcenter_id <> 0";
  }else if($callcenter == 2){
    $filter .= " and orders.callcenter_id = 0";
  }


  if(empty($storage)){
   $filter .= " and orders.storage_id = 0 ";
  }else if($storage > 0){
   $filter .= " and orders.storage_id =".$storage." ";
  }
  $sort = " order by orders.date DESC ";
  if($repated == 1){
   $filter .= " and b.rep >= 2";
   $sort = " order by orders.order_no DESC ";
  }else if($repated == 2){
   $filter .= " and b.rep < 2";

  }
  if($city >= 1){
    $filter .= " and to_city=".$city;
  }
  if($town >= 1){
    $filter .= " and to_town=".$town;
  }
  if(($money_status == 1 || $money_status == 0) && $money_status !=""){
    $filter .= " and money_status='".$money_status."'";
  }
  if($store >= 1){
    $filter .= " and orders.store_id=".$store;
  }

  if($client >= 1){
    $filter .= " and orders.client_id=".$client;
  }

  if($invoice == 1){
    $filter .= " and  (
                       ((order_status_id<>6 and order_status_id<>5) and orders.invoice_id = 0) or
                       ((order_status_id=6 or order_status_id=5) and (orders.invoice_id2=0))
                      )";
  }else if($invoice == 2){
    $filter .= " and  (
                       (orders.invoice_id <> 0) or
                       ((order_status_id=6 or order_status_id=5) and (orders.invoice_id2 <> 0))
                      )";
  }
  if(!empty($customer)){
    $filter .= " and (customer_name like '%".$customer."%' or
                      customer_phone like '%".$customer."%')";
  }
  if(!empty($order)){
    $filter .= " and orders.order_no = '".$order."'";
  }
  ///-----------------status
  $s = "";
  if(count($status) > 0){
    foreach($status as $stat){
      if($stat > 0){
        $s .= " or orders.order_status_id=".$stat;
      }
    }
  }
  $s = preg_replace('/^ or/', '', $s);
   if($s != ""){
    $s = " and (".$s." )";
    $filter .= $s;
  }
  //---------------------end of status
  function validateDate($date, $format = 'Y-m-d H:i:s')
    {
        $d = DateTime::createFromFormat($format, $date);
        return $d && $d->format($format) == $date;
    }
  if(validateDate($start) && validateDate($end)){
      $filter .= " and orders.date between '".$start."' AND '".$end."'";
     }
  if($filter != ""){
    $filter = preg_replace('/^ and/', '', $filter);
    $filter = $where." ".$filter;
    $count .= " ".$filter;
    $query .= " ".$filter;
  }

  $count = getData($con,$count);
  $orders = $count[0]['count'];
  $pages= ceil($count[0]['count'] / $limit);
  $lim = " limit ".(($page-1) * $limit).",".$limit;

  $query .= $sort.$lim;
  $data = getData($con,$query);
  $success="1";
} catch(PDOException $ex) {
   $data=["error"=>$ex];
   $success="0";
}
try{

 $sqlt = "select
          sum(new_price) as income,

          sum(
                 if(order_status_id = 9,
                     0,
                     if(to_city = 1,
                           if(order_status_id=9,0,if(client_dev_price.price is null,(".$config['dev_b']." - discount),(client_dev_price.price - discount))),
                           if(order_status_id=9,0,if(client_dev_price.price is null,(".$config['dev_o']." - discount),(client_dev_price.price - discount)))
                      )
                  )
          ) as dev,

          sum(new_price -
              (
                 if(order_status_id = 9,
                     0,
                     if(to_city = 1,
                           if(order_status_id=9,0,if(client_dev_price.price is null,(".$config['dev_b']." - discount),(client_dev_price.price - discount))),
                           if(order_status_id=9,0,if(client_dev_price.price is null,(".$config['dev_o']." - discount),(client_dev_price.price - discount)))
                      )
                  )
              )
          ) as client_price,
          sum(discount) as discount,
          count(orders.order_no) as orders
          from orders
          left join storage_invoice on storage_invoice.id = orders.storage_invoice_id
          left JOIN client_dev_price on client_dev_price.client_id = orders.client_id AND client_dev_price.city_id = orders.to_city
          left join invoice on invoice.id = orders.invoice_id
          left join (
             select order_no,count(*) as rep from orders where confirm = 1 or  confirm = 4
              GROUP BY order_no
              HAVING COUNT(orders.id) > 1
            ) b on b.order_no = orders.order_no
          ";

if($filter != ""){
    $filter = preg_replace('/^ and/', '', $filter);
    $sqlt .= " ".$filter;
}
$total = getData($con,$sqlt);
$total[0]['orders'] = $orders;
if($store >=1){
 $total[0]['store'] = $data[0]['store_name'];
}else{
 $total[0]['store'] = '<span class="text-danger">لم يتم تحديد صفحة</span>';
}
  $success="1";
} catch(PDOException $ex) {
   $total=["error"=>$ex];
   $success="0";
}
echo json_encode(array($query,"success"=>$success,"data"=>$data,'total'=>$total,"pages"=>$pages,"page"=>$page));
?>