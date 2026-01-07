<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header('Content-Type: application/json');

$data = [
    ["type" => "Regular", "count" => 150],
    ["type" => "VIP", "count" => 45],
    ["type" => "New", "count" => 80],
    ["type" => "Companies", "count" => 60],
    ["type" => "Partners", "count" => 30],
    ["type" => "Resellers", "count" => 40],
    ["type" => "Distributors", "count" => 25],
    ["type" => "Government", "count" => 35]
];

echo json_encode($data);
?>
