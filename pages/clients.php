<?php
if(file_exists("script/_access.php")){
require_once("script/_access.php");
access([1,2,5,3]);
}
?>
<style>
</style>
<div class="kt-content  kt-grid__item kt-grid__item--fluid kt-grid kt-grid--hor" id="kt_content">

<!-- begin:: Subheader -->
<div class="kt-subheader   kt-grid__item" id="kt_subheader">
    <div class="kt-container  kt-container--fluid ">
        <div class="kt-subheader__toolbar">
            <div class="kt-subheader__wrapper">
                <div class="dropdown dropdown-inline" data-toggle="kt-tooltip" title="اضافة عميل" data-placement="top">
                    <span>اضافة عميل جديد</span>
                    <a data-toggle="modal" data-target="#addClientModal" class="btn btn-icon btn btn-label btn-label-brand btn-bold" data-toggle="dropdown" data-offset="0px,0px" aria-haspopup="true" aria-expanded="false">
                        <i class="flaticon2-add-1"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- end:: Subheader -->
					<!-- begin:: Content -->
	<div class="kt-container  kt-container--fluid  kt-grid__item kt-grid__item--fluid">
<div class="kt-portlet kt-portlet--mobile">
	<div class="kt-portlet__head">
		<div class="kt-portlet__head-label">
			<h3 class="kt-portlet__head-title">
				العملاء
			</h3>
		</div>
	</div>

	<div class="kt-portlet__body" id="client_table">
		<!--begin: Datatable -->
		<table class="table table-striped- table-bordered table-hover table-checkable responsive no-wrap" id="tb-getAllclients">
			       <thead>
	  						<tr>
								<th>ID</th>
								<th>اسم العميل</th>
								<th>رقم الهاتف</th>
								<th>البريد الالكتروني</th>
								<th>الفرع</th>
								<th>تعديل</th>
		  					</tr>
      	            </thead>
                            <tbody id="getAllclientsTable">
                            </tbody>
                            <tfoot>
	                <tr>
								<th>ID</th>
								<th>اسم العميل</th>
								<th>رقم الهاتف</th>
								<th>البريد الالكتروني</th>
								<th>الفرع</th>
								<th>تعديل</th>
					</tr>
	           </tfoot>
		</table>
		<!--end: Datatable -->
	</div>
</div>	</div>
<!-- end:: Content -->				</div>


            <!--begin::Page Vendors(used by this page) -->
                            <script src="assets/vendors/custom/datatables/datatables.bundle.js" type="text/javascript"></script>
                        <!--end::Page Vendors -->



            <!--begin::Page Scripts(used by this page) -->
                            <script src="assets/js/demo1/pages/components/datatables/extensions/responsive.js" type="text/javascript"></script>
                       <script type="text/javascript">
function getAllclients(elem){
$.ajax({
  url:"script/_getAllclients.php",
  type:"POST",
  beforeSend:function(){
    $("#client_table").addClass('loading');
  },
  success:function(res){
   $("#tb-getAllclients").DataTable().destroy();
   console.log(res);
   elem.html("");
   $("#client_table").removeClass('loading');
   $.each(res.data,function(){
     if(this.show_earnings == 1){
       show = 'text-success ';
     }else{
       show = 'text-danger ';
     }
     elem.append(
       '<tr>'+
            '<td>'+this.id+'</td>'+
            '<td>'+this.name+'</td>'+
            '<td>'+this.phone+'</td>'+
            '<td>'+this.email+'</td>'+
            '<td>'+this.branch+'</td>'+
            '<td width="150px">'+
              '<button class="btn btn-clean btn-icon-lg" onclick="editClient('+this.id+')" data-toggle="modal" data-target="#editClient"><span class="flaticon-edit"></sapn>'+
              '<button class="btn btn-clean btn-icon-lg" onclick="deleteClient('+this.id+')" data-toggle="modal" data-target="#deleteClient"><span class="flaticon-delete"></sapn>'+
              '<button class="btn btn-clean btn-icon-lg" onclick="devPriceClient('+this.id+')" data-toggle="modal" data-target="#devPriceClient"><span class="flaticon-price-tag"></sapn>'+
              '<button class=" '+show+'btn btn-clean btn-icon-lg" onclick="editSetting('+this.id+')" data-toggle="modal" data-target="#showEarningsClient"><span class="flaticon-cogwheel"></sapn>'+
              '<button class="btn btn-clean btn-icon-lg" onclick="getApiToken('+this.id+')" data-toggle="modal" data-target="#getToken"><span class="flaticon-feed"></sapn>'+
            '</button></td>'+

       '</tr>');
     });
     var myTable= $('#tb-getAllclients').DataTable({
     columns:[
    //"dummy" configuration
        { visible: true }, //col 1
        { visible: true }, //col 2
        { visible: true }, //col 3
        { visible: true }, //col 4
        { visible: true }, //col 5
        { visible: true }, //col 6
        ],
        className: 'select-checkbox',
        targets: 0,
        "oLanguage": {
        "sLengthMenu": "عرض_MENU_سجل",
        "sSearch": "بحث:" ,
        select: {
        style: 'os',
        selector: 'td:first-child'
    }
      }
});
    },
   error:function(e){
    $("#client_table").removeClass('loading');
    console.log(e);
  }
});
}
getAllclients($("#getAllclientsTable"));

</script>
<div class="modal fade" id="editClient" role="dialog">
    <div class="modal-dialog">
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">تحديث بيانات العميل</h4>
        </div>
        <div class="modal-body">
		<!--begin::Portlet-->
		<div class="kt-portlet">

			<!--begin::Form-->
			<form class="kt-form" id="editClientForm">
				<div class="kt-portlet__body">
					<div class="form-group">
						<label>الفرع</label>
						<select data-show-subtext="true" data-live-search="true" type="text" class="selectpicker form-control dropdown-primary" name="e_client_branch" id="e_client_branch"  value="">
                        </select>
                        <span class="form-text text-danger" id="e_client_branch_err"></span>
					</div>
					<div class="form-group">
						<label>الاسم الكامل:</label>
						<input type="name" id="e_client_name" name="e_client_name" class="form-control"  placeholder="ادخل الاسم الكامل">
						<span class="form-text  text-danger" id="e_client_name_err"></span>
					</div>
					<div class="form-group">
						<label>الايميل:</label>
						<input type="email" id="e_client_email" name="e_client_email" class="form-control" placeholder="ادخل البريد الالكتروني">
						<span class="form-text text-danger" id="e_client_email_err"></span>
					</div>
					<div class="form-group">
						<label>رقم الهاتف:</label>
						<input type="text" id="e_client_phone" name="e_client_phone" class="form-control" placeholder="ادخل رقم الهاتف">
						<span  id="e_client_phone_err"class="form-text  text-danger"></span>
					</div>
					<div class="form-group">
						<label>كلمة السر:</label>
						<input type="password" id="e_client_password" name="e_client_password" class="form-control" placeholder="ادخل كلمة السر">
						<span class="form-text  text-danger" id="e_client_password_err"></span>
					</div>
                    <label class="text-warning">اذا كان العميل شركه توصيل تستخدم نفس البرنامج</label>
					<div class="form-group">
						<label>توكن المزامنه</label>
						<input type="text" id="e_client_token" name="e_client_token" class="form-control" placeholder="">
						<span  id="e_client_token_err"class="form-text  text-danger"></span>
					</div>
					<div class="form-group">
						<label>Dns</label>
						<input type="text" id="e_client_dns" name="e_client_dns" class="form-control" placeholder="">
						<span class="form-text  text-danger" id="e_client_dns_err"></span>
					</div>
	            </div>
	            <div class="kt-portlet__foot kt-portlet__foot--solid">
					<div class="kt-form__actions kt-form__actions--right">
						<button type="button" onclick="updateClient()" class="btn btn-brand">تحديث</button>
						<button type="reset" data-dismiss="modal" class="btn btn-secondary">الغاء</button>
					</div>
				</div>
                <input type="hidden" id="editclientid" name="editclientid" />
			</form>
			<!--end::Form-->
		</div>
		<!--end::Portlet-->
        </div>
      </div>

    </div>
  </div>
<div class="modal fade" id="showEarningsClient" role="dialog">
    <div class="modal-dialog">
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal"></button>
          <h4 class="modal-title">عرض او اخفاء الكشوفات والارباح</h4>
        </div>
        <div class="modal-body">
		<!--begin::Portlet-->
		<div class="kt-portlet">

			<!--begin::Form-->
			<form class="kt-form" id="showEarningsForm">
				<div class="kt-portlet__body">
					<div class="form-group">
						<label>حالة عرض الكشوفات والارباح</label>
						<select type="text" class="selectpicker form-control dropdown-primary" name="show_earnings" id="show_earnings"  value="">
                           <option value="1">اضهار الارباح والكشوفات</option>
                           <option value="0">اخفاء الارباح والكشوفات</option>
                        </select>
                        <span class="form-text text-danger" id="show_earnings_err"></span>
					</div>
				</div>
                <div class="kt-portlet__foot kt-portlet__foot--solid">
					<div class="kt-form__actions kt-form__actions--right">
						<button type="button" onclick="showEarnings()" class="btn btn-brand">تحديث</button>
						<button type="reset" data-dismiss="modal" class="btn btn-secondary">الغاء</button>
					</div>
				</div>
                <input type="hidden" id="sett_client_id" name="sett_client_id" />
			</form>
			<!--end::Form-->
		</div>
		<!--end::Portlet-->
        </div>
      </div>

    </div>
</div>
<div class="modal fade" id="getToken" role="dialog">
    <div class="modal-dialog">
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal"></button>
          <h4 class="modal-title">عرض التوكن الخاص بالعميل</h4>
        </div>
        <div class="modal-body">
		<!--begin::Portlet-->
		<div class="kt-portlet">

			<!--begin::Form-->
			<form class="kt-form" id="showEarningsForm">
				<div class="kt-portlet__body">
					<div class="form-group">
                        <div class="input-group">
    						<input type="text" class="form-control" id="token" placeholder="Type some value to copy">
    						<div class="input-group-append">
    							<a class="btn btn-secondary" data-clipboard="true" data-clipboard-target="#token"><i class="la la-copy"></i></a>
    						</div>
					    </div>
					</div>
					<div class="form-group">
                        <div class="input-group">
    						<input type="text" class="form-control" id="dns" placeholder="Type some value to copy">
    						<div class="input-group-append">
    							<a class="btn btn-secondary" data-clipboard="true" data-clipboard-target="#dns"><i class="la la-copy"></i></a>
    						</div>
					    </div>
					</div>
				</div>
                <div class="kt-portlet__foot kt-portlet__foot--solid">
					<div class="kt-form__actions kt-form__actions--right">
						<button type="button" onclick="showEarnings()" class="btn btn-brand">تحديث</button>
						<button type="reset" data-dismiss="modal" class="btn btn-secondary">الغاء</button>
					</div>
				</div>
                <input type="hidden" id="sett_client_id" name="sett_client_id" />
			</form>
			<!--end::Form-->
		</div>
		<!--end::Portlet-->
        </div>
      </div>

    </div>
  </div>
<script type="text/javascript" src="js/getCities.js"></script>
<script type="text/javascript" src="js/getManagers.js"></script>
<script type="text/javascript" src="js/getBraches.js"></script>
<script src="assets/js/demo1/pages/components/forms/widgets/clipboard.js" type="text/javascript"></script>
<script>
function editClient(id){
  $(".text-danger").text("");
  $("#editclientid").val(id);
  getCities($("#e_Client_city"));
  getManagers($("#e_Client_manager"));
  $.ajax({
    url:"script/_getClientByID.php",
    data:{id: id},
    beforeSend:function(){
      $("#editClientForm").addClass('loading');
    },
    success:function(res){
       $("#editClientForm").removeClass('loading');
      if(res.success == 1){
        $.each(res.data,function(){
          $('#e_client_name').val(this.name);
          $('#e_client_email').val(this.email);
          $('#e_client_phone').val(this.phone);
          $('#e_client_branch').selectpicker('val', this.branch_id);
          $('#e_client_token').val(this.sync_token);
          $('#e_client_dns').val(this.sync_dns);
        });
      }
      console.log(res);
    },
    error:function(e){
      $("#editClientForm").removeClass('loading');
      console.log(e);
    }
  });
}
function updateClient(){
    $(".text-danger").text("");
    $.ajax({
       url:"script/_updateClient.php",
       type:"POST",
       data:$("#editClientForm").serialize(),
       beforeSend:function(){
        $("#editClientForm").addClass('loading');
       },
       success:function(res){
         $("#editClientForm").removeClass('loading');
         console.log(res);
       if(res.success == 1){
          $("#kt_form input").val("");
          Toast.success('تم التحديث');
          getAllclients($("#getAllclientsTable"));
       }else{
           $("#e_client_branch_err").text(res.error["client_branch_err"]);
           $("#e_client_name_err").text(res.error["client_name_err"]);
           $("#e_client_email_err").text(res.error["client_email_err"]);
           $("#e_client_phone_err").text(res.error["client_phone_err"]);
           $("#e_client_password_err").text(res.error["client_password_err"]);
           $("#e_client_token_err").text(res.error["client_token_err"]);
           $("#e_client_dns_err").text(res.error["client_dns_err"]);
           Toast.warning("هناك بعض المدخلات غير صالحة",'خطأ');
       }
       },
       error:function(e){
        $("#editClientForm").removeClass('loading');
        Toast.error('خطأ');
        console.log(e);
       }
    })
}
function deleteClient(id){
  if(confirm("هل انت متاكد من الحذف")){
      $.ajax({
        url:"script/_deleteClient.php",
        type:"POST",
        data:{id:id},
        success:function(res){
         if(res.success == 1){
           Toast.success('تم الحذف');
           getAllclients($("#getAllclientsTable"));
         }else{
           Toast.warning(res.msg);
         }
         console.log(res)
        } ,
        error:function(e){
          console.log(e);
        }
      });
  }
}
function editSetting(id){
  $(".text-danger").text("");
  $("#sett_client_id").val(id);
  $.ajax({
    url:"script/_getClientByID.php",
    data:{id: id},
    beforeSend:function(){
      $("#showEarningsForm").addClass('loading');
    },
    success:function(res){
       $("#showEarningsForm").removeClass('loading');
      if(res.success == 1){
        $.each(res.data,function(){
          $('#show_earnings').val(this.show_earnings);
        });
      }
      $(".selectpicker").selectpicker('refresh');
      console.log(res);
    },
    error:function(e){
      $("#editClientForm").removeClass('loading');
      console.log(e);
    }
  });
}

function showEarnings(){
    $(".text-danger").text("");
    $.ajax({
       url:"script/_showEarnings.php",
       type:"POST",
       data:$("#showEarningsForm").serialize(),
       beforeSend:function(){
        $("#showEarningsForm").addClass('loading');
       },
       success:function(res){
         $("#showEarningsForm").removeClass('loading');
         console.log(res);
       if(res.success == 1){
          $("#kt_form input").val("");
          Toast.success('تم التحديث');
          getAllclients($("#getAllclientsTable"));
       }else{
           $("#show_earnings_err").text(res.error["client_branch_err"]);
           Toast.warning("هناك بعض المدخلات غير صالحة",'خطأ');
       }
       },
       error:function(e){
        $("#editClientForm").removeClass('loading');
        Toast.error('خطأ');
        console.log(e);
       }
    });
}
function getApiToken(id){
    $.ajax({
       url:"script/_getApiToken.php",
       type:"POST",
       data:{id: id},
       beforeSend:function(){
        $("#getToken").addClass('loading');
       },
       success:function(res){
         $("#getToken").removeClass('loading');
         console.log(res);
       if(res.success == 1){
          $("#token").val(res.token);
       }else{
           Toast.warning('خطأ');
       }
       },
       error:function(e){
        $("#getToken").removeClass('loading');
        Toast.error('خطأ');
        console.log(e);
       }
    });
}
</script>
  <!-- Modal -->
  <div class="modal fade " id="addClientModal" role="dialog">
    <div class="modal-dialog modal-lg">

      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">اضافة عميل</h4>
        </div>
        <div class="modal-body">
		<!--begin::Portlet-->
		<div class="kt-portlet">

			<!--begin::Form-->
			<form class="kt-form" id="addClientForm">
                <div class="row">
  				  <div class="col-md-6">
  				    <div class="kt-portlet__body">
  					<div class="form-group">
  						<label>الفرع</label>
  						<select data-show-subtext="true" data-live-search="true" type="text" class="selectpicker form-control dropdown-primary" name="client_branch" id="client_branch"  value="">
                          </select>
                          <span class="form-text text-danger" id="client_branch_err"></span>
  					</div>
  					<div class="form-group">
  						<label>الاسم الكامل:</label>
  						<input type="name" name="client_name" class="form-control"  placeholder="ادخل الاسم الكامل">
  						<span class="form-text  text-danger" id="client_name_err"></span>
  					</div>
  					<div class="form-group">
  						<label>الايميل:</label>
  						<input type="email" name="client_email" class="form-control" placeholder="ادخل البريد الالكتروني">
  						<span class="form-text text-danger" id="client_email_err"></span>
  					</div>
  					<div class="form-group">
  						<label>رقم الهاتف:</label>
  						<input type="text" name="client_phone" class="form-control" placeholder="ادخل رقم الهاتف">
  						<span  id="client_phone_err"class="form-text  text-danger"></span>
  					</div>
  					<div class="form-group">
  						<label>كلمة السر:</label>
  						<input type="password" name="client_password" class="form-control" placeholder="ادخل كلمة السر">
  						<span class="form-text  text-danger" id="client_password_err"></span>
  					</div>
                    <label class="text-warning">اذا كان العميل شركه توصيل تستخدم نفس البرنامج</label>
					<div class="form-group">
						<label>توكن المزامنه</label>
						<input type="text" id="client_token" name="client_token" class="form-control" placeholder="">
						<span  id="client_token_err"class="form-text  text-danger"></span>
					</div>
					<div class="form-group">
						<label>Dns</label>
						<input type="text" id="client_dns" name="client_dns" class="form-control" placeholder="">
						<span class="form-text  text-danger" id="client_dns_err"></span>
					</div>
  	            </div>
  	            </div>
                  <div class="col-md-6">
                    <div class="kt-portlet__body">
    					<div class="form-group">
    						<label>سعر التوصيل بغداد:</label>
    						<input type="text" name="client_dev_price_b" class="form-control" placeholder="">
    						<span class="form-text  text-danger" id="client_dev_price_b_err"></span>
    					</div>
    					<div class="form-group">
    						<label>سعر التوصيل باقي المحافضات:</label>
    						<input type="text" name="client_dev_price_o" class="form-control" placeholder="">
    						<span class="form-text  text-danger" id="client_dev_price_o_err"></span>
    					</div>
    					<div class="form-group">
    						<label>استثنائات:</label>
    						<button type="button" onclick="addexpetionprice()" name="" class="btn btn-success" placeholder="">
                             <span class="flaticon-add"></span>&nbsp;&nbsp;اضافة سعر توصيل لمحافظة معينة
                            </button>
    					 </div>
                         <div id="exceptionCities"></div>
                    </div>
                  </div>
                </div>
	            <div class="kt-portlet__foot kt-portlet__foot--solid">
					<div class="kt-form__actions kt-form__actions--right">
						<button type="button" onclick="addClient()" class="btn btn-brand">اضافة</button>
						<button type="reset" data-dismiss="modal" class="btn btn-secondary">الغاء</button>
					</div>
				</div>
			</form>
			<!--end::Form-->
		</div>
		<!--end::Portlet-->
        </div>
      </div>

    </div>
  </div>

  <!-- Modal -->
  <div class="modal fade " id="devPriceClient" role="dialog">
    <div class="modal-dialog">

      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">تعديل اسعار التوصيل للعميل</h4>
        </div>
        <div class="modal-body">
		<!--begin::Portlet-->
		<div class="kt-portlet">

			<!--begin::Form-->
			<form class="kt-form" id="devPriceClientForm">
                <div class="row">
                <div class="col-md-12">
  				   <div id="devPriceItems" class=""></div>
                   <label class="text-danger" id="devPrice_err"></label>
                </div>
                 </div>
	            <div class="kt-portlet__foot kt-portlet__foot--solid">
					<div class="kt-form__actions kt-form__actions--right">
						<button type="button" onclick="updateDevPriceClient()" class="btn btn-brand">تحديث اسعار التوصيل</button>
						<button type="reset" data-dismiss="modal" class="btn btn-secondary">الغاء</button>
					</div>
				</div>
                <input type="hidden" name="client_id" id="client_id"/>
			</form>
			<!--end::Form-->
		</div>
		<!--end::Portlet-->
        </div>
      </div>

    </div>
  </div>

  <script src="assets/js/demo1/pages/custom/profile/overview-3.js" type="text/javascript"></script>
  <script type="text/javascript" src="js/getCities.js"></script>
  <script type="text/javascript">
  getBraches($("#client_branch"));
  getBraches($("#e_client_branch"));
  function addClient(){
  $.ajax({
     url:"script/_addClient.php",
     type:"POST",
     data:$("#addClientForm").serialize(),
     success:function(res){
       console.log(res);
       if(res.success == 1){
         getAllclients($("#getAllclientsTable"));
         $("#addClientForm input").val("");
         Toast.success('تم الاضافة');
       }else{
           $("#client_name_err").text(res.error["client_name_err"]);
           $("#client_phone_err").text(res.error["client_phone_err"]);
           $("#client_email_err").text(res.error["client_email_err"]);
           $("#client_branch_err").text(res.error["client_branch_err"]);
           $("#client_password_err").text(res.error["client_password_err"]);
           $("#client_token_err").text(res.error["client_token_err"]);
           $("#client_dns_err").text(res.error["client_dns_err"]);
       }

     },
     error:function(e){
       console.log(e);
       Toast.error.displayDuration=5000;
       Toast.error('تأكد من المدخلات','خطأ');
     }
  });
}
indecater = 1;
function addexpetionprice(){
 city =     			'<div class="form-group">'+
    						'<label>سعر توصيل مستثنى</label><br />'+
  					        '<select indecater="'+indecater+'" data-show-subtext="true" data-live-search="true" type="text" class="form-control dropdown-primary" name="client_dev_city_e[]" id="client_dev_city_e[]"  value="">'+
                            '<option>اختر</option>'+
                            '</select>'+
                            '<input type="text" name="client_dev_price_e[]" class="form-control" placeholder="سعر التوصيل">'+
    						'<span class="form-text  text-danger" id="client_dev_price_o_err"></span>'+
    					'</div>'
if(indecater < 18){
$("#exceptionCities").append(city);
 getCities($('[indecater="'+indecater+'"]'));
 indecater = indecater +1;
 }else{
   Toast.error('لا يمكن اضافة المزيد');
 }
}
function devPriceClient(id){
  $("#client_id").val(id);
  $.ajax({
     url:"script/_getDevPriceClient.php",
     type:"POST",
     data:{id: id},
     success:function(res){
        $("#devPriceItems").html("");
        i=1;
        $.each(res.data,function(){
             console.log(res);
             $("#devPriceItems").append(
      					'<div class="col-md-12">'+
      					'<div class="form-group">'+
      						'<label>'+i+"-"+this.city+'</label>'+
      						'<input type="number" value="'+this.price+'" step="50" name="devPrice[]" class="form-control" placeholder="">'+
      						'<input type="hidden" value="'+this.city_id+'"step="50" name="devCity[]" class="form-control" placeholder="">'+
      					'</div>'+
      					'</div>'
             );
         i++;
       });

     },
     error:function(e){
       console.log(e);
     }
     });
}
function updateDevPriceClient(){
  $.ajax({
     url:"script/_updateDevPriceClient.php",
     type:"POST",
     data:$("#devPriceClientForm").serialize(),
     success:function(res){
       console.log(res);
        if(res.success != 1){
          $("#devPrice_err").text(res.error);
          Toast.warning("هناك بعض المدخلات غير صالحة",'خطأ');
        }else{
          $("#devPrice_err").text("");
          Toast.success('تم تحديث اسعار التوصيل');
        }

     },
     error:function(e){
       console.log(e);
       Toast.error('خطأ');
     }
    });
}
  </script>