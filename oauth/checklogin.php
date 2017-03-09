<?php
require '../vendor/autoload.php';

session_id('test');
session_start();
?>

<table border="1">
    <tr>
        <td>oauth_consumer_key</td>
        <td><?php echo isset($_SESSION['oauth_consumer_key']) ? $_SESSION['oauth_consumer_key'] : "" ?></td>
    </tr>
    <tr>
        <td>oauth_consumer_secret</td>
        <td><?php echo isset($_SESSION['oauth_consumer_secret']) ? $_SESSION['oauth_consumer_secret'] : "" ?></td>
    </tr>
    <tr>
        <td>store_base_url</td>
        <td><?php echo isset($_SESSION['store_base_url']) ? $_SESSION['store_base_url'] : "" ?></td>
    </tr>
    <tr>
        <td>oauth_verifier</td>
        <td><?php echo isset($_SESSION['oauth_verifier']) ? $_SESSION['oauth_verifier'] : "" ?></td>
    </tr>
</table>

<?php
$consumerKey = $_REQUEST['oauth_consumer_key'];
$callback = $_REQUEST['callback_url'];

var_dump($_SESSION['oauth_consumer_key']);
/** Use $consumerKey to retrieve the following data in case it was stored in DB when received at "endpoint.php" */
if ($consumerKey !== $_SESSION['oauth_consumer_key']) {
    throw new \Exception("Consumer keys received on on different requests do not match.");
}

$consumerSecret = $_SESSION['oauth_consumer_secret'];
$magentoBaseUrl = rtrim($_SESSION['store_base_url'], '/');
$oauthVerifier = $_SESSION['oauth_verifier'];

define('TESTS_BASE_URL', $magentoBaseUrl);

$credentials = new \OAuth\Common\Consumer\Credentials($consumerKey, $consumerSecret, $magentoBaseUrl);
$oAuthClient = new LeBorzoi\OauthClient($credentials, $_SESSION['store_base_url']);
$requestToken = $oAuthClient->requestRequestToken();
$accessToken = $oAuthClient->requestAccessToken(
    $requestToken->getRequestToken(),
    $oauthVerifier,
    $requestToken->getRequestTokenSecret()
);
$_SESSION['request_token'] = $accessToken->getRequestToken();

header("location: $callback");