<?php
session_id('test');
session_start();
?>

<table border="1">
    <tr>
        <td>oAuth request token</td>
        <td><?php echo $_SESSION['request_token']; ?>
        </td>
    </tr>
</table>

