<?php 
include('../config.php');
$action = isset($_REQUEST['action']) ? $_REQUEST['action'] : '';
switch ($action) {
	case 'doshii_setting':  
        doshii_setting($_REQUEST);
        break;		
	case 'product_sync':  
        product_sync($_REQUEST);
        break;		
	case 'product_edit_sync':  
        product_edit_sync($_REQUEST);
        break;	
	case 'sync_product_add':  
        sync_product_add($_REQUEST);
        break;		
	case 'sync_variation_product_add':  
        sync_variation_product_add($_REQUEST);
        break;		
	case 'new_order':  
        new_order($_REQUEST);
        break;	
	case 'doshii_webhook_call':  
        doshii_webhook_call($_REQUEST);
        break;		
}


function doshii_webhook_call($data){
$con=$GLOBALS['con'];	
$inputJSON = file_get_contents('php://input');
$orderResponse = json_decode($inputJSON, TRUE); //convert JSON into array

 $select_shopquery = mysqli_query($con,"select * from new_order where doshii_order_id='".$orderResponse['data']['id']."'");
 $result_shop = mysqli_fetch_object($select_shopquery);

 $verify = $_REQUEST['verify'];
 $shop_url = $result_shop->shop_name.'.myshopify.com';
 	require_once('../jwt/jwt.php');		
	//$shop_url = 'dhosii.myshopify.com';
	if(!empty($shop_url)){
		$select_query = mysqli_query($con,"select * from doshii_shop_details_tbl where shopify_url='".$shop_url."'");
		$result = mysqli_fetch_object($select_query);

		$clientId = $result->client_id;
		$serverKey = $result->client_secret;	
		$location_id = $result->location_id;	
		$base_url = $result->base_url;	
		$access_token = $result->access_token;		
	    $exp = time();

		// create a token
		$payloadArray = array();
		$payloadArray['clientId'] = $clientId;
		if (isset($exp)) {$payloadArray['timestamp'] = $exp;}
		$token = JWT::encode($payloadArray, $serverKey);
	
		 if (!empty($verify)) { 
			echo json_encode($verify);
		 }else{
				if(!empty($orderResponse)){
					$orderstatus = $orderResponse['data']['status'];
					$orderId = $orderResponse['data']['id'];
						 $update="update `new_order` SET `doshii_order_status`='".$orderstatus."' where doshii_order_id='".$orderId."'";
						 $query=mysqli_query($con,$update);
						    require_once("../inc/functions.php");
				$requests = $_GET;
				$serializeArray = serialize($requests);
				$requests = array_diff_key($requests, array('hmac' => '')); // Remove hmac from params
				krsort($requests);	
	            $shop = $result_shop->shop_name;
				$token = $access_token;
				$query = array(
					"Content-type" => "application/json" 
				);

				$orderTag = array(
				  "order"=> array(
					"id"=> $result_shop->shopify_order_id,
					"tags"=> $orderstatus
				  )
				);

				// Run API call to modify the product
				$modified_product = shopify_call($token, $shop, "/admin/api/2020-04/orders/".$result_shop->shopify_order_id.".json", $orderTag, 'PUT');
				// Storage response
				$modified_product_response = $modified_product['response'];	
				
				}
            } 	
	}
}

function doshii_setting($data){
	$con=$GLOBALS['con'];
	$baseUrl = $_REQUEST['doshii_base_url'];
	$client_id = $_REQUEST['client_id'];
	$client_secret = $_REQUEST['client_secret'];
	$location_id = $_REQUEST['location_id'];
	$shopify_url = $_REQUEST['shopify_url'];
	$menu_managment = $_REQUEST['use_menu_management'];
    $datetime = date('Y-m-d H:i:s');
	$message='';
	
	$shopify_url_query = mysqli_query($con,"select shopify_url from doshii_shop_details_tbl where shopify_url='$shopify_url'");
	$shopify_url_row = mysqli_num_rows($shopify_url_query);
	if(!empty($shopify_url_row)){
		$update="update `doshii_shop_details_tbl` SET `base_url`='".$baseUrl."',`client_id`='".$client_id."',`client_secret`='".$client_secret."',`location_id`='".$location_id."',`menu_managment`='".$menu_managment."' where shopify_url='".$shopify_url."'";
		$query=mysqli_query($con,$update);
			if(!empty($query)){
				
				
				/*=== Doshii Order Update Hooks ===*/
	
				require_once('../jwt/jwt.php');		
				//$shopname = 'dhosii.myshopify.com';
				if(!empty($shopify_url)){
					$select_query = mysqli_query($con,"select * from doshii_shop_details_tbl where shopify_url='".$shopify_url."'");
					$shopify_row = mysqli_num_rows($select_query);
					$result = mysqli_fetch_object($select_query);
					$clientId = $result->client_id;
					$serverKey = $result->client_secret;	
					$location_id = $result->location_id;	
					$base_url = $result->base_url;		
					$exp = time();

				// create a token
				$payloadArray = array();
				$payloadArray['clientId'] = $clientId;
				if (isset($exp)) {$payloadArray['timestamp'] = $exp;}
				$token = JWT::encode($payloadArray, $serverKey);
				$curl = curl_init();
				curl_setopt_array($curl, array(
				  CURLOPT_URL => "https://sandbox.doshii.co/partner/v3/webhooks",
				  CURLOPT_RETURNTRANSFER => true,
				  CURLOPT_ENCODING => "",
				  CURLOPT_MAXREDIRS => 10,
				  CURLOPT_TIMEOUT => 0,
				  CURLOPT_FOLLOWLOCATION => true,
				  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
				  CURLOPT_CUSTOMREQUEST => "POST",
				  CURLOPT_POSTFIELDS =>"{\r\n  \"event\": \"order_updated\",\r\n  \"webhookUrl\": \"https://frontendninjas.com/doshii_app/pages/function.php?action=doshii_webhook_call&shop_url=".$shopify_url."\",\r\n  \"authenticationKey\": \"".$clientId."\",\r\n  \"authenticationToken\": \"".$serverKey."\"\r\n}",
				  CURLOPT_HTTPHEADER => array(
					"accept: application/json",
					"doshii-location-id: ".$location_id."",
					"Content-Type: application/json",
					"Authorization: Bearer ".$token.""
				  ),
				));

				$response = curl_exec($curl);
				curl_close($curl);
$response = json_encode($response);
				}
				
				$message='<div class="alert alert-success"> <strong>Success!</strong> Your setting saved successfully.</div>';
			}else{
				$message='<div class="alert alert-success"> <strong>Success!</strong> Something going wrong please check.</div>';
			}
	}
	
	/*else{
		$message='<div class="alert alert-success"> <strong>Success!</strong> Something going wrong please check.</div>';
		
	}*/
	else{
		$query="INSERT INTO doshii_shop_details_tbl(`base_url`,`client_id`,`client_secret`,`location_id`,`menu_managment`,`date_time`) 
		VALUES ('".$baseUrl."','".$client_id."','".$client_secret."','".$location_id."','".$menu_managment."','".$datetime."')";
		mysqli_query($con,$query);
		$id=mysqli_insert_id($con);
		if(!empty($id))
			{
				$message='<div class="alert alert-success"> <strong>Success!</strong> Your setting saved successfully.</div>';
			}else{
				$message='<div class="alert alert-success"> <strong>Success!</strong> Something going wrong please check.</div>';
			}
	}
		echo json_encode($message);
}

function product_sync($data){
	$con=$GLOBALS['con'];
	require_once('../jwt/jwt.php');
	$shopname = $_REQUEST['shopname'];
	if(!empty($shopname)){
		$select_query = mysqli_query($con,"select * from doshii_shop_details_tbl where shopify_url='".$shopname."'");
		//$shopify_row = mysqli_num_rows($select_query);
		$result = mysqli_fetch_object($select_query);
		
		$select_menu_query = mysqli_query($con,"select * from `doshii_menu` where shop='".$shopname."'");
		$menu_row = mysqli_num_rows($select_menu_query);
		$result_menu = mysqli_fetch_object($select_menu_query);

		$clientId = $result->client_id;
		$serverKey = $result->client_secret;	
		$location_id = $result->location_id;	
		$base_url = $result->base_url;	
		$nowFiltered = empty($result->menu_managment) ? 'no' : $result->menu_managment;
       
        $doshii_filtered_menu = empty($result_menu->doshii_filtered_menu) ? 'no' : $result_menu->doshii_filtered_menu;
		$exp = time();
		
		 $doshii_menu_versions = $result_menu->doshii_menu_version;
		$url = $base_url.'/locations/'.$location_id.'/menu';

		if ($result->menu_managment == 'yes') { $url = $url.'?filtered'; }

		if (!empty($result_menu->doshii_menu_version) && ($doshii_filtered_menu == $nowFiltered)) {
			if ($result->menu_managment == 'yes') { $url = $url.'&'; } else { $url = $url.'?'; }
			$url = $url.'lastVersion='.$doshii_menu_versions; 
		}
		 
		// create a token
		$payloadArray = array();
		$payloadArray['clientId'] = $clientId;
		if (isset($exp)) {$payloadArray['timestamp'] = $exp;}
		$token = JWT::encode($payloadArray, $serverKey);
		
		 $curl = curl_init();
		 curl_setopt($curl, CURLOPT_URL, $url);
		 curl_setopt($curl, CURLOPT_HTTPHEADER, array(
			  'Authorization: Bearer '.$token.'',
			  'doshii-location-id: '.$location_id.'',
			  'content-Type: application/json',
			  'x-doshii-writeout: Shopify 1.0',
		));
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
   
	   // EXECUTE:
	   $result1 = curl_exec($curl);
	   
		
	 if (!curl_errno($curl)) {
     switch (curl_getinfo($curl, CURLINFO_HTTP_CODE)) {
       case '304' : 
         $sync_result= array(
            'message' => 'The Doshii menu has not changed since last synchronisation',
            'updatedAt' => $result_menu->doshii_menu_updated
         );
         echo json_encode($sync_result); die();
         break;
     }
   }
   
	  if(!$result1){die("Connection Failure");}
	   curl_close($curl);
		$data=''; 
		$option='';
		$response = json_decode($result1);	
		
if($menu_row > 0){ 
	$update="update `doshii_menu` SET `location_id`='".$location_id."',`doshii_menu_version`='".$response->version."',`doshii_menu_updated`='".$response->updatedAt."',`doshii_filtered_menu`='".$nowFiltered."' where shop='".$shopname."'";
	$query=mysqli_query($con,$update);
}else{		
	$query_menu="INSERT INTO doshii_menu(`location_id`,`doshii_menu_version`,`doshii_menu_updated`,`doshii_filtered_menu`,`shop`) VALUES ('".$location_id."','".$response->version."','".$response->updatedAt."','".$nowFiltered."','".$shopname."')";
	mysqli_query($con,$query_menu);
}
		if($response->products){
			
					$delete1 = "TRUNCATE TABLE doshii_product where shop = '".$shopname."'";
					mysqli_query($con,$delete1);
					$delete2 = "TRUNCATE TABLE doshii_product_option where shop = '".$shopname."'";
					mysqli_query($con,$delete2);
					$delete3 = "TRUNCATE TABLE doshii_product_variant where shop = '".$shopname."'";
					mysqli_query($con,$delete3);
		
				   foreach ( $response->products as $product_id ) {

						$query="INSERT INTO doshii_product(`doshii_product_name`,`doshii_product_id`,`shop`) VALUES ('".$product_id->name."','".$product_id->posId."','".$shopname."')";
		                mysqli_query($con,$query);
		
					}

				   foreach($response->products as $productData){
							foreach($productData->options as $Optiondata){
								
								$query="INSERT INTO doshii_product_option(`doshii_product_option_name`,`doshii_product_option_id`,`doshii_product_id`,`shop`) VALUES ('".$Optiondata->name."','".$Optiondata->posId."','".$productData->posId."','".$shopname."')";
								mysqli_query($con,$query);
						
							}
				   }
				   
				   foreach($response->products as $productData){
						foreach($productData->options as $Optiondata){
								foreach($Optiondata->variants as $Variantdata){
									
									$query="INSERT INTO doshii_product_variant(`doshii_product_variant_name`,`doshii_product_variant_id`,`doshii_product_id`,`doshii_product_option_id`,`shop`) VALUES ('".$Variantdata->name."','".$Variantdata->posId."','".$productData->posId."','".$Optiondata->posId."','".$shopname."')";
								    mysqli_query($con,$query);
								
								}
						}
				   }
				   
				   
		$sync_result = array(
            'message' => 'The Doshii menu has been successfully synchronised with '.count($response->products).' products.',
            'updatedAt' => $response->updatedAt
        );
		
				
		}
				
	}else{
	  $sync_result = array(
            'message' => 'No products found in Doshii.'
       );
	}
	echo json_encode($sync_result);
}

function product_edit_sync($data){
	$con=$GLOBALS['con'];
	$option='';
	$doshii_type = $_REQUEST['doshii_type'];
	$shop = $_REQUEST['shop'];
    if($doshii_type == 'options' ){  
		 $doshii_parentId = $_REQUEST['doshii_parentId'];
		 $doshii_option_sql = "SELECT * FROM doshii_product_option where doshii_product_id='".$doshii_parentId."' AND shop='".$shop."'";
		 $doshii_option_results = mysqli_query($con,$doshii_option_sql); 		
		 if(!empty($doshii_option_results)){
			while($Optiondata = mysqli_fetch_object($doshii_option_results)) {      
					$option .= '<option  value="'.$Optiondata->doshii_product_option_id.'">'.$Optiondata->doshii_product_option_name.'</option>';
				   }
				echo json_encode($option);
		  }
	  }	

     if($doshii_type == 'variants'){
		 $Variantoption='';
		 $doshii_optionId = $_REQUEST['doshii_optionId'];		 
		  $doshii_variant_sql = "SELECT * FROM doshii_product_variant where doshii_product_option_id='".$doshii_optionId."' AND shop='".$shop."'";
		  $doshii_variant_results = mysqli_query($con,$doshii_variant_sql); 		
		  while($Variantdata = mysqli_fetch_object($doshii_variant_results)) {      
			$Variantoption .= '<option  value="'.$Variantdata->doshii_product_variant_id.'">'.$Variantdata->doshii_product_variant_name.'</option>';
		 }
		 echo json_encode($Variantoption);
	 }	 
	 
	  
	
}


function sync_variation_product_add($data){
	$con=$GLOBALS['con'];
	$shopify_pid = $_REQUEST['product_id'];
	$shopify_variation_id = $_REQUEST['variation_id'];
	$doshii_posIds = $_REQUEST['doshii_posId'];
	$doshii_option_posId = $_REQUEST['doshii_option_posId'];
	$doshii_variant_posId = $_REQUEST['doshii_variant_posId'];
	$message='';
	$total_doshii_option_posId = [];
	$total_doshii_variant_posId = [];
	for($i=0; $i<count($doshii_option_posId); $i++){ 
	      array_push($total_doshii_option_posId,$doshii_option_posId[$i]);
	}
	
	for($j=0; $j<count($doshii_variant_posId); $j++){ 
	      array_push($total_doshii_variant_posId,$doshii_variant_posId[$j]);
	}
	
	$shopify_url_query = mysqli_query($con,"select * from product_sync_tbl where shopify_variation_id='".$shopify_variation_id."'");
	$shopify_url_row = mysqli_num_rows($shopify_url_query);
	if($shopify_url_row > 0){
		$update="update `product_sync_tbl` SET `doshii_pid`='".$doshii_posIds."',`shopify_variation_id`='".$shopify_variation_id."',`doshii_option_pid`='".serialize($total_doshii_option_posId)."',`doshii_variant_pid`='".serialize($total_doshii_variant_posId)."' where shopify_variation_id='".$shopify_variation_id."'";
		$query=mysqli_query($con,$update);
			if(!empty($query)){
				$message='<div class="alert alert-success"> <strong>Success!</strong> Your Data Saved Successfully.</div>';
			}else{
				$message='<div class="alert alert-success"> <strong>Success!</strong> Something going wrong please check.</div>';
			}
			
	}else{	
		$query="INSERT INTO product_sync_tbl(`shopify_pid`,`shopify_variation_id`,`doshii_pid`,`doshii_option_pid`,`doshii_variant_pid`) VALUES ('".$shopify_pid."','".$shopify_variation_id."','".$doshii_posIds."','".serialize($total_doshii_option_posId)."','".serialize($total_doshii_variant_posId)."')";
			mysqli_query($con,$query);
			$id=mysqli_insert_id($con);
				if(!empty($id))
					{
						$message='<div class="alert alert-success"> <strong>Success!</strong> Your Data Saved Successfully.</div>';
					}else{
						$message='<div class="alert alert-success"> <strong>Success!</strong> Something going wrong please check.</div>';
					}
	}		
	echo json_encode($message);
}

/*function sync_variation_product_add($data){
	$con=$GLOBALS['con'];
	$shopify_pid = $_REQUEST['product_id'];
	$shopify_variation_id = $_REQUEST['variation_id'];
	$doshii_posIds = $_REQUEST['doshii_posId'];
	$doshii_option_posId = $_REQUEST['doshii_option_posId'];
	$doshii_variant_posId = $_REQUEST['doshii_variant_posId'];
	$message='';
	
	for($i=0; $i<count($doshii_posIds); $i++){
	//echo json_encode('vivek:');
	$shopify_url_query = mysqli_query($con,"select * from product_sync_tbl where shopify_variation_id='".$shopify_variation_id[$i]."'");
	$shopify_url_row = mysqli_num_rows($shopify_url_query);
	if($shopify_url_row > 0){
		
		$update="update `product_sync_tbl` SET `doshii_pid`='".$doshii_posIds[$i]."',`shopify_variation_id`='".$shopify_variation_id[$i]."',`doshii_option_pid`='".$doshii_option_posId[$i]."',`doshii_variant_pid`='".serialize($doshii_variant_posId[$i])."' where shopify_variation_id='".$shopify_variation_id[$i]."'";
		$query=mysqli_query($con,$update);
			if(!empty($query)){
				$message='<div class="alert alert-success"> <strong>Success!</strong> Your Data Saved Successfully.</div>';
			}else{
				$message='<div class="alert alert-success"> <strong>Success!</strong> Something going wrong please check.</div>';
			}
			
	}else{	
		$query="INSERT INTO product_sync_tbl(`shopify_pid`,`shopify_variation_id`,`doshii_pid`,`doshii_option_pid`,`doshii_variant_pid`) VALUES ('".$shopify_pid."','".$shopify_variation_id[$i]."','".$doshii_posIds[$i]."','".$doshii_option_posId[$i]."','".serialize($doshii_variant_posId[$i])."')";
			mysqli_query($con,$query);
			$id=mysqli_insert_id($con);
				if(!empty($id))
					{
						$message='<div class="alert alert-success"> <strong>Success!</strong> Your Data Saved Successfully.</div>';
					}else{
						$message='<div class="alert alert-success"> <strong>Success!</strong> Something going wrong please check.</div>';
					}
	}		
	}
	echo json_encode($message);
}*/


function sync_product_add($data){
	require_once("../inc/functions.php");
	$con=$GLOBALS['con'];
	$doshii_posId = $_REQUEST['doshii_posId'];
	if(isset($_REQUEST['doshii_option_posId'])){ $doshii_option_posId = $_REQUEST['doshii_option_posId']; }else { 	$doshii_option_posId = ''; }
	if(isset($_REQUEST['doshii_variant_posId'])){ $doshii_variant_posId = $_REQUEST['doshii_variant_posId']; }else { 	$doshii_variant_posId = ''; }
	$shopify_pid = $_REQUEST['product_id'];
	$message='';
	$shopify_url_query = mysqli_query($con,"select * from product_sync_tbl where shopify_pid='$shopify_pid'");
	$shopify_url_row = mysqli_num_rows($shopify_url_query);
	if($shopify_url_row > 0){
		$update="update `product_sync_tbl` SET `doshii_pid`='".$doshii_posId."' where shopify_pid='".$shopify_pid."'";
		$query=mysqli_query($con,$update);
			if(!empty($query)){
				$message='<div class="alert alert-success"> <strong>Success!</strong> Your Data Saved Successfully.</div>';
			}else{
				$message='<div class="alert alert-success"> <strong>Success!</strong> Something going wrong please check.</div>';
			}
	}else{
		$query="INSERT INTO product_sync_tbl(`shopify_pid`,`shopify_variation_id`,`doshii_pid`,`doshii_option_pid`,`doshii_variant_pid`) VALUES ('".$shopify_pid."','','".$doshii_posId."','','')";
		mysqli_query($con,$query);
		$id=mysqli_insert_id($con);
			if(!empty($id))
				{
					$message='<div class="alert alert-success"> <strong>Success!</strong> Your Data Saved Successfully.</div>';
				}else{
					$message='<div class="alert alert-success"> <strong>Success!</strong> Something going wrong please check.</div>';
				}
	}		
	echo json_encode($message);
}

function new_order($data){
 $con=$GLOBALS['con'];
 $action = $_REQUEST['action'];
 $shopn = $_REQUEST['shop'];				 
 $webhook_content = NULL;
 $webhook = fopen('php://input' , 'rb');
 while (!feof($webhook)) {
 $webhook_content .= fread($webhook, 4096);
 }
 fclose($webhook);
 // Decode Shopify POST
 $webhook_ordercontent = json_decode($webhook_content, TRUE);
 $webhook_orderjson = json_encode($webhook_content, TRUE);
 $shopname = explode("/",$webhook_ordercontent['order_status_url']);
 $shopDetails = explode(".",$shopname[2]);
 $shop = $shopDetails[0];	
if($shop == $shopn){
  $to = $action.'-'.$shopn;
  $query="INSERT INTO shopdetails(`details`) VALUES ('".$to."')";
  mysqli_query($con,$query);
	
	if(!empty($webhook_ordercontent)){
	$shopify_Order_Id = $webhook_ordercontent['id'];
    $status = 'pending';
	$shopify_pid = [];
	$shopify_variation_id = [];
	$doshii_pid = [];
	$doshii_option_pid = [];
	$doshii_variant_pid = [];
	foreach($webhook_ordercontent['line_items'] as $webhook_itemdata){
		$shopify_productid = $webhook_itemdata['product_id'];
		$variant_title = $webhook_itemdata['variant_title'];
		if(empty($variant_title)){
			$shopify_url_query = mysqli_query($con,"select * from product_sync_tbl where shopify_pid='$shopify_productid'");
		}else{
			$shopify_variant_id = $webhook_itemdata['variant_id'];
			$shopify_url_query = mysqli_query($con,"select * from product_sync_tbl where shopify_variation_id='$shopify_variant_id'");
		}
		$shopify_url_row = mysqli_num_rows($shopify_url_query);
		if($shopify_url_row > 0){ 
		  $result  = mysqli_fetch_object($shopify_url_query);
		  array_push($shopify_pid,$webhook_itemdata['product_id']);	
		  array_push($shopify_variation_id,$result->shopify_variation_id);			
		  array_push($doshii_pid,$result->doshii_pid);			
		  array_push($doshii_option_pid,$result->doshii_option_pid);			
		  array_push($doshii_variant_pid,$result->doshii_variant_pid);			
		}
      }  

	   //$id=mysqli_insert_id($con);

   
//if(!empty($insertOrder)){	   
//$webhook_ordercontent = json_decode($insertOrder, TRUE);	   

$itemData = [];
$transactionsData =[];
$ShippingData = [];
$OrderType = '';
$MultipleOption = [];
$order_id = $webhook_ordercontent['id'];
$date_created = $webhook_ordercontent['created_at'];
if(empty($webhook_ordercontent['shipping_address'])){
  $first_name = $webhook_ordercontent['billing_address']['first_name'];
  $last_name = $webhook_ordercontent['billing_address']['last_name'];
  $address1 = $webhook_ordercontent['billing_address']['address1'];
  $address2 = $webhook_ordercontent['billing_address']['address2'];
  $city = $webhook_ordercontent['billing_address']['city'];
  $state = $webhook_ordercontent['billing_address']['province'];
  $postalCode = $webhook_ordercontent['billing_address']['zip'];
  $country = $webhook_ordercontent['billing_address']['country_code'];
  $OrderType = 'pickup';
}else{
  $first_name = $webhook_ordercontent['shipping_address']['first_name'];
  $last_name = $webhook_ordercontent['shipping_address']['last_name'];
  $address1 = $webhook_ordercontent['shipping_address']['address1'];
  $address2 = $webhook_ordercontent['shipping_address']['address2'];
  $city = $webhook_ordercontent['shipping_address']['city'];
  $state = $webhook_ordercontent['shipping_address']['province'];
  $postalCode = $webhook_ordercontent['shipping_address']['zip'];
  $country = $webhook_ordercontent['shipping_address']['country_code'];
  $OrderType = 'delivery';
}
  $email = $webhook_ordercontent['email'];
  $phone = $webhook_ordercontent['phone'];
  if(!empty($phone)){  $phone = $phone; }else{ $phone ='0123456789'; }

	$status = 'pending';
	$notes = $webhook_ordercontent['note'];
	$totalTransactionAmount = to_pennies($webhook_ordercontent['total_price']);
	if(!empty($notes)){  $notes = '"notes":"'.$notes.'",'; }else{ $notes =''; }
	$posId = $webhook_ordercontent['id'];
	$uuid = $webhook_ordercontent['order_number'];
	$transactionsId = $webhook_ordercontent['checkout_token'];
	$paymentMethod = $webhook_ordercontent['processing_method'];
	//$webhook_ordercontent['shipping_lines'][0]['title']
	$user_id = $uuid;
	if($paymentMethod == 'free'){ 
	$paymentMethod = 'cash'; 
		array_push($transactionsData);
	}else{
	  //stripe
	  $paymentMethod = 'visa';
	  array_push($transactionsData, array('amount' => $totalTransactionAmount,'reference' => strval($user_id),'invoice' => strval($order_id),'method' => $paymentMethod,'tip' => 0,'prepaid' => true,'surcounts' => []));
	}
	
	if(!empty($webhook_ordercontent['shipping_lines'])){
        $shippingAmount = $webhook_ordercontent['shipping_lines'][0]['price'];
        $shippingcode = $webhook_ordercontent['shipping_lines'][0]['code'];
		if($shippingAmount != '0.00'){
         array_push($ShippingData, array('name' =>$shippingcode,'amount' => to_pennies($shippingAmount),'type' => "absolute",'value' => to_pennies($shippingAmount)));
		}
    }
	if(!empty($webhook_ordercontent['discount_codes'])){
		 $discountAmount = $webhook_ordercontent['discount_codes'][0]['amount'];
		 $code = $webhook_ordercontent['discount_codes'][0]['code'];
		 $total_discounts = $webhook_ordercontent['total_discounts'];
         array_push($ShippingData, array('name' =>'Coupon Discount','description'=>'Coupon:'.$code,'amount' => '-'.to_pennies($discountAmount),'type' => "absolute",'value' => '-'.to_pennies($total_discounts)));
	}
	
	
	

	$orderStatus = $status;
	foreach ($webhook_ordercontent['line_items'] as $item) {
	   $product_id = $item['product_id'];
	   $product_price = $item['price'];
	    $name = $item['title'];
	    $quantity = $item['quantity'];
		$subtotal = $webhook_ordercontent['subtotal_price'];
	    $total = $webhook_ordercontent['total_price'];

	$fullname = $first_name.' '.$last_name;
	$externalOrderRef = $order_id;
	$productPrice = to_pennies($product_price);
	$centAmount = to_pennies($total);
	$beforeSurAmount = to_pennies($product_price*$quantity);
      if(empty($item['variant_title'])){
		  $shopify_url_query = mysqli_query($con,"select * from product_sync_tbl where shopify_pid='$product_id'");
		  $doshii_posId_results  = mysqli_fetch_object($shopify_url_query);
		  $doshii_posId = $doshii_posId_results->doshii_pid;
          $doshii_option_posId = $doshii_posId_results->doshii_option_pid;   
		  $doshii_variant_posId = unserialize($doshii_posId_results->doshii_variant_pid);
		  if(empty($doshii_posId)){  $doshii_posId = ''; }
			
          if(empty($doshii_posId)){
		    array_push($itemData, array('uuid' => strval($user_id),'name' => $name,'quantity' => $quantity,'unitPrice' => $productPrice,'totalBeforeSurcounts' => $beforeSurAmount,'totalAfterSurcounts' => $beforeSurAmount,'surcounts' => [],'options' => []));
          }else{
            array_push($itemData, array('uuid' => strval($user_id),'posId' => $doshii_posId,'name' => $name,'quantity' => $quantity,'unitPrice' => $productPrice,'totalBeforeSurcounts' => $beforeSurAmount,'totalAfterSurcounts' => $beforeSurAmount,'surcounts' => [],'options' => []));
          }
	
	 }else{

	      $variation_id = $item['variant_id'];
	      $shopify_url_query = mysqli_query($con,"select * from product_sync_tbl where shopify_variation_id='$variation_id'");
		  $doshii_posId_results  = mysqli_fetch_object($shopify_url_query);
		  $doshii_posId = $doshii_posId_results->doshii_pid;
         /* $doshii_option_posId = $doshii_posId_results->doshii_option_pid;   
		  $doshii_variant_posId = unserialize($doshii_posId_results->doshii_variant_pid);

		  $doshii_option_sql = mysqli_query($con,"select * from doshii_product_option where doshii_product_option_id='$doshii_option_posId'");
		  $doshii_option_posId_id  = mysqli_fetch_object($doshii_option_sql);
		 
		  $option_posId = $doshii_option_posId_id->doshii_product_option_id;
		  $option_name = $doshii_option_posId_id->doshii_product_option_name;*/
		  
		   $doshii_option_posId = unserialize($doshii_posId_results->doshii_option_pid);   
		  $doshii_variant_posId = unserialize($doshii_posId_results->doshii_variant_pid);

		  for($o=0;$o<count($doshii_option_posId);$o++){
			  $doshii_option_sql = mysqli_query($con,"select * from doshii_product_option where doshii_product_option_id='$doshii_option_posId[$o]'");
			  $doshii_option_posId_id  = mysqli_fetch_object($doshii_option_sql);
			  $option_posId = $doshii_option_posId_id->doshii_product_option_id;
			  $option_name = $doshii_option_posId_id->doshii_product_option_name;
			  
			  $doshii_variant_sql = mysqli_query($con,"select * from doshii_product_variant where doshii_product_variant_id='$doshii_variant_posId[$o]'");
			  $doshii_variant_posId_id  = mysqli_fetch_object($doshii_variant_sql);
			  $variant_posId = $doshii_variant_posId_id->doshii_product_variant_id;
			  $variant_name = $doshii_variant_posId_id->doshii_product_variant_name;
			  
			  
			  array_push($MultipleOption,array("posId"=>$option_posId,"name"=>$option_name,"variants"=>[array("posId"=>$variant_posId,"name"=>$variant_name,"price"=>0)]));
			  
			  
		  }
		  

		 $variationName =  $item['variant_title'];
		 $variationPrice =  to_pennies($item['price']);
		 $productType = 'single';
		 
		 if(empty($doshii_posId)){ 
			$doshii_posId = strval($product_id);   
			$doshii_variant_posId = strval($variation_id); 
			array_push($itemData, array('name' => $name,'quantity' => $quantity,'unitPrice' => $variationPrice,'totalBeforeSurcounts' => $beforeSurAmount,'totalAfterSurcounts' => $beforeSurAmount, "tags"=>[],'type' => $productType,'surcounts' => [],'taxes' => [],"options"=>[array("name"=>$name,"variants"=>[array("name"=>$variationName,"price"=>0)])]));
		 }else{

			//array("posId"=>$option_posId,"name"=>$option_name,"variants"=>[array("posId"=>$doshii_variant_posId,"name"=>$variationName,"price"=>0)])

			array_push($itemData, array('posId' => $doshii_posId,'name' => $name,'quantity' => $quantity,'unitPrice' => $variationPrice,'totalBeforeSurcounts' => $beforeSurAmount,'totalAfterSurcounts' => $beforeSurAmount, "tags"=>[],'type' => $productType,'surcounts' => [],'taxes' => [],"options"=>$MultipleOption));
		 }
	 }
   }
	
	
$itemData = json_encode($itemData);
$transactionsData = json_encode($transactionsData);
$ShippingData = json_encode($ShippingData);	

$myOrderdata = '{
    "order": {
                "externalOrderRef": "'.$externalOrderRef.'",
				'.$notes.'
				"type": "'.$OrderType.'",
				"status": "'.$orderStatus.'",
        "items": '.$itemData.',
        "surcounts": '.$ShippingData.',
        "taxes": []
    },
    "consumer": {
				"name": "'.$fullname.'",
				"email": "'.$email.'",
				"phone": "'.$phone.'",
				"address": {
					'.$notes.'
					"line1": "'.$address1.'",
					"line2": "'.$address1.'",
					"city": "'.$city.'",
					"state": "'.$state.'",
					"postalCode": "'.$postalCode.'",
					"country": "'.$country.'"
				}
    },
    "transactions": '.$transactionsData.'
}';


	
//$shopname = 'dhosii.myshopify.com';
 require_once('../jwt/jwt.php');
	if(!empty($shopname[2])){
		$select_query = mysqli_query($con,"select * from doshii_shop_details_tbl where shopify_url='".$shopname[2]."'");
		$shopify_row = mysqli_num_rows($select_query);
		$result = mysqli_fetch_object($select_query);

		$clientId = $result->client_id;
		$serverKey = $result->client_secret;	
		$location_id = $result->location_id;	
		$base_url = $result->base_url;		
	    $exp = time();

	// create a token
	$payloadArray = array();
	$payloadArray['clientId'] = $clientId;
	if (isset($exp)) {$payloadArray['timestamp'] = $exp;}
	$token1 = JWT::encode($payloadArray, $serverKey);
		
if($myOrderdata != ''){
	if(!empty($base_url) && !empty($location_id)){
		
		
		$checkorder_query = mysqli_query($con,"select * from new_order where shopify_order_id = '".$shopify_Order_Id."'");
        $checkrow = mysqli_num_rows($checkorder_query);

			/*$url = $base_url.'/orders?externalOrderRef='.$externalOrderRef;
			$curl = curl_init();
			curl_setopt($curl, CURLOPT_URL, $url);
			   curl_setopt($curl, CURLOPT_HTTPHEADER, array(
				  'Authorization: Bearer '.$token1.'',
				  'doshii-location-id: '.$location_id.'',
				  'content-Type: application/json',
			   ));
			   curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
			   curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
			   // EXECUTE:
			   $result = curl_exec($curl);
			   if(!$result){die("Connection Failure");}
			   curl_close($curl);
			$Checkresponse = json_decode($result); 
			//echo 'Check <pre>'; print_r($Checkresponse); echo '</pre>';
				$Checkresponse1 = json_encode($result); 
			$query="INSERT INTO shopdetails(`details`) VALUES ('".$Checkresponse1."')";
	        mysqli_query($con,$query);
	   */
			
			if($checkrow == 0){
				  
	   $query="INSERT INTO new_order(`doshii_order_status`,`shopify_order_id`,`shopify_pid`,`shopify_variation_id`,`doshii_order_id`,`doshii_pid`,`doshii_option_pid`,`doshii_variant_pid`,`shopify_order_json`,`shop_name`) VALUES ('".$status."','".$shopify_Order_Id."','".serialize($shopify_pid)."','".serialize($shopify_variation_id)."','','".serialize($doshii_pid)."','".serialize($doshii_option_pid)."','".serialize($doshii_variant_pid)."','".$webhook_orderjson."','".$shop."')";
	   $insertOrder = mysqli_query($con,$query);
	   
					$curl = curl_init();
					curl_setopt_array($curl, array(
					  CURLOPT_URL => $base_url."/orders",
					  CURLOPT_RETURNTRANSFER => true,
					  CURLOPT_ENCODING => "",
					  CURLOPT_MAXREDIRS => 10,
					  CURLOPT_TIMEOUT => 0,
					  CURLOPT_FOLLOWLOCATION => true,
					  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
					  CURLOPT_CUSTOMREQUEST => "POST",
					  CURLOPT_POSTFIELDS =>$myOrderdata,
					  CURLOPT_HTTPHEADER => array(
						"content-Type: application/json",
						"doshii-location-id: ".$location_id."",
						"accept: application/json",
						"Accept-encoding: gzip",
						"Authorization: Bearer ".$token1.""
					  ),
					));

					$response = curl_exec($curl);
					$response = json_decode($response);
					$response1 = json_encode($response);
					curl_close($curl);
					//echo 'Creart order';
					//echo '<pre>'; print_r($response); echo '</pre>';
			$query="INSERT INTO shopdetails(`details`) VALUES ('".$response1."')";
	        mysqli_query($con,$query);
			
				$update="update `new_order` SET `doshii_order_id`='".$response->id."' where shopify_order_id='".$shopify_Order_Id."'";
		        $query=mysqli_query($con,$update);
			    //echo json_encode($response);
				
				require_once("../inc/functions.php");
				$requests = $_GET;
				$serializeArray = serialize($requests);
				$requests = array_diff_key($requests, array('hmac' => '')); // Remove hmac from params
				krsort($requests);

 

				$select_query = mysqli_query($con,"select * from doshii_shop_details_tbl where shopify_url = '".$shopname[2]."'");
				$shopify_row = mysqli_num_rows($select_query);
				$result_shop=mysqli_fetch_object($select_query);
				$token = $result_shop->access_token;
 
				//$shop = "dhosii";
				//$token = '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';
				$query = array(
					"Content-type" => "application/json" 
				);

				$orderTag = array(
				  "order"=> array(
					"id"=> $shopify_Order_Id,
					"tags"=> $status
				  )
				);

				// Run API call to modify the product
				$modified_product = shopify_call($token, $shop, "/admin/api/2020-04/orders/".$shopify_Order_Id.".json", $orderTag, 'PUT');
				// Storage response
				$modified_product_response = $modified_product['response'];	
				
			}
		}
}
	}
	

	//}  
	
}
}
}

function to_pennies($value)
{
    return intval(
        strval(floatval(
            preg_replace("/[^0-9.]/", "", $value)
        ) * 100)
    );
}
?>