<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header('Content-Type: application/json');

$data = [
    ["department" => "Marketing", "sales" => 15000],
    ["department" => "Sales", "sales" => 22000],
    ["department" => "IT", "sales" => 18000],
    ["department" => "Support", "sales" => 9000],
    ["department" => "HR", "sales" => 12000],
    ["department" => "Finance", "sales" => 16000],
    ["department" => "Legal", "sales" => 8000],
    ["department" => "Operations", "sales" => 11000]
];

echo json_encode($data);
?>
