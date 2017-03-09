<?php
session_id('test');
session_start();

file_put_contents("/tmp/post", var_export($_POST, true));


// If this data is stored in the DB, oauth_consumer_key can be used as ID to retrieve this data later in "checklogin.php"
// For simplicity of this sample, it is stored in session
$_SESSION['oauth_consumer_key'] = $_POST['oauth_consumer_key'];
$_SESSION['oauth_consumer_secret'] = $_POST['oauth_consumer_secret'];
$_SESSION['store_base_url'] = $_POST['store_base_url'];
$_SESSION['oauth_verifier'] = $_POST['oauth_verifier'];

session_write_close();

header("HTTP/1.0 200 OK");
echo "Response";