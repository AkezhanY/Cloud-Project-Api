<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header('Content-Type: application/json');

$data = [
    ["category" => "Salaries", "percent" => 40],
    ["category" => "Advertising", "percent" => 20],
    ["category" => "Development", "percent" => 25],
    ["category" => "Office", "percent" => 15],
    ["category" => "Training", "percent" => 5],
    ["category" => "R&D", "percent" => 10],
    ["category" => "IT infrastructure", "percent" => 8],
    ["category" => "Maintenance", "percent" => 12]
];

// Проверяем что сумма = 100
$sum = array_sum(array_column($data, 'percent'));
if ($sum !== 100) {
    // Нормализуем
    foreach ($data as &$item) {
        $item['percent'] = round(($item['percent'] / $sum) * 100);
    }
}

echo json_encode($data);
?>
