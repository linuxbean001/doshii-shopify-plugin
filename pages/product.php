<?php
include('../header.php');
?>
<?php 
$shop = $_REQUEST['shop']; 
$select_query = mysqli_query($con,"select * from doshii_shop_details_tbl where shopify_url = '".$shop."'");
$shopify_row = mysqli_num_rows($select_query);
$result=mysqli_fetch_object($select_query);
$shopDetails = explode(".",$sessionShop);
$shop = $shopDetails[0];
$token = $result->access_token;
// Set variables for our request
$requests = $_GET;
$serializeArray = serialize($requests);
$requests = array_diff_key($requests, array('hmac' => '')); // Remove hmac from params
krsort($requests);	
//$shop = "dhosii";
//$token = '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';
$query = array(
	"Content-type" => "application/json" 
);
$products = shopify_call($token, $shop, "/admin/api/2020-04/products.json", array(), 'GET');
$products = json_decode($products['response'], TRUE);
 
//echo '<pre>'; print_r($products); echo '</pre>';
?>
				
<div id="page-wrapper">
	<div class="container-fluid">
		<!-- Page Heading -->
		<div class="row" id="main" >
			<div class="col-sm-12 col-md-12 well" id="content">
			<table class="table table-striped">
			  <thead>
				<tr style="background:#428bca;color: #000;">
				  <th scope="col">#</th>
				  <th scope="col">Product Name</th>
				  <th scope="col">Image</th>
				  <th scope="col">POS Product ID</th>
				  <th scope="col">POS Product Name</th>
				  <th scope="col"></th>
				</tr>
			  </thead>
			  <tbody>
			  <?php $i=1; foreach($products['products'] as $product){
				$doshii_query = mysqli_query($con,"select * from product_sync_tbl where shopify_pid = '".$product['id']."'");
				$shopify_row = mysqli_num_rows($doshii_query);
				$doshii_result=mysqli_fetch_object($doshii_query);
				$product_doshii_name='';
				if(!empty($doshii_result->doshii_pid)){
				$doshii_name_query = mysqli_query($con,"select * from doshii_product where doshii_product_id = '".$doshii_result->doshii_pid."'");
				$shopify_row = mysqli_num_rows($doshii_name_query);
				$doshiip_result=mysqli_fetch_object($doshii_name_query);
				$product_doshii_name = $doshiip_result->doshii_product_name;
				}

			  ?>
				<tr>
				  <th scope="row"><?php echo $i; ?></th>
				  <td><?php echo $product['title']; ?></td>
				  <td><img width="50px" src="<?php echo $product['image']['src']; ?>"></td>
				  <td><?php if(!empty($doshii_result->doshii_pid)){ echo $doshii_result->doshii_pid; } ?></td>
				  <td><?php if(!empty($product_doshii_name)){ echo $product_doshii_name; } ?></td>
				  <td><form method="POST" action="product_edit.php"><input type="hidden" value="<?php echo $product['id']; ?>" name="product_id"><input type="hidden" value="<?php echo $sessionShop; ?>" name="shop"><input type="hidden" value="<?php echo $token; ?>" name="token"><button type="submit" class="btn btn-primary">Edit</button></form></td>
				</tr>
			  <?php $i++; } ?>
			  </tbody>
			</table>
			</div>
		</div>
	</div>
</div>			

<?php
include('../footer.php');
?>