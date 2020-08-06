<?php
include('config.php');
// Get our helper functions
require_once("inc/functions.php");

// Set variables for our request
$api_key = "22d574aee2e8094e87cbfd2e91514448";
$shared_secret = "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";
$params = $_GET; // Retrieve all request parameters 
$hmac = $_GET['hmac']; // Retrieve HMAC request parameter

$params = array_diff_key($params, array('hmac' => '')); // Remove hmac from params
ksort($params); // Sort params lexographically
$computed_hmac = hash_hmac('sha256', http_build_query($params), $shared_secret);

//echo '<pre>'; print_r($params); echo '</pre>';


// Use hmac data to check that the response is from Shopify or not
if (hash_equals($hmac, $computed_hmac)) {

	// Set variables for our request
	$query = array(
		"client_id" => $api_key, // Your API key
		"client_secret" => $shared_secret, // Your app credentials (secret key)
		"code" => $params['code'] // Grab the access key from the URL
	);

	
	//echo '<pre>'; print_r($query); echo '</pre>';
 
	// Generate access token URL
	$access_token_url = "https://" . $params['shop'] . "/admin/oauth/access_token";

	// Configure curl client and execute request
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_URL, $access_token_url);
	curl_setopt($ch, CURLOPT_POST, count($query));
	curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($query));
	$result = curl_exec($ch);
	curl_close($ch);

	// Store the access token
	$result = json_decode($result, true);
	$access_token = $result['access_token'];

	// Show the access token (don't do this in production!)
	//echo $access_token;
	
	$select_query = mysqli_query($con,"select * from doshii_shop_details_tbl where shopify_url = '".$params['shop']."'");
    $shopify_row = mysqli_num_rows($select_query);
    if($shopify_row == 0){
		$query="INSERT INTO doshii_shop_details_tbl(`base_url`,`client_id`,`client_secret`,`location_id`,`shopify_url`,`access_token`,`date_time`) 
		VALUES ('','','','','".$params['shop']."','".$access_token."','".date('Y-m-d H:i:s')."')";
		$insertQuery = mysqli_query($con,$query);
		$id=mysqli_insert_id($con);
		if(!empty($id)){
			
			header('Location: https://'.$params['shop'].'/admin/apps/doshiiapp?action=hooks');
		}	
	}else{
		$update="update `doshii_shop_details_tbl` SET `access_token`='".$access_token."' where shopify_url='".$params['shop']."'";
		$query=mysqli_query($con,$update);
		header('Location: https://'.$params['shop'].'/admin/apps/doshiiapp?action=hooks');
	}

} else {
	// Someone is trying to be shady!
	die('This request is NOT from Shopify!');
}