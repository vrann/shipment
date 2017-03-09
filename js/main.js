/**
 * Created by etulika on 2/17/17.
 */
$( document ).ready(function() {
    $("#login").click(function (e) {
        location.href = "../profile";
    });
    $("#profile").click(function (e) {
        location.href = "../profile";
    });
    $("#orders").click(function (e) {
        location.href = "../orders";
    });
    $("#integration").click(function (e) {
        location.href = "../integration";
    });
    $("#complete_order").click(function (e) {
        var orderId = $("#complete_order").data('order-id');
        location.href = "../orders/completeOrder.php?order_id=" + $("#"+orderId).val();
    });
    $("#deleteIntegration").click(function (e) {
        location.href = "../integration/deleteIntegration.php";
    });
    $('#myModal').on('show.bs.modal', function(e) {
        var orderId = $(e.relatedTarget).data('order-id');
        $("#order_id").val($("#"+orderId).val());
    });
    $("#ship").click(function (e) {
        var url = "ship.php?order_id=" + $("#order_id").val() + "&tracking=" + $("#tracking_number").val();
        console.log(url);
        location.href = url;
    });
});

