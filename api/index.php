<?php
header("Content-Type: application/json");
include "../../config/db.php";

$data = [];
$q = mysqli_query($conn, "SELECT * FROM barang");

while ($row = mysqli_fetch_assoc($q)) {
    $data[] = $row;
}

echo json_encode([
    "status" => true,
    "data" => $data
]);
