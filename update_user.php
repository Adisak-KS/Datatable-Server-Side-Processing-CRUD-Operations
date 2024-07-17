<?php
include('connection.php');

$id = $_POST['id'];
$username = $_POST['username'];
$email = $_POST['email'];
$mobile = $_POST['mobile'];
$city = $_POST['city'];

$sql = "UPDATE users
        SET username = '$username',
            email = '$email',
            mobile = '$mobile',
            city = '$city'
        WHERE id = '$id'";
$query = mysqli_query($con, $sql);

if ($query) {
    $data = array(
        'status' => 'success',
    );
    echo json_encode($data);
} else {
    $data = array(
        'status' => 'failed',
    );
    echo json_encode($data);
}
