<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header('Content-Type: application/json');

$data = [
    ["hour" => 9, "users" => 120],
    ["hour" => 10, "users" => 250],
    ["hour" => 11, "users" => 310],
    ["hour" => 12, "users" => 280],
    ["hour" => 13, "users" => 200],
    ["hour" => 14, "users" => 270],
    ["hour" => 15, "users" => 320],
    ["hour" => 16, "users" => 300],
    ["hour" => 17, "users" => 350],
    ["hour" => 18, "users" => 400],
    ["hour" => 19, "users" => 450],
    ["hour" => 20, "users" => 500]
];

echo json_encode($data);
?>
