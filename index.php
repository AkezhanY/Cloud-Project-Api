<!DOCTYPE html>
<?php
date_default_timezone_set("Europe/Warsaw");

/* ===================== CURL FUNCTION ===================== */
function fetchApi($url) {
    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT => 10,
        CURLOPT_SSL_VERIFYPEER => false
    ]);
    $res = curl_exec($ch);
    curl_close($ch);
    return json_decode($res, true) ?? [];
}

/* ===================== NBP CURRENCY ===================== */
function getRates($currency) {
    $url = "https://api.nbp.pl/api/exchangerates/rates/a/$currency/last/20/?format=json";
    return fetchApi($url);
}

/* ===================== ТВОИ API URL ===================== */
$BASE = "https://cloud-project-api-3.onrender.com";

/* ===================== FETCH ТВОИХ API ===================== */
$departmentData = fetchApi("$BASE/department.php");
$hourlyData = fetchApi("$BASE/hourly.php");
$budgetData = fetchApi("$BASE/budget.php");
$clientsData = fetchApi("$BASE/clients.php");

/* ===================== ДАННЫЕ ВАЛЮТ ===================== */
$usd = getRates("usd");
$chf = getRates("chf");

/* ===================== ПОДГОТОВКА ДАННЫХ ===================== */
// Если API не работают, используем демо-данные
if (!$departmentData) {
    $departmentData = [
        ["department" => "Marketing", "sales" => 15000],
        ["department" => "Sales", "sales" => 22000],
        ["department" => "IT", "sales" => 18000],
        ["department" => "Support", "sales" => 9000]
    ];
}

if (!$hourlyData) {
    $hourlyData = [
        ["hour" => 9, "users" => 120],
        ["hour" => 10, "users" => 250],
        ["hour" => 11, "users" => 310],
        ["hour" => 12, "users" => 280],
        ["hour" => 13, "users" => 200],
        ["hour" => 14, "users" => 270],
        ["hour" => 15, "users" => 320],
        ["hour" => 16, "users" => 300]
    ];
}

if (!$budgetData) {
    $budgetData = [
        ["category" => "Salaries", "percent" => 40],
        ["category" => "Advertising", "percent" => 20],
        ["category" => "Development", "percent" => 25],
        ["category" => "Office", "percent" => 15]
    ];
}

if (!$clientsData) {
    $clientsData = [
        ["type" => "Regular", "count" => 150],
        ["type" => "VIP", "count" => 45],
        ["type" => "New", "count" => 80],
        ["type" => "Companies", "count" => 60]
    ];
}

/* ===================== ПОДГОТОВКА МАССИВОВ ДЛЯ ГРАФИКОВ ===================== */
$deptNames = array_column($departmentData, 'department');
$deptValues = array_map('intval', array_column($departmentData, 'sales'));

$hourLabels = array_map(function($h) { return $h['hour'] . ':00'; }, $hourlyData);
$hourValues = array_map('intval', array_column($hourlyData, 'users'));

$budgetLabels = array_column($budgetData, 'category');
$budgetValues = array_map('intval', array_column($budgetData, 'percent'));

$clientLabels = array_column($clientsData, 'type');
$clientValues = array_map('intval', array_column($clientsData, 'count'));

/* ===================== ВАЛЮТНЫЕ ДАННЫЕ ===================== */
$usdRates = [];
$usdTimes = [];
$chfRates = [];
$chfTimes = [];

if (isset($usd['rates']) && is_array($usd['rates'])) {
    $usdRates = array_column($usd['rates'], 'mid');
    $usdTimes = array_column($usd['rates'], 'effectiveDate');
}

if (isset($chf['rates']) && is_array($chf['rates'])) {
    $chfRates = array_column($chf['rates'], 'mid');
    $chfTimes = array_column($chf['rates'], 'effectiveDate');
}

/* ===================== KPI РАСЧЁТЫ ===================== */
$totalSales = array_sum($deptValues);
$totalUsers = array_sum($hourValues);
$totalBudget = array_sum($budgetValues);
$totalClients = array_sum($clientValues);

$salesDelta = count($deptValues) >= 2 
    ? $deptValues[count($deptValues)-1] - $deptValues[count($deptValues)-2] 
    : 0;

$topDept = $deptNames[array_search(max($deptValues), $deptValues)] ?? 'N/A';
$topHour = $hourLabels[array_search(max($hourValues), $hourValues)] ?? 'N/A';
$topBudget = $budgetLabels[array_search(max($budgetValues), $budgetValues)] ?? 'N/A';
$topClient = $clientLabels[array_search(max($clientValues), $clientValues)] ?? 'N/A';

$lastUpdate = date("d M Y, H:i");
?>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Enterprise Analytics Dashboard - Akezhan Yergali 66836</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<script src="https://cdn.plot.ly/plotly-latest.min.js"></script>
<style>
:root{
 --bg:#f5f7fb; --card:#fff; --text:#1f2937; --muted:#6b7280;
}
body.dark{
 --bg:#0f172a; --card:#1e293b; --text:#e5e7eb; --muted:#9ca3af;
}
body{
 background:var(--bg);
 color:var(--text);
 font-family:system-ui;
 transition:.25s;
}
body, body *{color:var(--text)}
.text-muted, small{color:var(--muted)!important}
.card{background:var(--card);border:none;border-radius:16px}
.chart-box{height:320px}
.table, .table td, .table th{
 color:var(--text)!important;
 background:transparent!important
}
.table thead th{background:rgba(255,255,255,.06)!important}
body:not(.dark) .table thead th{background:rgba(0,0,0,.04)!important}
.modal-content,.modal-header,.modal-body{
 background:var(--card)!important;
 color:var(--text)!important
}
.btn-close{filter:invert(1)}
body:not(.dark) .btn-close{filter:none}
.table-striped>tbody>tr:nth-of-type(odd)>*{
 background:rgba(255,255,255,.04)!important
}
body:not(.dark) .table-striped>tbody>tr:nth-of-type(odd)>*{
 background:rgba(0,0,0,.03)!important
}
footer{
 background:var(--card);
 border-top:1px solid rgba(0,0,0,.1)
}
.json-key {color: #22c55e;}
.json-string {color: #ef4444;}
.json-number {color: #38bdf8;}
.json-boolean {color: #facc15;}
.json-null {color: #fb7185;}
.json-brace {color: #16a34a; font-weight: 600;}
.api-status {font-size:12px; padding:2px 8px; border-radius:10px;}
.api-online {background:#22c55e20; color:#22c55e;}
.api-offline {background:#ef444420; color:#ef4444;}
</style>
</head>

<body class="d-flex flex-column min-vh-100">

<!-- КНОПКИ УПРАВЛЕНИЯ -->
<div class="position-fixed end-0 m-3 d-flex flex-column gap-2">
  <button id="themeBtn" class="btn btn-sm btn-outline-secondary" onclick="toggleTheme()">🌙 Dark</button>
  <button class="btn btn-sm btn-outline-primary" onclick="openDoc()">📘 Description</button>
</div>

<div class="container py-5 flex-grow-1">

<!-- ЗАГОЛОВОК -->
<div class="text-center mb-4">
<h2><b>Enterprise Analytics Dashboard</b></h2>
<p class="text-muted">
<b>PHP • PostgreSQL • Render.com • Plotly API • NBP API</b><br>
Last updated: <?=$lastUpdate?> | <span class="api-status <?=($departmentData ? 'api-online' : 'api-offline')?>">API: <?=($departmentData ? 'ONLINE' : 'OFFLINE')?></span>
</p>
</div>

<!-- KPI КАРТОЧКИ -->
<div class="row g-4 mb-5">
<div class="col-lg-3">
<div class="card p-3 text-center shadow-sm">
<small><b>Total Sales</b></small>
<h4><b><?=number_format($totalSales)?> PLN</b></h4>
<small><b><?=($salesDelta>=0?'+':'')?><?=$salesDelta?></b></small>
</div>
</div>
<div class="col-lg-3">
<div class="card p-3 text-center shadow-sm">
<small><b>Total Users (Today)</b></small>
<h4><b><?=number_format($totalUsers)?></b></h4>
<small><b>Peak: <?=$topHour?></b></small>
</div>
</div>
<div class="col-lg-3">
<div class="card p-3 text-center shadow-sm">
<small><b>Budget Categories</b></small>
<h4><b><?=count($budgetLabels)?></b></h4>
<small><b>Largest: <?=$topBudget?></b></small>
</div>
</div>
<div class="col-lg-3">
<div class="card p-3 text-center shadow-sm">
<small><b>Client Types</b></small>
<h4><b><?=count($clientLabels)?></b></h4>
<small><b>Most: <?=$topClient?></b></small>
</div>
</div>
</div>

<!-- 6 ГРАФИКОВ -->
<div class="row g-4">

<!-- Department Sales (Bar Chart) -->
<div class="col-lg-6">
<div class="card p-3 shadow-sm">
<div class="d-flex justify-content-between mb-2 gap-2">
<strong>Department Sales</strong>
<div class="d-flex gap-2">
<button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#deptModal">Show table</button>
<button class="btn btn-sm btn-outline-success" onclick="openTestDialog('Department Sales','deptChart')">Test API</button>
</div>
</div>
<div id="deptChart" class="chart-box"></div>
</div>
</div>

<!-- Hourly Activity (Bar Chart) -->
<div class="col-lg-6">
<div class="card p-3 shadow-sm">
<div class="d-flex justify-content-between mb-2 gap-2">
<strong>Hourly Activity</strong>
<div class="d-flex gap-2">
<button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#hourlyModal">Show table</button>
<button class="btn btn-sm btn-outline-success" onclick="openTestDialog('Hourly Activity','hourlyChart')">Test API</button>
</div>
</div>
<div id="hourlyChart" class="chart-box"></div>
</div>
</div>

<!-- Budget Distribution (Pie Chart) -->
<div class="col-lg-6">
<div class="card p-3 shadow-sm">
<div class="d-flex justify-content-between mb-2 gap-2">
<strong>Budget Distribution</strong>
<div class="d-flex gap-2">
<button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#budgetModal">Show table</button>
<button class="btn btn-sm btn-outline-success" onclick="openTestDialog('Budget Distribution','budgetChart')">Test API</button>
</div>
</div>
<div id="budgetChart" class="chart-box"></div>
</div>
</div>

<!-- Client Types (Pie Chart) -->
<div class="col-lg-6">
<div class="card p-3 shadow-sm">
<div class="d-flex justify-content-between mb-2 gap-2">
<strong>Client Types</strong>
<div class="d-flex gap-2">
<button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#clientModal">Show table</button>
<button class="btn btn-sm btn-outline-success" onclick="openTestDialog('Client Types','clientChart')">Test API</button>
</div>
</div>
<div id="clientChart" class="chart-box"></div>
</div>
</div>

<!-- USD Chart -->
<div class="col-lg-6">
<div class="card p-3 shadow-sm">
<div class="d-flex justify-content-between mb-2 gap-2">
<strong>USD → PLN (last 20)</strong>
<button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#usdModal">Show table</button>
</div>
<div id="usdChart" class="chart-box"></div>
</div>
</div>

<!-- CHF Chart -->
<div class="col-lg-6">
<div class="card p-3 shadow-sm">
<div class="d-flex justify-content-between mb-2 gap-2">
<strong>CHF → PLN (last 20)</strong>
<button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#chfModal">Show table</button>
</div>
<div id="chfChart" class="chart-box"></div>
</div>
</div>

</div>
</div>

<!-- ПОДВАЛ -->
<footer class="text-center py-4">
<div class="fw-semibold mb-2">Akezhan Yergali 66836</div>
<div class="text-muted">Enterprise Analytics Dashboard - Final Project</div>
</footer>

<!-- МОДАЛЬНЫЕ ОКНА ДЛЯ ТАБЛИЦ -->
<?php
$modals = [
    ['id' => 'deptModal', 'title' => 'Department Sales', 'headers' => ['Department', 'Sales (PLN)'], 'data' => $departmentData],
    ['id' => 'hourlyModal', 'title' => 'Hourly Activity', 'headers' => ['Hour', 'Users'], 'data' => $hourlyData],
    ['id' => 'budgetModal', 'title' => 'Budget Distribution', 'headers' => ['Category', 'Percent'], 'data' => $budgetData],
    ['id' => 'clientModal', 'title' => 'Client Types', 'headers' => ['Type', 'Count'], 'data' => $clientsData],
    ['id' => 'usdModal', 'title' => 'USD Rates', 'headers' => ['Date', 'Rate'], 'data' => isset($usd['rates']) ? array_map(function($r) { return ['Date' => $r['effectiveDate'], 'Rate' => $r['mid']]; }, $usd['rates']) : []],
    ['id' => 'chfModal', 'title' => 'CHF Rates', 'headers' => ['Date', 'Rate'], 'data' => isset($chf['rates']) ? array_map(function($r) { return ['Date' => $r['effectiveDate'], 'Rate' => $r['mid']]; }, $chf['rates']) : []]
];

foreach ($modals as $modal):
?>
<div class="modal fade" id="<?=$modal['id']?>" tabindex="-1">
<div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
<div class="modal-content">
<div class="modal-header"><h5><?=$modal['title']?> – Data Table</h5><button class="btn-close" data-bs-dismiss="modal"></button></div>
<div class="modal-body">
<table class="table table-striped">
<thead><tr><?php foreach($modal['headers'] as $h): ?><th><?=$h?></th><?php endforeach; ?></tr></thead>
<tbody>
<?php foreach($modal['data'] as $row): ?>
<tr><?php foreach($row as $val): ?><td><?=$val?></td><?php endforeach; ?></tr>
<?php endforeach; ?>
</tbody>
</table>
</div>
</div>
</div>
</div>
<?php endforeach; ?>

<!-- МОДАЛЬНОЕ ОКНО ДЛЯ ТЕСТОВ API -->
<div class="modal fade" id="testModal" tabindex="-1">
<div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
<div class="modal-content">
<div class="modal-header"><h5 id="testTitle">API Test</h5><button class="btn-close" data-bs-dismiss="modal"></button></div>
<div class="modal-body">
<div class="card p-3 mb-3"><strong>API Endpoint</strong>
<input id="apiInput" class="form-control mt-2" placeholder="Paste full API URL here (https://...)">
<small class="text-muted mt-2 d-block">Example: <?=$BASE?>/department.php</small>
</div>
<button class="btn btn-success mb-3" onclick="runApiTest()">▶ Run Tests</button>
<div id="testResult" style="display:none; background:#0b1220; color:#e5e7eb; padding:16px; border-radius:12px; font-family:monospace; white-space:pre"></div>
</div>
</div>
</div>
</div>

<!-- МОДАЛЬНОЕ ОКНО ОПИСАНИЯ -->
<div class="modal fade" id="docModal" tabindex="-1">
<div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
<div class="modal-content">
<div class="modal-header"><h5>📘 Project Documentation</h5><button class="btn-close" data-bs-dismiss="modal"></button></div>
<div class="modal-body">
<section class="mb-4"><h6>- Author</h6><p>Name: <strong>Akezhan Yergali</strong><br>Student ID: <strong>66836</strong></p></section>
<section class="mb-4"><h6>- Project Overview</h6><p>Enterprise Analytics Dashboard with 4 custom REST APIs, 6 interactive charts, and real-time currency data.</p></section>
<section class="mb-4"><h6>- REST API Endpoints</h6>
<ul>
<li><code><?=$BASE?>/department.php</code> – Department sales data</li>
<li><code><?=$BASE?>/hourly.php</code> – Hourly user activity</li>
<li><code><?=$BASE?>/budget.php</code> – Budget distribution</li>
<li><code><?=$BASE?>/clients.php</code> – Client types data</li>
<li><code>NBP API</code> – USD/CHF exchange rates</li>
</ul></section>
<section class="mb-4"><h6>- Technologies</h6>
<ul>
<li><strong>Frontend:</strong> PHP, Bootstrap 5, Plotly.js</li>
<li><strong>Backend:</strong> REST APIs with CURL</li>
<li><strong>Database:</strong> PostgreSQL on Render</li>
<li><strong>Hosting:</strong> Render.com</li>
<li><strong>External APIs:</strong> NBP (National Bank of Poland)</li>
</ul></section>
<section class="mb-4"><h6>- Features</h6>
<ul>
<li>4 custom REST APIs with meaningful business data</li>
<li>6 interactive charts (2 bar, 2 pie, 2 line)</li>
<li>PHP CURL for data fetching</li>
<li>API testing interface</li>
<li>Dark/Light theme toggle</li>
<li>Responsive design with Bootstrap</li>
</ul></section>
</div>
</div>
</div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
/* ===================== API TESTS ===================== */
const BASE_URL = "<?=$BASE?>";
const testCases = {
    deptChart: {
        api: `${BASE_URL}/department.php`,
        tests: [
            {name: "HTTP 200 OK", check: (res, data) => res.status === 200},
            {name: "Valid JSON array", check: (res, data) => Array.isArray(data) && data.length > 0},
            {name: "Data structure: department & sales", check: (res, data) => data.every(i => i.department && i.sales)},
            {name: "Sales values positive", check: (res, data) => data.every(i => i.sales > 0)}
        ]
    },
    hourlyChart: {
        api: `${BASE_URL}/hourly.php`,
        tests: [
            {name: "HTTP 200 OK", check: (res, data) => res.status === 200},
            {name: "Valid JSON array", check: (res, data) => Array.isArray(data) && data.length > 0},
            {name: "Data structure: hour & users", check: (res, data) => data.every(i => i.hour && i.users)},
            {name: "Users values positive", check: (res, data) => data.every(i => i.users > 0)}
        ]
    },
    budgetChart: {
        api: `${BASE_URL}/budget.php`,
        tests: [
            {name: "HTTP 200 OK", check: (res, data) => res.status === 200},
            {name: "Valid JSON array", check: (res, data) => Array.isArray(data) && data.length > 0},
            {name: "Data structure: category & percent", check: (res, data) => data.every(i => i.category && i.percent)},
            {name: "Sum equals 100%", check: (res, data) => data.reduce((sum, i) => sum + i.percent, 0) === 100}
        ]
    },
    clientChart: {
        api: `${BASE_URL}/clients.php`,
        tests: [
            {name: "HTTP 200 OK", check: (res, data) => res.status === 200},
            {name: "Valid JSON array", check: (res, data) => Array.isArray(data) && data.length > 0},
            {name: "Data structure: type & count", check: (res, data) => data.every(i => i.type && i.count)},
            {name: "Count values positive", check: (res, data) => data.every(i => i.count > 0)}
        ]
    }
};

let currentTest = null;
function openTestDialog(title, id) {
    currentTest = testCases[id];
    document.getElementById("testTitle").innerText = title + " – API Tests";
    document.getElementById("apiInput").value = currentTest.api;
    document.getElementById("testResult").style.display = "none";
    new bootstrap.Modal(document.getElementById("testModal")).show();
}

async function runApiTest() {
    const box = document.getElementById("testResult");
    const url = document.getElementById("apiInput").value.trim();
    box.style.display = "block";
    box.innerHTML = "⏳ Validating URL...\n\n";
    
    if (!url.startsWith("http")) {
        box.innerHTML = "❌ Invalid URL\n\nPlease provide a full valid API URL.";
        return;
    }
    
    try {
        const response = await fetch(url);
        if (!response.ok) {
            box.innerHTML = `❌ HTTP Error: ${response.status}\n\n${response.statusText}`;
            return;
        }
        const data = await response.json();
        box.innerHTML = "🔍 Running tests...\n\n";
        let passed = 0;
        
        for (let i = 0; i < currentTest.tests.length; i++) {
            const t = currentTest.tests[i];
            box.innerHTML += `⏳ Test ${i + 1}: ${t.name}\n`;
            await new Promise(r => setTimeout(r, 500));
            
            let ok = false;
            try { ok = t.check(response, data); } catch (e) { ok = false; }
            
            if (ok) {
                passed++;
                box.innerHTML += `✅ Test ${i + 1} passed\n\n`;
            } else {
                box.innerHTML += `❌ Test ${i + 1} failed\n\n`;
            }
        }
        
        box.innerHTML += `\nSummary:\n${passed} / ${currentTest.tests.length} tests passed\n`;
        if (passed === currentTest.tests.length) {
            box.innerHTML += "\n--- JSON Preview ---\n" + prettyJson(data);
        }
    } catch (e) {
        box.innerHTML = "❌ Network error\n\n" + e.message;
    }
}

function prettyJson(obj) {
    let json = JSON.stringify(obj, null, 2).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
    return json.replace(/({|}|\[|\])|("(\\u[a-zA-Z0-9]{4}|\\[^u]|[^\\"])*")(\s*:)?|\b(true|false|null)\b|-?\d+(\.\d+)?/g, (match, brace) => {
        if (brace) return `<span class="json-brace">${match}</span>`;
        if (/^"/.test(match)) return /:$/.test(match) ? `<span class="json-key">${match}</span>` : `<span class="json-string">${match}</span>`;
        if (/true|false/.test(match)) return `<span class="json-boolean">${match}</span>`;
        if (/null/.test(match)) return `<span class="json-null">${match}</span>`;
        return `<span class="json-number">${match}</span>`;
    });
}

/* ===================== ГРАФИКИ ===================== */
function drawCharts() {
    const dark = document.body.classList.contains('dark');
    const fontColor = dark ? '#e5e7eb' : '#1f2937';
    const layout = {
        paper_bgcolor: 'transparent',
        plot_bgcolor: 'transparent',
        font: {color: fontColor},
        margin: {t: 40, l: 40, r: 30, b: 40}
    };
    const cfg = {displayModeBar: false, displaylogo: false, responsive: true};

    // Department Sales
    Plotly.newPlot('deptChart', [{
        x: <?=json_encode($deptNames)?>,
        y: <?=json_encode($deptValues)?>,
        type: 'bar',
        marker: {color: '#3b82f6'}
    }], {...layout, title: 'Sales by Department (PLN)'}, cfg);

    // Hourly Activity
    Plotly.newPlot('hourlyChart', [{
        x: <?=json_encode($hourLabels)?>,
        y: <?=json_encode($hourValues)?>,
        type: 'bar',
        marker: {color: '#10b981'}
    }], {...layout, title: 'Hourly User Activity'}, cfg);

    // Budget Distribution
    Plotly.newPlot('budgetChart', [{
        labels: <?=json_encode($budgetLabels)?>,
        values: <?=json_encode($budgetValues)?>,
        type: 'pie',
        hole: 0.4,
        marker: {colors: ['#3b82f6', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6', '#ec4899', '#14b8a6', '#f97316']}
    }], {...layout, title: 'Budget Distribution (%)'}, cfg);

    // Client Types
    Plotly.newPlot('clientChart', [{
        labels: <?=json_encode($clientLabels)?>,
        values: <?=json_encode($clientValues)?>,
        type: 'pie',
        hole: 0.4,
        marker: {colors: ['#3b82f6', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6', '#ec4899']}
    }], {...layout, title: 'Client Types Distribution'}, cfg);

    // USD Chart
    if (<?=json_encode($usdTimes)?>.length > 0) {
        Plotly.newPlot('usdChart', [{
            x: <?=json_encode($usdTimes)?>,
            y: <?=json_encode($usdRates)?>,
            mode: 'lines+markers',
            line: {width: 3, color: '#2563eb'}
        }], {...layout, title: 'USD to PLN Exchange Rate'}, cfg);
    }

    // CHF Chart
    if (<?=json_encode($chfTimes)?>.length > 0) {
        Plotly.newPlot('chfChart', [{
            x: <?=json_encode($chfTimes)?>,
            y: <?=json_encode($chfRates)?>,
            mode: 'lines+markers',
            line: {width: 3, color: '#16a34a'}
        }], {...layout, title: 'CHF to PLN Exchange Rate'}, cfg);
    }
}

/* ===================== ТЕМА ===================== */
function toggleTheme() {
    const dark = document.body.classList.toggle('dark');
    localStorage.setItem('theme', dark ? 'dark' : 'light');
    document.getElementById('themeBtn').innerText = dark ? '☀️ Light' : '🌙 Dark';
    drawCharts();
}

if (localStorage.getItem('theme') === 'dark') {
    document.body.classList.add('dark');
    document.getElementById('themeBtn').innerText = '☀️ Light';
}

/* ===================== ДОПОЛНИТЕЛЬНЫЕ ФУНКЦИИ ===================== */
function openDoc() {
    new bootstrap.Modal(document.getElementById("docModal")).show();
}

/* ===================== ЗАПУСК ===================== */
document.addEventListener('DOMContentLoaded', function() {
    drawCharts();
    console.log('Dashboard loaded successfully');
    console.log('API Status:', {
        department: <?=json_encode($departmentData ? 'OK' : 'FAILED')?>,
        hourly: <?=json_encode($hourlyData ? 'OK' : 'FAILED')?>,
        budget: <?=json_encode($budgetData ? 'OK' : 'FAILED')?>,
        clients: <?=json_encode($clientsData ? 'OK' : 'FAILED')?>
    });
});
</script>
</body>
</html>
