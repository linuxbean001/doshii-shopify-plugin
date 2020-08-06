<?php
include('header.php');
?>

   <div id="page-wrapper">
        <div class="container-fluid">
            <!-- Page Heading -->
            <div class="row" id="main" >
                <div class="col-sm-12 col-md-12 well" id="content">
                    <h1>Welcome Admin!</h1>
					
					<?php 
		
/*	$select_query = mysqli_query($con,"select * from doshii_shop_details_tbl where shopify_url = '".$sessionShop."'");
    $shopify_row = mysqli_num_rows($select_query);
    $result=mysqli_fetch_object($select_query);
	echo '<pre>'; print_r($result); echo '</pre>';
	$shopDetails = explode(".",$sessionShop);
	 $shop = $shopDetails[0];
	  $token = $result->access_token;
	$modified_webhook1 = shopify_call($token, $shop, "/admin/api/2020-04/webhooks.json", array(), 'GET');
	echo '<pre>'; print_r($modified_webhook1); echo '</pre>';
	
	
 $webhook_content = NULL;
 $webhook = fopen('php://input' , 'rb');
 while (!feof($webhook)) {
 $webhook_content .= fread($webhook, 4096);
 }
 fclose($webhook);
 // Decode Shopify POST
 $webhook_ordercontent = json_decode($webhook_content, TRUE);
 echo '<pre>'; print_r($webhook_ordercontent); echo '</pre>';
*/
					?>
            </div>
            </div>
            <!-- /.row -->
        </div>
        <!-- /.container-fluid -->
    </div>
    <!-- /#page-wrapper -->
	
<?php
include('footer.php');
?>
 