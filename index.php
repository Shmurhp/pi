<?php
session_start();
$_SESSION["favcolor"] = "green";

echo 
'
<form action="group.php" method="post">
Group ID: <input type="text" name="group_id"><br />
<input type="submit">
</form>
'

?>
