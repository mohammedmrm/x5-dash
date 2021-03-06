<?php
ini_set('max_execution_time', 600);
ob_start();
session_start();
error_reporting(0);
header('Content-Type: application/json');
require_once("_access.php");
access([1,5,8,2]);
require_once("dbconnection.php");
$style='
<style>

  td,th{
    padding:3px;
    text-align:center;
  }
  .re {
    background-color: #FFA07A;
  }
  .ch {
    background-color: #FFFACD;
  }
  .repated {
    background-color:#E0FFFF;
  }
  .head-tr {
   background-color:#DF5B5B;
   color:#111;
  }
  .row_bg {
    background-color: #FBD5DC;
  }
</style>';
require("../config.php");

$branch = $_REQUEST['branch'];
$to_branch = $_REQUEST['to_branch'];
$city = $_REQUEST['city'];
$customer = $_REQUEST['customer'];
$order = $_REQUEST['order_no'];
$client= $_REQUEST['client'];
$statues = $_REQUEST['orderStatus'];
$store = $_REQUEST['store'];
$storage = $_REQUEST['storage'];
$driver = $_REQUEST['driver'];
$start = trim($_REQUEST['start']);
$end = trim($_REQUEST['end']);
$total = [];
$msg = "";
if(!empty($end)) {
   $end .=" 23:59:59";
}else{
   $end =date('Y-m-d', strtotime(' + 1 day'));
   $end .=" 23:59:59";
}
if(!empty($start)) {
   $start .=" 00:00:00";
}
if($storage == $_SESSION['user_details']['storage_id'] || $_SESSION['role'] == 1){
try{
  $count = "select count(*) as count,
               SUM(IF (to_city = 1,1,0)) as  b_orders,
               SUM(IF (to_city > 1,1,0)) as  o_orders
            from orders ";
  $query = "select orders.*, date_format(orders.date,'%Y-%m-%d') as dat,
            clients.name as client_name,clients.phone as client_phone,
            cites.name as city,towns.name as town,branches.name as branch_name,
            stores.name as store_name
            from orders
            left join clients on clients.id = orders.client_id
            left join cites on  cites.id = orders.to_city
            left join stores on  stores.id = orders.store_id
            left join towns on  towns.id = orders.to_town
            left join branches on  branches.id = orders.to_branch
            ";
  $where = "where orders.confirm=1 and (
                 ((order_status_id<>6 and order_status_id<>5) and orders.invoice_id = 0) or
                 ((order_status_id=6 or order_status_id=5) and (orders.invoice_id2=0))
                ) and storage_id='".$storage."'";
  $filter = "";
  if($client >= 1){
    $filter .= " and client_id=".$client;
  }
  if($store >= 1){
    $filter .= " and store_id=".$store;
   }
     ///-----------------status
  $s = "";
  if(count($status) > 0){
    foreach($status as $stat){
      if($stat == 9 || $stat == 6 || $stat == 5){
        $s .= " or order_status_id=".$stat;
      }
    }
  }else{
    $filter .= " and (order_status_id = 6 or order_status_id = 9 or order_status_id = 5)";
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
     $filter = $where." ".$filter;
     $count .= " ".$filter;
     $query .= " ".$filter." order by orders.order_no,to_city";
  }else{
    $query .=" order by orders.date";
  }

  $count1 = getData($con,$count);
  $orders = $count1[0]['count'];
  $total['b_orders'] = $count1[0]['b_orders'];
  $total['o_orders'] = $count1[0]['o_orders'];
  $data = getData($con,$query);
  $success="1";
} catch(PDOException $ex) {
   $data=["error"=>$ex];
   $success="0";
}
}else{
 $success = 3;
 $msg = "لاتملك صلاحيا انشاء كشف للمخزن المحدد";
}
// set default header data
$status = 9;
$status_name = "كشف راجع";



if($orders > 0){
    try{
        $i = 0;
        $content = "";
        $success = 0;
        $pdf_name = date('Y-m-d')."_".uniqid().".pdf";
        $sql = "insert into invoice (path,store_id,orders_status,staff_id) values(?,?,?,?)";
        $res = setData($con,$sql,[$pdf_name,$store,$status,$_SESSION['userid']]);
    if($res > 0){
      $success = 1;
      $sql = "select *,date_format(date,'%Y-%m-%d') as date from invoice where path=? and store_id =? order by date DESC limit 1";
      $res = getData($con,$sql,[$pdf_name,$store]);
      $invoice = $res[0]['id'];
      $date = $res[0]['date'];

        foreach($data as $k=>$v){
          $total['income'] += $data[$i]['new_price'];
                $sql = "select * from client_dev_price where client_id=? and city_id=?";
                $dev_price  = getData($con,$sql,[$v['client_id'],$v['to_city']]);
                if(count($dev_price) > 0){
                  $dev_p = $dev_price[0]['price'];
                }else{
                  if($v['to_city'] == 1){
                   $dev_p = $config['dev_b'];
                  }else{
                   $dev_p = $config['dev_o'];
                  }
                }
                $data[$i]['dev_price'] = $dev_p;
                if($data[$i]['order_status_id'] == 9){
                  $data[$i]['dev_price'] = 0;
                  $dev_p = 0;
                  $data[$i]['dicount']=0;
                  $data[$i]['new_price']=0;
                  $data[$i]['client_price']=0;
                }
                $data[$i]['client_price'] = ($data[$i]['new_price'] -  $dev_p) + $data[$i]['discount'];
               $note =  $data[$i]['note'];
               $bg = "";
               if($data[$i]['order_status_id'] == 6){
                 $bg = "re";
                 $note = "راجع جزئي";
               }
               if($data[$i]['order_status_id'] == 5){
                 $bg = "ch";
                 $note = "استبدال";
               }
               if($data[$i]['repated'] > 1){
                 $bg = "repated";
               }
               $row_bg = "";
               if(($i%2) == 1 && $data[$i]['order_status_id'] != 6 && $data[$i]['order_status_id'] != 5 && $data[$i]['repated'] <= 1){
                  $row_bg = "row_bg";
               }
              if(($data[$i]['order_status_id'] == 6 || $data[$i]['order_status_id'] == 5)){
                 $sql = "update orders set invoice_id2 =? where id=?";
                 $res = setData($con,$sql,[$invoice,$v['id']]);
                 $data[$i]['client_price'] = 0;
              }else{
                $sql = "update orders set invoice_id =? where id=?";
                $res = setData($con,$sql,[$invoice,$v['id']]);
              }
        $hcontent .=
         '<tr class="'.$bg.'  '.$row_bg.'">
           <td width="60"  align="center">'.($i+1).'</td>
           <td width="100" align="center">'.$data[$i]['dat'].'</td>
           <td width="80"  align="center">'.$data[$i]['order_no'].'</td>
           <td width="120" align="center">'.phone_number_format($data[$i]['customer_phone']).'</td>
           <td width="160" align="center" >'.$data[$i]['city'].' - '.$data[$i]['town'].' - '.$data[$i]['address'].'</td>
           <td width="80" align="center">'.number_format($data[$i]['price']).'</td>
           <td width="80" align="center">'.number_format($data[$i]['new_price']).'</td>
           <td width="80" align="center">'.number_format($data[$i]['dev_price']).'</td>
           <td align="center">'.number_format($data[$i]['client_price']).'</td>
           <td align="center">'.$note.'</td>
         </tr>';
          $total['discount'] += $data[$i]['discount'];
          $total['dev_price'] += $data[$i]['dev_price'];
          $total['client_price'] += $data[$i]['client_price'];
          $i++;

       }
       $total['invoice'] = $invoice;
       $total['status'] = $status_name;
       $total['date'] = $date;
    }



          $total['orders'] = $orders;
          if($store >=1){
           $total['client'] = $data[0]['client_name'];
           $total['store'] = $data[0]['store_name'];
           $total['client_phone'] = $data[0]['client_phone'];
          }else{
           $total['client'] = 'غير معروف';
           $total['store'] = 'غير معروف';
          }

    } catch(PDOException $ex) {
       $data=["error"=>$ex];
       $success="0";
    }

require_once("../tcpdf/tcpdf.php");
class MYPDF extends TCPDF {
    public function Header() {
        $t = $GLOBALS['total'];
        $this->writeHTML('');
    }
}
$pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// set document information
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('07822816693');
$pdf->SetTitle('كشف رواجع');
$pdf->SetSubject($start."-".$end);
// set some language dependent data:
$lg = Array();
$lg['a_meta_charset'] = 'UTF-8';
$lg['a_meta_dir'] = 'rtl';
$lg['a_meta_language'] = 'ar';
$lg['w_page'] = 'page';

// set some language-dependent strings (optional)
$pdf->setLanguageArray($lg);
// set font
$pdf->SetFont('aealarabiya', '', 12);


//$pdf->SetHeaderData("../../../".$config['Company_logo'],30, ' ??? ?????? ?? ?????: '.$total['store']."               "." ?????? ???????: ".date("Y-m-d",strtotime($start))." || ".date("Y-m-d",strtotime($end))," ???? ??????? : ".$status_name."\n"."????? ?????? ??????: ".number_format($total['client_price'])."                "."\n"."??? ????????: ".$total['orders']." ");

// set header and footer fonts
$pdf->setHeaderFont(Array('aealarabiya', '', 12));
$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));


// set margins
$pdf->SetMargins(5, 5, 10);
$pdf->SetHeaderMargin(5);
$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
// set auto page breaks
$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

// set image scale factor
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);



// ---------------------------------------------------------


// add a page
$pdf->AddPage('L', 'A4');

// Persian and English content
$header ='
             <table>
             <tr>
                    <td ></td>
                    <td width="300"></td>
                    <td style="text-align:left;" width="300" rowspan="5">
                      <img src="../img/logos/logo.png" height="100px"/>
                    </td>
             </tr>
             <tr>
                    <td style="text-align:right;"><span align="right" style="color:#DC143C;">كشف حساب العميل</span></td>
                    <td  width="350" rowspan="4">
                    '.
                      'عدد الطلبيات  الكلي: '.$total['orders'].'<br />'.
                      'عدد طلبيات دبي : '.$total['b_orders'].'<br />'.
                      'عدد طلبيات المحافظات : '.$total['o_orders'].
                    '</td>
             </tr>
             <tr>
                    <td style="text-align:right;">اسم الصفحه: '.$total['store'].' - '.$total['client_phone'].'</td>
             </tr>
             <tr>
                    <td style="text-align:right;">التاريخ:'.date('Y-m-d').'</td>
             </tr>
             <tr>
                    <td style="text-align:right;">رقم الكشف:'.$total['invoice'].'</td>
             </tr>
            </table>
        ';
$htmlpersian = '<table border="1" class="table" cellpadding="3">
			       <thead>
	  						<tr  class="head-tr">
                                        <th width="60">#</th>
                                        <th width="100">تاريخ الادخال</th>
										<th width="80">رقم الوصل</th>
										<th width="120">هاتف المستلم</th>
										<th width="160">عنوان المستلم</th>
                                        <th width="80">مبلغ الوصل</th>
										<th width="80">المبلغ المستلم</th>
										<th width="80">مبلغ التوصيل</th>
										<th> المبلغ الصافي للعميل </th>
										<th>الملاحظات</th>
							</tr>
      	            </thead>
                            <tbody id="ordersTable">'
                            .$hcontent.
                            '</tbody>
		</table>
        ';
$pdf->WriteHTML($style.$header.$htmlpersian, true, 0, true, 0);

// set LTR direction for english translation
$pdf->setRTL(false);

$pdf->SetFontSize(10);
// print newline
$pdf->Ln();
//Close and output PDF document
ob_end_clean();
//print_r($hcontent);

$pdf->Output(dirname(__FILE__).'/../invoice/'.$pdf_name, 'F');
}else{
  $success = 2;
  $msg = "لايوجد طلبيات";
}
echo json_encode(['msg'=>$msg,'num'=>$count,'success'=>$success,'invoice'=>$pdf_name]);
?>