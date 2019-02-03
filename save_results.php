<?php
	session_start();
	print "hey";
	$feedback = $_POST['feedback'];
	$group_id = $_SESSION['group_id'];
	$global_var = $_POST['global_var'];
	echo $feedback . ' is here!';
	echo $group_id . ' is here!';
	var_dump($_SESSION);
	
	$servername = "localhost";
$username = "root";
$password = "root";
$dbname = "pi";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "INSERT INTO pi.pi (group_id, feedback)
VALUES ('" . $group_id . "', '" . $feedback . "')";

if ($conn->query($sql) === TRUE) {
    echo "New record created successfully";
} else {
    echo "Error: " . $sql . "<br>" . $conn->error;
}

$conn->close();
?>
