<?php include('config.php');
require_once("inc/functions.php");
/*echo '<pre>';
print_r($_REQUEST);
echo '</pre>';*/
$sessionShop = $_REQUEST['shop'];
if(isset($_GET['action'])){
 $action = $_GET['action'];
if($action == 'hooks'){
	$select_query = mysqli_query($con,"select * from doshii_shop_details_tbl where shopify_url = '".$sessionShop."'");
    $shopify_row = mysqli_num_rows($select_query);
    $result=mysqli_fetch_object($select_query);
	$shopDetails = explode(".",$sessionShop);
	 $shop = $shopDetails[0];
     $token = $result->access_token;
	$arguments =array(
	  "webhook"=> array(
		"topic"=>  "orders/create",
		"address"=>  "https://frontendninjas.com/doshii_app/pages/function.php?action=new_order&shop=".$shop."",
		"format"=>  "json"
	  )
	);
	$modified_webhook = shopify_call($token, $shop, "/admin/api/2020-04/webhooks.json", $arguments, 'POST');
	//$modified_webhook1 = shopify_call($token, $shop, "/admin/api/2020-04/webhooks.json", array(), 'GET');
	//echo '<pre>'; print_r($modified_webhook1); echo '</pre>';
	
	//header('Location: https://'.$sessionShop.'/admin/apps/doshiiapp');
}
}
 ?>
<html>

<link href="https://select2.org/assets/c46e5285ab2e44facce8fa6d6ab2b4b1.css" rel="stylesheet" id="bootstrap-css">
<link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.0/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
<link href="https://frontendninjas.com/doshii_app/assets/css/dashborard.css" rel="stylesheet" id="bootstrap-css">
<link href="https://frontendninjas.com/doshii_app/assets/css/styles.css" rel="stylesheet" id="bootstrap-css">
<!------ Include the above in your HEAD tag ---------->

<div id="throbber" style="display:none; min-height:120px;"></div>
<div id="noty-holder"></div>
<div id="wrapper">
    <!-- Navigation -->
    <nav class="navbar navbar-inverse navbar-fixed-top" role="navigation">
        <!-- Brand and toggle get grouped for better mobile display -->
        <div class="navbar-header">
           <!-- <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-ex1-collapse">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="https://bryanrojasq.wordpress.com">
                <img src="http://placehold.it/200x50&text=LOGO" alt="LOGO">
            </a>-->
        </div>
        <!-- Top Menu Items -->
       <!-- <ul class="nav navbar-right top-nav">
            <li><a href="#" data-placement="bottom" data-toggle="tooltip" href="#" data-original-title="Stats"><i class="fa fa-bar-chart-o"></i>
                </a>
            </li>            
            <li class="dropdown">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown">Admin User <b class="fa fa-angle-down"></b></a>
                <ul class="dropdown-menu">
                    <li><a href="#"><i class="fa fa-fw fa-user"></i> Edit Profile</a></li>
                    <li><a href="#"><i class="fa fa-fw fa-cog"></i> Change Password</a></li>
                    <li class="divider"></li>
                    <li><a href="#"><i class="fa fa-fw fa-power-off"></i> Logout</a></li>
                </ul>
            </li>
        </ul>-->
        <!-- Sidebar Menu Items - These collapse to the responsive navigation menu on small screens -->
        <div class="collapse navbar-collapse navbar-ex1-collapse">
            <ul class="nav navbar-nav side-nav">
               <!-- <li>
                    <a href="#" data-toggle="collapse" data-target="#submenu-1"><i class="fa fa-fw fa-search"></i> MENU 1 <i class="fa fa-fw fa-angle-down pull-right"></i></a>
                    <ul id="submenu-1" class="collapse">
                        <li><a href="#"><i class="fa fa-angle-double-right"></i> SUBMENU 1.1</a></li>
                        <li><a href="#"><i class="fa fa-angle-double-right"></i> SUBMENU 1.2</a></li>
                        <li><a href="#"><i class="fa fa-angle-double-right"></i> SUBMENU 1.3</a></li>
                    </ul>
                </li>
                <li>
                    <a href="#" data-toggle="collapse" data-target="#submenu-2"><i class="fa fa-fw fa-star"></i>  MENU 2 <i class="fa fa-fw fa-angle-down pull-right"></i></a>
                    <ul id="submenu-2" class="collapse">
                        <li><a href="#"><i class="fa fa-angle-double-right"></i> SUBMENU 2.1</a></li>
                        <li><a href="#"><i class="fa fa-angle-double-right"></i> SUBMENU 2.2</a></li>
                        <li><a href="#"><i class="fa fa-angle-double-right"></i> SUBMENU 2.3</a></li>
                    </ul>
                </li>
                <li>
                    <a href="investigaciones/favoritas"><i class="fa fa-fw fa-user-plus"></i>  MENU 3</a>
                </li>
                <li>
                    <a href="sugerencias"><i class="fa fa-fw fa-paper-plane-o"></i> MENU 4</a>
                </li>-->
                <li>
                    <a href="/doshii_app/pages/setting.php?shop=<?php echo $sessionShop; ?>"><i class="fa fa-fw fa fa-question-circle"></i> Doshii Setting</a>
                </li>
				<li>
                    <a href="/doshii_app/pages/product.php?shop=<?php echo $sessionShop; ?>"><i class="fa fa-fw fa fa-question-circle"></i> Product Setting</a>
                </li>
				<li>
                    <a href="/doshii_app/pages/cut_request_data.php?shop=<?php echo $sessionShop; ?>"><i class="fa fa-fw fa fa-question-circle"></i> data</a>
                </li>
            </ul>
        </div>
        <!-- /.navbar-collapse -->
    </nav>


