<?php
/**
 * Created by PhpStorm.
 * User: etulika
 * Date: 2/21/17
 * Time: 5:10 AM
 */
session_id('test');
session_start();
$orderId = (int)$_REQUEST['order_id'];
$trackingNumber = $_REQUEST['tracking'];

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

$ch = curl_init($_SESSION['store_base_url'] . 'rest/V1/order/' . $orderId. '/ship');
$headers = array(
    "Accept: application/json",
    "Authorization: Bearer " . $_SESSION['request_token'],
    'Content-Type: application/json'
);

$body['items'] = [];
foreach ($data['items'] as $item) {
    $body['items'][] = [
        "extension_attributes" => [],
        "order_item_id" => $item['item_id'],
        "qty" => $item['qty_ordered'],
    ];
}

$body['notify'] = true;
$body['appendComment'] = true;
$body['comment'] = [
    "extension_attributes" => [],
    "comment" => "Shipped via Shipment Provider",
    "is_visible_on_front" => 0,
];
$body['tracks'] = [
    [
        "extension_attributes" => [],
        "track_number" => $trackingNumber,
        "title" => "fedex",
        "carrier_code" => "fedex"
    ]
];
$body['packages'] = [
        ["extension_attributes" => []]
    ];
$body['arguments'] = [
    "extension_attributes" => [],
];
$body = json_encode($body);



curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
$data = curl_exec($ch);

if (curl_errno($ch)) {
    print "Error: " . curl_error($ch); die();
} else {
    // Show me the result
    $data = json_decode($data, true);
    var_dump($data);
    curl_close($ch);
}

header("location: ../orders");