<script>
$( "#setting_btn" ).click(function() {
	var doshii_setting_data = $('#doshii_setting_form').serialize()
	//alert(doshii_setting_data);
   $.ajax({
    type: 'POST',
	dataType:'json',
    url: 'function.php',
    data: doshii_setting_data,
     success: function(response) {
       //alert(response);
	   $('.message').html(response);
	} 
 });
});


$( "#product_sync_btn" ).click(function() {
	jQuery(this).attr('disabled','disabled');
	var shop = jQuery(this).attr('shop');
	jQuery('#processing').show();
	jQuery.ajax({
	 type : "GET",
	 dataType : "JSON",
     url: 'function.php',
	 data : {action: "product_sync", shopname:shop },
	 success: function(response) {
		 console.log('test:'+response.message);
		   jQuery('.sync_message').html(response.message);
		   jQuery(this).removeAttr('disabled','disabled');
		   jQuery('#processing').hide();
	 },
	 error: function(request,status,errorThrown) {
			console.log(request);
			console.log(status);
			console.log(errorThrown);
			jQuery('#processing').hide();
			jQuery(this).removeAttr('disabled','disabled');
		}
  }) 
});


/*$('#doshii_posId').on('change', function() {
	var doshii_parentId = this.value;
	var doshii_type = 'options';
    jQuery.ajax({
         type : "post",
		 dataType : "JSON",
         url : 'function.php',
         data : {action: "product_edit_sync", doshii_parentId : doshii_parentId , doshii_type : doshii_type},
         success: function(response) {
			   if(response != '' || response != 0){
				   jQuery('.overlay_section').removeClass('overlay'); 
				   jQuery('.doshii_option_posId').show();
				   jQuery('#doshii_option_posId,#doshii_variant_posId').empty();
				   jQuery('#doshii_option_posId').prepend("<option value='' selected='selected'>Select Option</option>");
				   jQuery('#doshii_option_posId').append(response);
				   console.log('response:'+JSON.stringify(response));
			   }else{
				   //jQuery('.overlay_section').removeClass('overlay');
				   jQuery('#doshii_option_posId,#doshii_variant_posId').empty();
				   jQuery('.doshii_option_posId,.doshii_variant_posId').hide();
			   }
         },
		 error: function(request,status,errorThrown) {
				console.log(request);
				console.log(status);
				console.log(errorThrown);
			}
      }) 
	  
	  
});



$('#doshii_option_posId').on('change', function() {
		var doshii_optionId = this.value;
		var doshii_type = 'variants';
		jQuery.ajax({
			 type : "post",
			 dataType : "JSON",
			 url : 'function.php',
			 data : {action: "product_edit_sync", doshii_optionId : doshii_optionId, doshii_type : doshii_type },
			 success: function(response) {
				   if(response != '' || response != null ){
					   jQuery('.doshii_variant_posId').show();
					   jQuery('#doshii_variant_posId').empty();
					   jQuery('#doshii_variant_posId').append(response);
					   console.log('varientresponse:'+JSON.stringify(response));
				   }
			 },
			 error: function(request,status,errorThrown) {
					console.log(request);
					console.log(status);
					console.log(errorThrown);
				}
		  }) 
});
*/



function variation_doshiParent_id(sel,variationid,shop){
	var doshii_parentId = sel.value;
	var doshii_type = 'options';
    jQuery.ajax({
         type : "post",
		 dataType : "JSON",
         url : 'function.php',
         data : {action: "product_edit_sync", doshii_parentId : doshii_parentId , doshii_type : doshii_type, shop : shop},
         success: function(response) {
			   if(response != '' || response != 0){
				   jQuery('.overlay_section').removeClass('overlay'); 
				   jQuery('.doshii_option_posId_'+variationid).show();
				   jQuery('#doshii_option_posId_'+variationid+',#doshii_variant_posId_'+variationid).empty();
				   jQuery('#doshii_option_posId_'+variationid).prepend("<option value='' selected='selected'>Select Option</option>");
				   jQuery('#doshii_option_posId_'+variationid).append(response);
				   console.log('response:'+JSON.stringify(response));
			   }else{
				   //jQuery('.overlay_section').removeClass('overlay');
				   jQuery('#doshii_option_posId_'+variationid+',#doshii_variant_posId_'+variationid).empty();
				   jQuery('.doshii_option_posId_'+variationid+',.doshii_variant_posId_'+variationid).hide();
			   }
         },
		 error: function(request,status,errorThrown) {
				console.log(request);
				console.log(status);
				console.log(errorThrown);
			}
      }) 
	  
}

function variation_doshivarient_id(sel,variationid,shop){ 
	var doshii_optionId = sel.value;
	var doshii_type = 'variants';
	jQuery.ajax({
		 type : "post",
		 dataType : "JSON",
		 url : 'function.php',
		 data : {action: "product_edit_sync", doshii_optionId : doshii_optionId, doshii_type : doshii_type, shop : shop },
		 success: function(response) {
			   if(response != '' || response != null ){
				   jQuery('.doshii_variant_posId_'+variationid).show();
				   jQuery('#doshii_variant_posId_'+variationid).empty();
				   jQuery('#doshii_variant_posId_'+variationid).append(response);
				   console.log('varientresponse:'+JSON.stringify(response));
			   }
		 },
		 error: function(request,status,errorThrown) {
				console.log(request);
				console.log(status);
				console.log(errorThrown);
			}
	  }) 
}

$('.product_variation_edit_submit').click(function() {
	var var_id = $(this).attr('var_id');
	var editFormData = $('#editForm_'+var_id).serialize();
	console.log('editFormData:'+editFormData);
	jQuery.ajax({
		 type : "post",
		 dataType : "JSON",
		 url : 'function.php',
		 data : editFormData,
		 success: function(response) {
			 console.log('respons:'+response);
				   jQuery('.message').html(response);
				   console.log('varientresponse:'+JSON.stringify(response));
		 },
		 error: function(request,status,errorThrown) {
				console.log(request);
				console.log(status);
				console.log(errorThrown);
			}
	})
});

$('#product_edit_submit').click(function() {
	var editFormData = $('#editForm').serialize();
	console.log('editFormData:'+editFormData);
	jQuery.ajax({
		 type : "post",
		 dataType : "JSON",
		 url : 'function.php',
		 data : editFormData,
		 success: function(response) {
			 console.log('respons:'+response);
				   jQuery('.message').html(response);
				   console.log('varientresponse:'+JSON.stringify(response));
		 },
		 error: function(request,status,errorThrown) {
				console.log(request);
				console.log(status);
				console.log(errorThrown);
			}
	})
});


/*$( document ).ready(function() {
	jQuery.ajax({
		 type : "GET",
		 dataType : "JSON",
		 url : 'function.php',
		 data : { action:'new_order' },
		 success: function(response) {
				   //jQuery('.message').html(response);
				   console.log('varientresponse:'+JSON.stringify(response));
		 },
		 error: function(request,status,errorThrown) {
				console.log(request);
				console.log(status);
				console.log(errorThrown);
			}
	})
});*/	
</script>
<?php
 /*if(isset($_REQUEST['verify'])){
    $verify = $_REQUEST['verify'];
	 if (!empty($verify)) { 
	   $query="INSERT INTO shopdetails(`details`) VALUES ('".$verify."')";
	   mysqli_query($con,$query);
					?> 
			<script>
				$( document ).ready(function() {
					alert()
					jQuery.ajax({
						 type : "POST",
						 dataType : "JSON",
						 url : 'function.php',
						 data : { action:'doshii_webhook_call', 'verify':'<?php echo $verify; ?>' },
						 success: function(response) {
								   return response;
								   console.log('varientresponse:'+JSON.stringify(response));
						 },
						 error: function(request,status,errorThrown) {
								console.log(request);
								console.log(status);
								console.log(errorThrown);
							}
					})
				});
			</script>
		 
	<?php	
	 }
	  
 }else{
 	
//$_REQUEST['action'] = 'doshii_webhook_call';
    if(isset($_REQUEST['action'])){
	$action =  $_REQUEST['action'];
	$shop = $_REQUEST['shop'];
	if($action == 'doshii_webhook_call'){
		
		 $query="INSERT INTO shopdetails(`details`) VALUES ('".$action."')";
	     mysqli_query($con,$query);
	   
		
?>

<script>
$( document ).ready(function() {
	alert()
	jQuery.ajax({
		 type : "POST",
		 dataType : "JSON",
		 url : 'function.php',
		 data : { action:'doshii_webhook_call', shop:'<?php echo $shop; ?>' },
		 success: function(response) {
			 
				   //jQuery('.message').html(response);
				   console.log('varientresponse:'+JSON.stringify(response));
		 },
		 error: function(request,status,errorThrown) {
				console.log(request);
				console.log(status);
				console.log(errorThrown);
			}
	})
});
</script>
<?php 	}
 } }*/
 ?>