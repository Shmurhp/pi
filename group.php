<?php
session_start();
$_SESSION["group_id"] = $_POST['group_id'];

header('Location: test.html');

?>
