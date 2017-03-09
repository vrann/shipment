<?php
/**
 * Created by PhpStorm.
 * User: etulika
 * Date: 2/21/17
 * Time: 11:32 AM
 */
session_id('test');
session_start();
session_destroy();
header("location: ../integration");