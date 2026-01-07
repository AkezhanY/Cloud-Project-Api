<?php
function getDataWithCurl($url) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    $response = curl_exec($ch);
    curl_close($ch);
    return json_decode($response, true);
}

$api_url = "https://cloud-project-api-3.onrender.com";

$department = getDataWithCurl($api_url . '/department.php');
$hourly = getDataWithCurl($api_url . '/hourly.php');
$budget = getDataWithCurl($api_url . '/budget.php');
$clients = getDataWithCurl($api_url . '/clients.php');
$usd = getDataWithCurl('https://api.nbp.pl/api/exchangerates/rates/a/usd/last/20/?format=json');
$chf = getDataWithCurl('https://api.nbp.pl/api/exchangerates/rates/a/chf/last/20/?format=json');
?>
