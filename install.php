<?php

// Set variables for our request
$shop = $_GET['shop'];
$api_key = "22d574aee2e8094e87cbfd2e91514448";
$scopes = "read_orders,write_orders,write_products,read_products";
$redirect_uri = "https://frontendninjas.com/doshii_app/generate_token.php";

// Build install/approval URL to redirect to
$install_url = "https://" . $shop . ".myshopify.com/admin/oauth/authorize?client_id=" . $api_key . "&scope=" . $scopes . "&redirect_uri=" . urlencode($redirect_uri);

// Redirect
header("Location: " . $install_url);
die();