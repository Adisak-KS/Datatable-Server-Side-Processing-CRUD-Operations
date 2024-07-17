<?php
include('connection.php');

// เริ่มต้นคำสั่ง SQL
$sql = "SELECT * FROM users";
$query = mysqli_query($con, $sql);
$count_all_rows = mysqli_num_rows($query);

// การค้นหา
if (isset($_POST['search']['value'])) {
    $search_value = $_POST['search']['value'];
    $sql .= " WHERE username LIKE '%" . $search_value . "%' ";
    $sql .= " OR email LIKE '%" . $search_value . "%' ";
    $sql .= " OR mobile LIKE '%" . $search_value . "%' ";
    $sql .= " OR city LIKE '%" . $search_value . "%' ";
}

// การจัดเรียงข้อมูล
if (isset($_POST['order'])) {
    $column = $_POST['order'][0]['column'];
    $order = $_POST['order'][0]['dir'];
    $sql .= " ORDER BY " . $column . " " . $order;
} else {
    $sql .= " ORDER BY id ASC";
}

// การจำกัดจำนวนแถวที่แสดงผล
if (isset($_POST['length']) && $_POST['length'] != -1) {
    $start = isset($_POST['start']) ? $_POST['start'] : 0;
    $length = $_POST['length'];
    $sql .= " LIMIT " . $start . ", " . $length;
}

$data = array();
$run_query = mysqli_query($con, $sql);
$filtered_rows = mysqli_num_rows($run_query);

while ($row = mysqli_fetch_array($run_query)) {
    $subarray = array();
    $subarray[] = $row['id'];
    $subarray[] = $row['username'];
    $subarray[] = $row['email'];
    $subarray[] = $row['mobile'];
    $subarray[] = $row['city'];
    $subarray[] = '<a href="javascript:void(0);" data-id="'. $row['id'].'" class="btn btn-sm btn-info editBtn">Edit</a> <a href="javascript:void(0);" data-id="'. $row['id'].'" class="btn btn-sm btn-danger btnDelete">Delete</a>';
    $data[] = $subarray;
}

$output = array(
    'data' => $data,
    'draw' => isset($_POST['draw']) ? $_POST['draw'] : 0,
    'recordsTotal' => $count_all_rows,
    'recordsFiltered' => $filtered_rows,
);

echo json_encode($output);
?>
