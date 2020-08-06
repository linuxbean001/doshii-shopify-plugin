<?php include('../header.php'); ?>
 <div id="page-wrapper">
        <div class="container-fluid">
            <!-- Page Heading -->
            <div class="row" id="main" >
                <div class="col-sm-12 col-md-12 well" id="content">
				<?php 
				    $webhook_content = NULL;
					 $webhook = fopen('php://input' , 'rb');
					 while (!feof($webhook)) {
					 $webhook_content .= fread($webhook, 4096);
					 }
					 fclose($webhook);
					 // Decode Shopify POST
					 $webhook_data = json_decode($webhook_content, TRUE);
					 $webhook_ordercontent1 = json_encode($webhook_content, TRUE);
					 //echo '<pre>'; print_r($webhook_data); echo '</pre>';
					 
					 //$query="INSERT INTO shopdetails(`details`) VALUES ('".$webhook_ordercontent1."')";
                     //mysqli_query($con,$query);
					 
	
					 $query1="INSERT INTO cus_request_data(`shop_id`,`shop_domain`,`customer_id`,`email`,`phone`,`orders_requested`) 
					 VALUES ('".$webhook_data['shop_id']."','".$webhook_data['shop_domain']."','".$webhook_data['customer']['id']."','".$webhook_data['customer']['email']."','".$webhook_data['customer']['phone']."','".serialize($webhook_data['orders_requested'])."')";
                     mysqli_query($con,$query1);

					    $to_email = $webhook_data['customer']['email'];
						$subject = 'GDPR Request for customer information';
						$message = 'We are requesting to view our data in shopify store.';
						mail($to_email,$subject,$message);

  
				?>
				</div>
			</div>
		</div>
</div>		
<?php include('../footer.php'); ?>