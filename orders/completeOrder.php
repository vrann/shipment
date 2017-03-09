<?php
/**
 * Created by PhpStorm.
 * User: etulika
 * Date: 2/21/17
 * Time: 5:43 AM
 */
session_id('test');
session_start();
$orderId = (int)$_REQUEST['order_id'];

$ch = curl_init($_SESSION['store_base_url'] . 'rest/V1/orders/' . $orderId);

$headers = array(
    "Accept: application/json",
    "Authorization: Bearer " . $_SESSION['request_token']
);

curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$data = curl_exec($ch);

if (curl_errno($ch)) {
    print "Error: " . curl_error($ch);
} else {
    // Show me the result
    $data = json_decode($data, true);
    curl_close($ch);
}
$data['status'] = 'complete';
$data['state'] = 'complete';
unset($data['payment']['additional_information']);
$data["entity"] = $data;

$body = json_encode($data);
$ch = curl_init($_SESSION['store_base_url'] . 'rest/V1/orders/create');
$headers = array(
    "Accept: application/json",
    "Authorization: Bearer " . $_SESSION['request_token'],
    'Content-Type: application/json',
    'Content-Length: ' . strlen($body)
);

curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
//curl_setopt($ch, CURLOPT_PUT, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
$data = curl_exec($ch);

if (curl_errno($ch)) {
    print "Error: " . curl_error($ch);
    die();
} else {
    // Show me the result
    $data = json_decode($data, true);
    curl_close($ch);
}

header("location: ../orders");