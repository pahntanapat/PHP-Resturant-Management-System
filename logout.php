<?php
require 'class.Session.php';
Session::destroy();
header("Location: login.php");
?>
Log out สำเร็จ