<!DOCTYPE html>
<?php
// ================== PHP CURL ЧАСТЬ ==================
function getDataWithCurl($url) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    $response = curl_exec($ch);
    curl_close($ch);
    return json_decode($response, true);
}

$api_url = "https://cloud-project-api-3.onrender.com";

// Проверяем что API файлы существуют
$department = getDataWithCurl($api_url . '/department.php');
$hourly = getDataWithCurl($api_url . '/hourly.php');
$budget = getDataWithCurl($api_url . '/budget.php');
$clients = getDataWithCurl($api_url . '/clients.php');

// Если API не работают, используем запасные данные
if (!$department) {
    $department = [
        ["department" => "Marketing", "sales" => 15000],
        ["department" => "Sales", "sales" => 22000],
        ["department" => "IT", "sales" => 18000],
        ["department" => "Support", "sales" => 9000]
    ];
}
// ... аналогично для остальных
?>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Analytics Dashboard - Akezhan Yergali 66836</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.plot.ly/plotly-latest.min.js"></script>
    <style>
        body { background: #f5f7fb; font-family: Arial; padding: 20px; }
        .card { background: white; border-radius: 12px; padding: 20px; margin-bottom: 20px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .chart { height: 320px; width: 100%; }
        h1 { text-align: center; color: #1f2937; margin-bottom: 10px; }
        .author { text-align: center; color: #6b7280; margin-bottom: 30px; padding: 20px; background: white; border-radius: 12px; }
        footer { text-align: center; margin-top: 40px; color: #6b7280; padding: 20px; }
        .test-btn { margin-top: 10px; }
        .error { color: red; padding: 10px; background: #fee; border-radius: 5px; margin: 10px 0; }
    </style>
</head>
<body>
    <div class="container">
        <div class="author">
            <h1>📊 Enterprise Analytics Dashboard</h1>
            <h4>Akezhan Yergali 66836</h4>
            <p class="text-muted">PHP • CURL • Plotly • Bootstrap • NBP API</p>
        </div>

        <?php if (!$department && !$hourly): ?>
        <div class="error">
            ⚠️ API временно недоступны. Используются демо-данные.
        </div>
        <?php endif; ?>

        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <h5>Department Sales (Bar Chart)</h5>
                    <div id="chart1" class="chart"></div>
                    <button class="btn btn-outline-primary btn-sm test-btn" onclick="testAPI(1)">Test API 1</button>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <h5>Hourly Activity (Bar Chart)</h5>
                    <div id="chart2" class="chart"></div>
                    <button class="btn btn-outline-primary btn-sm test-btn" onclick="testAPI(2)">Test API 2</button>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <h5>Budget Distribution (Pie Chart)</h5>
                    <div id="chart3" class="chart"></div>
                    <button class="btn btn-outline-primary btn-sm test-btn" onclick="testAPI(3)">Test API 3</button>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <h5>Client Types (Pie Chart)</h5>
                    <div id="chart4" class="chart"></div>
                    <button class="btn btn-outline-primary btn-sm test-btn" onclick="testAPI(4)">Test API 4</button>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <h5>USD to PLN (Last 20 days)</h5>
                    <div id="chart5" class="chart"></div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <h5>CHF to PLN (Last 20 days)</h5>
                    <div id="chart6" class="chart"></div>
                </div>
            </div>
        </div>

        <footer>
            <p>© 2026 Akezhan Yergali 66836 - Enterprise Analytics Dashboard</p>
            <p>Data loaded via PHP CURL from Render API</p>
        </footer>
    </div>

    <script>
        // Данные из PHP CURL
        const departmentData = <?php echo json_encode($department ?: []); ?>;
        const hourlyData = <?php echo json_encode($hourly ?: []); ?>;
        const budgetData = <?php echo json_encode($budget ?: []); ?>;
        const clientsData = <?php echo json_encode($clients ?: []); ?>;

        function drawCharts() {
            // Chart 1
            if (departmentData && departmentData.length > 0) {
                Plotly.newPlot('chart1', [{
                    x: departmentData.map(d => d.department),
                    y: departmentData.map(d => d.sales),
                    type: 'bar',
                    marker: {color: '#3b82f6'}
                }], {
                    title: 'Sales by Department (PLN)',
                    paper_bgcolor: 'transparent'
                });
            }

            // Chart 2
            if (hourlyData && hourlyData.length > 0) {
                Plotly.newPlot('chart2', [{
                    x: hourlyData.map(d => d.hour + ':00'),
                    y: hourlyData.map(d => d.users),
                    type: 'bar',
                    marker: {color: '#10b981'}
                }], {
                    title: 'Users by Hour',
                    paper_bgcolor: 'transparent'
                });
            }

            // Chart 3
            if (budgetData && budgetData.length > 0) {
                Plotly.newPlot('chart3', [{
                    labels: budgetData.map(d => d.category),
                    values: budgetData.map(d => d.percent),
                    type: 'pie',
                    hole: 0.4
                }], {
                    title: 'Budget Distribution (%)',
                    paper_bgcolor: 'transparent'
                });
            }

            // Chart 4
            if (clientsData && clientsData.length > 0) {
                Plotly.newPlot('chart4', [{
                    labels: clientsData.map(d => d.type),
                    values: clientsData.map(d => d.count),
                    type: 'pie',
                    hole: 0.4
                }], {
                    title: 'Client Types',
                    paper_bgcolor: 'transparent'
                });
            }

            // Валютные графики (через fetch, так как CORS)
            fetch('https://api.nbp.pl/api/exchangerates/rates/a/usd/last/20/?format=json')
                .then(res => res.json())
                .then(data => {
                    Plotly.newPlot('chart5', [{
                        x: data.rates.map(r => r.effectiveDate),
                        y: data.rates.map(r => r.mid),
                        type: 'scatter',
                        mode: 'lines+markers',
                        name: 'USD/PLN'
                    }], {
                        title: 'USD to PLN Exchange Rate',
                        paper_bgcolor: 'transparent'
                    });
                });

            fetch('https://api.nbp.pl/api/exchangerates/rates/a/chf/last/20/?format=json')
                .then(res => res.json())
                .then(data => {
                    Plotly.newPlot('chart6', [{
                        x: data.rates.map(r => r.effectiveDate),
                        y: data.rates.map(r => r.mid),
                        type: 'scatter',
                        mode: 'lines+markers',
                        name: 'CHF/PLN',
                        line: {color: 'green'}
                    }], {
                        title: 'CHF to PLN Exchange Rate',
                        paper_bgcolor: 'transparent'
                    });
                });
        }

        function testAPI(num) {
            const tests = [
                {name: "Department API", url: "<?php echo $api_url; ?>/department.php"},
                {name: "Hourly API", url: "<?php echo $api_url; ?>/hourly.php"},
                {name: "Budget API", url: "<?php echo $api_url; ?>/budget.php"},
                {name: "Clients API", url: "<?php echo $api_url; ?>/clients.php"}
            ];
            
            const test = tests[num-1];
            alert(`Testing: ${test.name}\nURL: ${test.url}\n\nOpen this URL in browser to check API.`);
        }

        document.addEventListener('DOMContentLoaded', drawCharts);
    </script>
</body>
</html>