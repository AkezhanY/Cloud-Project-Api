<?php
// Функция для получения данных через CURL
function fetchWithCurl($url) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    $response = curl_exec($ch);
    curl_close($ch);
    return json_decode($response, true);
}

// Получаем данные с твоих API через CURL
$departmentData = fetchWithCurl('https://cloud-project-api-3.onrender.com/department.php');
$hourlyData = fetchWithCurl('https://cloud-project-api-3.onrender.com/hourly.php');
$budgetData = fetchWithCurl('https://cloud-project-api-3.onrender.com/budget.php');
$clientsData = fetchWithCurl('https://cloud-project-api-3.onrender.com/clients.php');

// Получаем данные валют
$usdData = fetchWithCurl('https://api.nbp.pl/api/exchangerates/rates/a/usd/last/20/?format=json');
$chfData = fetchWithCurl('https://api.nbp.pl/api/exchangerates/rates/a/chf/last/20/?format=json');
?>