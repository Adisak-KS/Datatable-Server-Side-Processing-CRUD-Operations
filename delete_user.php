<?php
include('connection.php');

$id = $_POST['id'];

$sql = "DELETE FROM users WHERE id = '$id'";
$query = mysqli_query($con, $sql);

if ($query) {
    $data = array('status' => 'success');
} else {
    $data = array('status' => 'failed');
}
echo json_encode($data);
