<?php
include('../header.php');
$shop = $_REQUEST['shop']; 
$select_query = mysqli_query($con,"select * from doshii_shop_details_tbl where shopify_url = '".$shop."'");
$shopify_row = mysqli_num_rows($select_query);
$result=mysqli_fetch_object($select_query);

$productCount = mysqli_query($con,"SELECT * FROM doshii_product where shop = '".$shop."'" );
$result_productCount = mysqli_num_rows($productCount);
$productCountMessage='';
if($result_productCount > 0){
	$productCountMessage = $result_productCount.' products synchronised';
}else{
	$productCountMessage = '';
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

    <div id="page-wrapper">
        <div class="container-fluid">
            <!-- Page Heading -->
            <div class="row" id="main" >
                <div class="col-sm-12 col-md-12 well" id="content">

				<div class="message"></div>
                   <form id="doshii_setting_form" method="post">
					  <div class="form-group">
						<label for="">Doshii API Base URL</label>
						<input type="text" class="form-control" name="doshii_base_url" value="<?php if(!empty($result->base_url)){ echo $result->base_url; } ?>" id="doshii_base_url" >
					  </div>
					  <div class="form-group">
						<label for="">Client ID</label>
						<input type="password" class="form-control" value="<?php if(!empty($result->client_id)){ echo $result->client_id; } ?>" id="client_id" name="client_id">
					  </div>
					  <div class="form-group">
						<label for="">Client Secret</label>
						<input type="password" class="form-control" value="<?php if(!empty($result->client_secret)){ echo $result->client_secret; } ?>" id="client_secret" name="client_secret">
					  </div>
					  <div class="form-group">
						<label for="">Location ID:</label>
						<input type="text" class="form-control" value="<?php if(!empty($result->location_id)){ echo $result->location_id; } ?>" id="location_id" name="location_id">
					  </div>
					  
					  <div class="form-group">
						<label for="">Shopify Url:</label>
						<input type="text" class="form-control" value="<?php if(!empty($result->shopify_url)){ echo $result->shopify_url; } ?>" id="shopify_url" name="shopify_url">
						<!--<small>Entery your shopify shop name Example: Url: https://dhosii.myshopify.com , Shopname: dhosii</small>-->
					  </div>
					  
					  <div class="form-group">
						<label for="">Use Menu Management:</label>
						&nbsp;&nbsp;<input id="use_menu_management" name="use_menu_management" type="checkbox" <?php if(!empty($result->menu_managment)){ echo 'checked'; } ?> value="yes" />

					  </div>
					  
					  <input type="hidden" value="doshii_setting" name="action">
					 <!-- <div class="form-group form-check">
						<input type="checkbox" class="form-check-input" id="exampleCheck1">
						<label class="form-check-label" for="exampleCheck1">Check me out</label>
					  </div>-->
					  <button type="button" id="setting_btn" class="btn btn-primary">SAVE</button>
					</form>
                </div>
				
				 <div class="col-sm-12 col-md-12 well" id="content1">
				  <img src="https://i.gifer.com/origin/34/34338d26023e5515f6cc8969aa027bca_w200.gif" style="display:none" id="processing">
				  <p class="sync_message"></p>
				    <button type="button"  shop="<?php if(!empty($result->shopify_url)){ echo $result->shopify_url; } ?>" id="product_sync_btn" class="btn btn-primary">Sync Doshii Product</button> &nbsp; <?php echo $productCountMessage; ?>
				 </div>
            </div> 
            <!-- /.row -->
        </div>
		
    
    </div>
    <!-- /#page-wrapper -->
	
<?php
include('../footer.php');
?>