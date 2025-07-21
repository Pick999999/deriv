<?php
// Function to generate mock candlestick data
function generateMockCandlestickData($numPoints) {
    $data = [];
    $basePrice = 100;
    $startTime = strtotime('2025-06-26 00:00:00') * 1000; // Start date in milliseconds

    for ($i = 0; $i < $numPoints; $i++) {
        $open = $basePrice + rand(-5, 5);
        $close = $open + rand(-10, 10);
        $high = max($open, $close) + rand(1, 5);
        $low = min($open, $close) - rand(1, 5);

        $open = round(max(90, min(110, $open)), 2);
        $close = round(max(90, min(110, $close)), 2);
        $high = round(max($open, $close, $high), 2);
        $low = round(min($open, $close, $low), 2);

        $timestamp = $startTime + ($i * 3600 * 1000); // Add 1 hour per point

        $data[] = [
            'x' => $timestamp,
            'o' => $open,
            'h' => $high,
            'l' => $low,
            'c' => $close
        ];

        $basePrice = $close;
    }

    return $data;
}

// Function to calculate EMA
function calculateEMA($prices, $period) {
    $ema = [];
    $alpha = 2 / ($period + 1);
    $ema[0] = $prices[0]; // First EMA = first close price

    for ($i = 1; $i < count($prices); $i++) {
        $ema[$i] = round(($prices[$i] * $alpha) + ($ema[$i - 1] * (1 - $alpha)), 2);
    }

    return $ema;
}

// Generate 60 mock data points
$mockData = generateMockCandlestickData(60);

// Extract close prices and timestamps for EMA
$closePrices = array_map(function($point) { return $point['c']; }, $mockData);
$timestamps = array_map(function($point) { return $point['x']; }, $mockData);

// Calculate EMA3 and EMA5
$ema3 = calculateEMA($closePrices, 3);
$ema5 = calculateEMA($closePrices, 5);

// Format EMA data for Chart.js
$ema3Data = array_map(function($timestamp, $value) {
    return ['x' => $timestamp, 'y' => $value];
}, $timestamps, $ema3);
$ema5Data = array_map(function($timestamp, $value) {
    return ['x' => $timestamp, 'y' => $value];
}, $timestamps, $ema5);

// Chart.js configuration with candlestick and EMA
$chartConfig = [
    'type' => 'candlestick',
    'data' => [
        'datasets' => [
            [
                'label' => 'Stock Price',
                'data' => $mockData,
                'borderColor' => [
                    'up' => 'rgba(0, 255, 0, 1)', // Green for up candles
                    'down' => 'rgba(255, 0, 0, 1)', // Red for down candles
                    'unchanged' => 'rgba(128, 128, 128, 1)'
                ],
                'backgroundColor' => [
                    'up' => 'rgba(0, 255, 0, 0.5)',
                    'down' => 'rgba(255, 0, 0, 0.5)',
                    'unchanged' => 'rgba(128, 128, 128, 0.5)'
                ]
            ],
            [
                'type' => 'line',
                'label' => 'EMA3',
                'data' => $ema3Data,
                'borderColor' => 'rgba(0, 123, 255, 1)', // Blue for EMA3
                'backgroundColor' => 'rgba(0, 123, 255, 0.2)',
                'fill' => false,
                'borderWidth' => 2,
                'pointRadius' => 0
            ],
            [
                'type' => 'line',
                'label' => 'EMA5',
                'data' => $ema5Data,
                'borderColor' => 'rgba(255, 215, 0, 1)', // Yellow for EMA5
                'backgroundColor' => 'rgba(255, 215, 0, 0.2)',
                'fill' => false,
                'borderWidth' => 2,
                'pointRadius' => 0
            ]
        ]
    ],
    'options' => [
        'scales' => [
            'x' => [
                'type' => 'time',
                'time' => [
                    'unit' => 'hour',
                    'displayFormats' => [
                        'hour' => 'MMM D, HH:mm'
                    ]
                ],
                'title' => [
                    'display' => true,
                    'text' => 'Date'
                ]
            ],
            'y' => [
                'title' => [
                    'display' => true,
                    'text' => 'Price'
                ],
                'min' => 80,
                'max' => 120
            ]
        ],
        'plugins' => [
            'legend' => [
                'display' => true
            ],
            'title' => [
                'display' => true,
                'text' => 'Candlestick Chart with EMA3 and EMA5'
            ]
        ]
    ]
];

// Encode chart configuration as JSON
$chartJson = json_encode($chartConfig);

// QuickChart.io URL
$quickChartUrl = 'https://quickchart.io/chart?width=800&height=400&v=3&chart=' . urlencode($chartJson);

// Save the chart image to server
$savePath = 'charts/chart_' . time() . '.png'; // Unique filename with timestamp
$imageContent = @file_get_contents($quickChartUrl);
if ($imageContent !== false) {
    if (!file_exists('charts')) {
        mkdir('charts', 0755, true); // Create charts directory if it doesn't exist
    }
    file_put_contents($savePath, $imageContent);
    $saveMessage = "Chart saved successfully as <a href='$savePath'>$savePath</a>";
} else {
    $saveMessage = "Error: Failed to save chart image. Check QuickChart.io URL or server permissions.";
}

// Output HTML
echo <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Candlestick Chart with EMA3 and EMA5</title>
</head>
<body>
    <h2>Candlestick Chart with EMA3 and EMA5</h2>
    <img src="$quickChartUrl" alt="Candlestick Chart with EMA" style="max-width:100%;">
    <p>$saveMessage</p>
</body>
</html>
HTML;
?>