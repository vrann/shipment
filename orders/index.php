<?php
session_id('test');
session_start();

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
  <meta name="description" content="">
  <meta name="author" content="">
  <link rel="icon" href="../../favicon.ico">

  <title>Dashboard Template for Bootstrap</title>
  <!-- Bootstrap core CSS -->
  <link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet">

  <!-- Custom styles for this template -->
  <link href="dashboard.css" rel="stylesheet">

  <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
  <!--[if lt IE 9]>
  <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
  <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
  <![endif]-->
</head>

<body>

<nav class="navbar navbar-inverse navbar-fixed-top">
  <div class="container-fluid">
    <div class="navbar-header">
      <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
        <span class="sr-only">Toggle navigation</span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
      </button>
      <a class="navbar-brand" href="#">Project name</a>
    </div>
    <div id="navbar" class="navbar-collapse collapse">
      <ul class="nav navbar-nav navbar-right">
        <li><a href="#">Dashboard</a></li>
        <li id="orders"><a href="#">Orders</a></li>
        <li id="profile"><a href="#">Profile</a></li>
        <li class="dropdown">
          <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Dropdown <span class="caret"></span></a>
          <ul class="dropdown-menu">
            <li id="integration"><a href="#">Integration</a></li>
          </ul>
        </li>
        <li><a href="#">Help</a></li>
      </ul>
      <form class="navbar-form navbar-right">
        <input type="text" class="form-control" placeholder="Search...">
      </form>
    </div>
  </div>
</nav>

<div class="container-fluid">
  <div class="row">
    <div class="col-sm-3 col-md-2 sidebar">
      <ul class="nav nav-sidebar">
        <li class="active"><a href="#">Overview <span class="sr-only">(current)</span></a></li>
        <li><a href="#">Reports</a></li>
        <li><a href="#">Analytics</a></li>
        <li><a href="#">Export</a></li>
      </ul>

    </div>
    <div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main">
      <h2 class="sub-header">Pending Orders</h2>
      <div class="table-responsive">
        <table class="table table-striped">
          <thead>
          <tr>
            <th>#</th>
            <th>Updated</th>
            <th>Customer</th>
            <th>Address</th>
            <th>Order Items</th>
            <th>Status</th>
            <th>Actions</th>
          </tr>
          </thead>
          <tbody>
      <?php
          $ch = curl_init($_SESSION['store_base_url'] . '/rest/V1/orders?searchCriteria[filterGroups][0][filters][0][field]=status&searchCriteria[filterGroups][0][filters][0][conditionType]=in&searchCriteria[filterGroups][0][filters][0][value]=pending,processing');
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
          if (isset($data['items'])) {
            foreach ($data['items'] as $order) {
              $id = $order["entity_id"];
              $address =
                  $order['extension_attributes']['shipping_assignments'][0]['shipping']['address']['street'][0] . ", " .
                  $order['extension_attributes']['shipping_assignments'][0]['shipping']['address']['postcode'] . ", " .
                  $order['extension_attributes']['shipping_assignments'][0]['shipping']['address']['city'] . ", " .
                  $order['extension_attributes']['shipping_assignments'][0]['shipping']['address']['region'] . ", " .
                  $order['extension_attributes']['shipping_assignments'][0]['shipping']['address']['country_id'] . ", " .
                  $order['extension_attributes']['shipping_assignments'][0]['shipping']['address']['telephone'];

              $items = "";
              foreach ($order['items'] as $item) {
                $items .= $item['name'] . ", $" . $item['price'] . ", qty:" . $item['qty_ordered'] . ", sku:" . $item['sku'] . ", weight:" . $item['row_weight'] . "lbs\n";
              }

              $customer = $order['billing_address']["firstname"] . " " . $order['billing_address']["lastname"] . ", " . $order['billing_address']["email"];
              $date = $order['updated_at'];
              $buttons = "<input type=\"hidden\" id=\"order_id_{$id}\" value=\"{$id}\">";
              if ($order['status'] == 'pending') {
                $buttons .= "<button type=\"button\" class=\"btn btn-primary\" data-toggle=\"modal\" data-order-id=\"order_id_{$id}\" data-target=\"#myModal\">Add Tracking Number</button>";
              }
              $buttons .= "<button id=\"complete_order\" type=\"button\" data-order-id=\"order_id_{$id}\" class=\"btn btn-success\">Complete</button>";

              echo "<tr>
              <td>{$id}</td>
              <td>{$date}</td>
              <td>{$customer}</td>
              <td>{$address}</td>
              <td>{$items}</td>
              <td>{$order['status']}</td>
              <td>
                  {$buttons}
              </td>
            </tr>";
            }
          }
      ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
<!-- Modal -->
<div id="myModal" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Add Tracking Number</h4>
      </div>
      <div class="modal-body">
        <input type="hidden" id="order_id" value="">
        <input type="text" id="tracking_number" class="form-control" placeholder="Tracking Number" autofocus>
        <button id="ship" class="btn btn-lg btn-primary">Add Tracking Number</button>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

<!-- Bootstrap core JavaScript
================================================== -->
<!-- Placed at the end of the document so the pages load faster -->
<script
    src="https://code.jquery.com/jquery-3.1.1.min.js"
    integrity="sha256-hVVnYaiADRTO2PzUGmuLJr8BLUSjGIZsDYGmIJLv2b8="
    crossorigin="anonymous"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
<script src="../js/main.js"></script>
</body>
</html>
