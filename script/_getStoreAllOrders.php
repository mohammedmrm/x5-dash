<?php
session_start();
header('Content-Type: application/json');
error_reporting(0);
require_once("_access.php");
access([1,2,3,5,6,7,8]);
require_once("dbconnection.php");
require_once("../config.php");
$id = $_REQUEST['store'];
$data = [];
$success =0;
$sql = "select orders.*,date_format(orders.date,'%Y-%m-%d') as dat,  order_status.status as status_name,
          cites.name as city_name,
          towns.name as town_name,
            if(to_city = 1,
                 if(client_dev_price.price is null,(".$config['dev_b']." - discount),(client_dev_price.price - discount)),
                 if(client_dev_price.price is null,(".$config['dev_o']." - discount),(client_dev_price.price - discount))
            )
            + if(new_price > 500000 ,( (ceil(new_price/500000)-1) * ".$config['addOnOver500']." ),0)
            + if(weight > 1 ,( (weight-1) * ".$config['weightPrice']." ),0)
            + if(towns.center = 0 ,".$config['countrysidePrice'].",0)
            as dev_price,
            new_price -
              (if(to_city = 1,
                  if(client_dev_price.price is null,(".$config['dev_b']." - discount),(client_dev_price.price - discount)),
                  if(client_dev_price.price is null,(".$config['dev_o']." - discount),(client_dev_price.price - discount))
                 )
             ) as client_price
          from orders
          inner join order_status on orders.order_status_id = order_status.id
          inner join cites on orders.to_city = cites.id
          inner join towns on orders.to_town = towns.id
          left JOIN client_dev_price on client_dev_price.client_id = orders.client_id AND client_dev_price.city_id = orders.to_city
          where store_id = ? limit 500
          ";
$res3= getData($con,$sql,[$id]);
$data= $res3;

echo json_encode(array("success"=>$success,"data"=>$data));
?>