<?php
include('../header.php');
$productId = $_REQUEST['product_id'];

$sessionShop = $_REQUEST['shop'];
$shopDetails = explode(".",$sessionShop);
$shop = $shopDetails[0];
$token = $_REQUEST['token'];
$requests = $_GET;
$serializeArray = serialize($requests);
$requests = array_diff_key($requests, array('hmac' => '')); // Remove hmac from params
krsort($requests);	
//$shop = "dhosii";
//$token = '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';
$query = array(
	"Content-type" => "application/json" 
);
$products = shopify_call($token, $shop, "/admin/api/2020-04/products/".$productId.".json", array(), 'GET');
$products = json_decode($products['response'], TRUE);
/*echo '<pre>';
print_r($products);
echo '</pre>';*/
	
?>
<div id="page-wrapper">
	<div class="container-fluid">
		<!-- Page Heading -->
		<div class="row" id="main" >
			<div class="col-sm-12 col-md-12 well" id="content">
			<?php //echo $productId; ?>
			   <div class="col-sm-12">
			   

			   <div class="col-sm-8">
			   <?php if($products['product']['variants'][0]['title'] == 'Default Title'){ 
				   $shopify_url_query = mysqli_query($con,"select * from product_sync_tbl where shopify_pid='$productId'");
					$result = mysqli_fetch_object($shopify_url_query);
					if(!empty($result)){ 
						$doshii_pid = $result->doshii_pid;
						$doshii_option_pid = $result->doshii_option_pid;
						$doshii_variant_pid_Array =  unserialize($result->doshii_variant_pid); 
					}else{
						$doshii_pid='';
						$doshii_option_pid='';
						$doshii_variant_pid_Array = [];
					}
			   ?>
			   <p class="message"></p>
					<form style="display: grid;" id="editForm">
					  <div class="form-group">
						<h5 class="product_edit_title"><?php echo $products['product']['title']; ?></h5> 
					  </div>
					  <div class="form-group">
						<label for="email">Doshii Products</label>
						<select  name="doshii_posId" id="doshii_posId" class="form-control select2">
						  <option value="">Select Doshii Product</option>
						<?php     
							 $doshii_sql = "SELECT * FROM doshii_product where shop='".$sessionShop."'";
							 $doshii_results = mysqli_query($con,$doshii_sql); 		
							 if(!empty($doshii_results)){
							 while($row = mysqli_fetch_object($doshii_results)) {             
						?>
						  <option <?php if($doshii_pid == $row->doshii_product_id){ echo 'selected'; } ?> value="<?php echo $row->doshii_product_id; ?>"><?php echo $row->doshii_product_name; ?></option>
							 <?php } } ?>
						</select>
					  </div>
					  <div style="display:<?php if(!empty($result->doshii_option_pid)){ echo 'block'; }else{ echo 'none'; } ?>" class="form-group doshii_option_posId">
						<label for="email">Products Options</label>
						
	
						<select name="doshii_option_posId" id="doshii_option_posId" class="form-control">
						 <option value="">Select Option</option>
						<?php 		  
							 $doshii_option_sql = "SELECT * FROM doshii_product_option where doshii_product_id = '$result->doshii_pid' AND shop='".$sessionShop."'";
							 $doshii_option_results = mysqli_query($con,$doshii_option_sql); 		
							 while($Optiondata = mysqli_fetch_object($doshii_option_results)) { 
						?>
						 <option <?php if($doshii_option_pid == $Optiondata->doshii_product_option_id){ echo 'selected'; } ?> value="<?php echo $Optiondata->doshii_product_option_id; ?>"><?php echo $Optiondata->doshii_product_option_name; ?></option>
					    <?php } ?>
						</select>
					  </div>
					  <div style="display:<?php if(!empty($doshii_variant_pid_Array)){ echo 'block'; }else{ echo 'none'; } ?> " class="form-group doshii_variant_posId">
						<label for="email">Products Variant</label>
						
						<select multiple="multiple" id="doshii_variant_posId" name="doshii_variant_posId[]" class="form-control">
						<?php
						$doshii_variant_sql = "SELECT * FROM doshii_product_variant where doshii_product_option_id=$result->doshii_option_pid AND shop='".$sessionShop."'"; 
						$doshii_variant_results = mysqli_query($con,$doshii_variant_sql); 		
					    while($Variantdata = mysqli_fetch_object($doshii_variant_results)) { 
						$selected = '';
						if (in_array($Variantdata->doshii_product_variant_id, $doshii_variant_pid_Array)){ $selected = 'selected'; }else{ $selected=''; }
						?>
						  <option <?php echo $selected; ?> value="<?php echo $Variantdata->doshii_product_variant_id; ?>"><?php echo $Variantdata->doshii_product_variant_name; ?></option>
						<?php } ?>  
						</select>
					  </div>
					  <input type="hidden" value="<?php echo $products['product']['id']; ?>" name="product_id">
					  <input type="hidden" value="sync_product_add" name="action">
					</form>
					
					<?php }else{ ?>
					
					  <p class="message"></p>
					  <div class="form-group">
						<h5 class="product_edit_title"><?php echo $products['product']['title']; ?></h5> 
					  </div>
					<div id="accordion">
                      <div class="form-group">
						<h6 class="">Variation Details:</h6> 
					  </div>
					 <!-- <form  action="#" id="editForm">-->
					  <?php foreach($products['product']['variants'] as $variantProduct){ 
					  $shopify_url_query = mysqli_query($con,"select * from product_sync_tbl where shopify_variation_id='".$variantProduct['id']."'");
					$result = mysqli_fetch_object($shopify_url_query);
					if(!empty($result)){ 
						$doshii_pid = $result->doshii_pid;
						$doshii_option_pid = unserialize($result->doshii_option_pid);
						$doshii_variant_pid_Array =  unserialize($result->doshii_variant_pid); 
						//print_r($doshii_variant_pid_Array);
					}else{
						$doshii_pid='';
						$doshii_option_pid=[];
						$doshii_variant_pid_Array = [];
					}
					  
					  ?>
					  <form  action="#" id="editForm_<?php echo $variantProduct['id']; ?>">
					  <div class="card">
						<div class="card-header" id="h_<?php echo $variantProduct['id']; ?>">
						  <h5 class="mb-0">
							<a href="javascript:void(0)" class="btn btn-link" data-toggle="collapse" data-target="#<?php echo $variantProduct['id']; ?>" aria-controls="<?php echo $variantProduct['id']; ?>">
							  <?php echo $variantProduct['title']; ?>
							</a>
						  </h5>
						</div>
						<input type="hidden" value="<?php echo $variantProduct['id']; ?>" name="variation_id">

						<div id="<?php echo $variantProduct['id']; ?>" class="collapse " aria-labelledby="h_<?php echo $variantProduct['id']; ?>" data-parent="#accordion">
						  <div class="card-body">
							  <div class="form-group">
								<label for="email">Doshii Products</label>
								<select onchange="variation_doshiParent_id(this,<?php echo $variantProduct['id']; ?>,'<?php echo $sessionShop; ?>')" name="doshii_posId" id="doshii_posId_<?php echo $variantProduct['id']; ?>" class="form-control select2">
								  <option value="">Select Doshii Product</option>
								<?php     
									 $doshii_sql = "SELECT * FROM doshii_product where shop='".$sessionShop."'";
									 $doshii_results = mysqli_query($con,$doshii_sql); 		
									 if(!empty($doshii_results)){
									 while($row = mysqli_fetch_object($doshii_results)) {             
								?>
								  <option <?php if($doshii_pid == $row->doshii_product_id){ echo 'selected'; } ?> value="<?php echo $row->doshii_product_id; ?>"><?php echo $row->doshii_product_name; ?></option>
									 <?php } } ?>
								</select>
							  </div>
							  <div style="display:<?php if(!empty($result->doshii_option_pid)){ echo 'block'; }else{ echo 'none'; } ?>" class="form-group doshii_option_posId_<?php echo $variantProduct['id']; ?>">
								<label for="email">Products Options</label>
							
								<select multiple="multiple" onchange="variation_doshivarient_id(this,<?php echo $variantProduct['id']; ?>,'<?php echo $sessionShop; ?>')" name="doshii_option_posId[]" id="doshii_option_posId_<?php echo $variantProduct['id']; ?>" class="js-example-tags form-control">
								
								<?php 		  
									 $doshii_option_sql = "SELECT * FROM doshii_product_option where doshii_product_id = '$result->doshii_pid' AND shop='".$sessionShop."'";
									 $doshii_option_results = mysqli_query($con,$doshii_option_sql); 		
									 while($Optiondata = mysqli_fetch_object($doshii_option_results)) { 
									
								?>
								 <option <?php if (in_array($Optiondata->doshii_product_option_id, $doshii_option_pid)){  echo 'selected'; }else{ echo ''; }; ?> <?php //if($doshii_option_pid == $Optiondata->doshii_product_option_id){ echo 'selected'; } ?> value="<?php echo $Optiondata->doshii_product_option_id; ?>"><?php echo $Optiondata->doshii_product_option_name; ?></option>
								<?php } ?>
								</select>
							  </div>
							  <div style="display:<?php if(!empty($doshii_variant_pid_Array)){ echo 'block'; }else{ echo 'none'; } ?> " class="form-group doshii_variant_posId_<?php echo $variantProduct['id']; ?>">
								<label for="email">Products Variant</label>
								<?php //print_r($doshii_variant_pid_Array); ?>
								<select multiple="multiple" id="doshii_variant_posId_<?php echo $variantProduct['id']; ?>" name="doshii_variant_posId[]" class="js-example-tags form-control">
								<?php
								$doshii_option_pid_List = implode(', ', $doshii_option_pid); 
								$doshii_variant_sql = "SELECT * FROM doshii_product_variant where doshii_product_option_id IN ($doshii_option_pid_List) AND shop='".$sessionShop."'"; 
								$doshii_variant_results = mysqli_query($con,$doshii_variant_sql); 		
								while($Variantdata = mysqli_fetch_object($doshii_variant_results)) { 
							
								?>
								  <option <?php if(in_array($Variantdata->doshii_product_variant_id, $doshii_variant_pid_Array)){  echo 'selected'; }else{ echo ''; }; ?> <?php //if($doshii_variant_pid_Array == $Variantdata->doshii_product_variant_id){ echo 'selected'; } ?> value="<?php echo $Variantdata->doshii_product_variant_id; ?>"><?php echo $Variantdata->doshii_product_variant_name; ?></option>
								<?php } ?>  
								</select>
							  </div>
							  
							  
							  <input type="hidden" value="<?php echo $products['product']['id']; ?>" name="product_id">
							  <input type="hidden" value="sync_variation_product_add" name="action">
							  <button type="button" var_id="<?php echo $variantProduct['id']; ?>" id="" class="btn btn-primary btn-lg product_variation_edit_submit">Save</button>
							
							
						  </div>
						</div>
						   
					  </div>
					 
					  </form>
					  <?php } ?>
					  <!--<input type="hidden" value="<?php echo $products['product']['id']; ?>" name="product_id">
							  <input type="hidden" value="sync_variation_product_add" name="action">
					  </form>-->
					</div>

					<?php } ?>
					
				</div>
				<div class="col-sm-4">
				  <div class="form-group">
					<img class="product_edit_image" src="<?php echo $products['product']['image']['src']; ?>"/>	 
				  </div>
				  <?php if($products['product']['variants'][0]['title'] == 'Default Title'){  ?>
				  <div class="form-group">
					<button type="submit" id="product_edit_submit" class="btn btn-primary btn-lg product_edit_submit">Save</button>
				  </div>
				  <?php } ?>
				   <div class="form-group">
					<a href="product.php?shop=<?php echo $sessionShop; ?>" id="back_list" class="btn btn-primary btn-lg product_edit_submit">&#8592; Back</a>
				  </div>
				</div>
               </div>		
			</div>
		</div>
	</div>
</div>			

<?php
include('../footer.php');
?>