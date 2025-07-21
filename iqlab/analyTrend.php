<?php
  
 $sFileName = '../deriv/rawData.json';
 $file = fopen($sFileName,"r");
 while(! feof($file))  {
   $st .= fgets($file) ;
 }
 fclose($file);

//$sample_data = JSON_DECODE($st);
$sample_data = $st;
  

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Candlestick Analysis Dashboard</title>
    <script src="https://unpkg.com/lightweight-charts@4.0.1/dist/lightweight-charts.standalone.production.js"></script>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 20px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
        }
        
        .container {
            max-width: 1400px;
            margin: 0 auto;
            background: rgba(255, 255, 255, 0.95);
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        
        .header {
            background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }
        
        .header h1 {
            margin: 0;
            font-size: 2.5em;
            font-weight: 300;
            text-shadow: 0 2px 4px rgba(0,0,0,0.3);
        }
        
        .content {
            padding: 30px;
        }
        
        .chart-container {
            background: white;
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 30px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            position: relative;
        }
        
        #chartContainer {
            width: 100%;
            height: 600px;
            border-radius: 10px;
        }
        
        .analysis-container {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
            margin-bottom: 30px;
        }
        
        .analysis-panel {
            background: white;
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }
        
        .analysis-panel h3 {
            margin-top: 0;
            color: #2c3e50;
            font-size: 1.4em;
            border-bottom: 3px solid #3498db;
            padding-bottom: 10px;
        }
        
        .trend-indicator {
            display: inline-block;
            padding: 8px 16px;
            border-radius: 25px;
            color: white;
            font-weight: bold;
            margin: 5px;
            font-size: 0.9em;
        }
        
        .uptrend { background: linear-gradient(135deg, #27ae60, #2ecc71); }
        .downtrend { background: linear-gradient(135deg, #e74c3c, #c0392b); }
        .sideways { background: linear-gradient(135deg, #95a5a6, #7f8c8d); }
        
        .summary-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        
        .summary-table th,
        .summary-table td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid #ecf0f1;
        }
        
        .summary-table th {
            background: linear-gradient(135deg, #3498db, #2980b9);
            color: white;
            font-weight: 600;
        }
        
        .summary-table tr:hover {
            background: #f8f9fa;
            transform: scale(1.01);
            transition: all 0.3s ease;
        }
        
        .legend {
            position: absolute;
            top: 20px;
            right: 20px;
            background: rgba(255, 255, 255, 0.95);
            padding: 15px;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
            font-size: 0.9em;
        }
        
        .legend-item {
            display: flex;
            align-items: center;
            margin: 5px 0;
        }
        
        .legend-color {
            width: 20px;
            height: 15px;
            margin-right: 10px;
            border-radius: 3px;
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin-top: 20px;
        }
        
        .stat-card {
            background: linear-gradient(135deg, #f8f9fa, #e9ecef);
            padding: 20px;
            border-radius: 10px;
            text-align: center;
            border-left: 4px solid #3498db;
        }
        
        .stat-value {
            font-size: 1.8em;
            font-weight: bold;
            color: #2c3e50;
        }
        
        .stat-label {
            font-size: 0.9em;
            color: #7f8c8d;
            margin-top: 5px;
        }
        
        @media (max-width: 768px) {
            .analysis-container {
                grid-template-columns: 1fr;
            }
            
            .content {
                padding: 15px;
            }
            
            #chartContainer {
                height: 400px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>📈 Candlestick Analysis Dashboard</h1>
            <p>Advanced Technical Analysis with Trend Detection & Support/Resistance Levels</p>
        </div>
        
        <div class="content">
            <div class="chart-container">
                <div id="chartContainer"></div>
                <div class="legend">
                    <div class="legend-item">
                        <div class="legend-color" style="background: rgba(39, 174, 96, 0.2);"></div>
                        <span>Uptrend Zone</span>
                    </div>
                    <div class="legend-item">
                        <div class="legend-color" style="background: rgba(231, 76, 60, 0.2);"></div>
                        <span>Downtrend Zone</span>
                    </div>
                    <div class="legend-item">
                        <div class="legend-color" style="background: rgba(149, 165, 166, 0.2);"></div>
                        <span>Sideways Zone</span>
                    </div>
                    <div class="legend-item">
                        <div class="legend-color" style="background: #e74c3c;"></div>
                        <span>Resistance</span>
                    </div>
                    <div class="legend-item">
                        <div class="legend-color" style="background: #27ae60;"></div>
                        <span>Support</span>
                    </div>
                </div>
            </div>
            
            <div class="analysis-container">
                <div class="analysis-panel">
                    <h3>🎯 Trend Analysis</h3>
                    <div id="trendSummary"></div>
                    <div class="stats-grid">
                        <div class="stat-card">
                            <div class="stat-value" id="totalSegments">-</div>
                            <div class="stat-label">Total Segments</div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-value" id="dominantTrend">-</div>
                            <div class="stat-label">Dominant Trend</div>
                        </div>
                    </div>
                </div>
                
                <div class="analysis-panel">
                    <h3>📊 Support & Resistance</h3>
                    <div id="srLevels"></div>
                    <div class="stats-grid">
                        <div class="stat-card">
                            <div class="stat-value" id="supportCount">-</div>
                            <div class="stat-label">Support Levels</div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-value" id="resistanceCount">-</div>
                            <div class="stat-label">Resistance Levels</div>
                        </div>
                    </div>
                </div>
            </div>
            
            <table class="summary-table" id="detailsTable">
                <thead>
                    <tr>
                        <th>Segment</th>
                        <th>Trend Type</th>
                        <th>Strength</th>
                        <th>Price Change</th>
                        <th>Volatility</th>
                        <th>Key Levels</th>
                    </tr>
                </thead>
                <tbody id="tableBody">
                </tbody>
            </table>
        </div>
    </div>

    <script>
        // Sample candlestick data (replace with your actual data from PHP)
		
        let sampleData = [
            { time: '2024-01-01', open: 1.1345, high: 1.1367, low: 1.1340, close: 1.1355 },
            { time: '2024-01-02', open: 1.1355, high: 1.1372, low: 1.1351, close: 1.1368 },
            { time: '2024-01-03', open: 1.1368, high: 1.1385, low: 1.1365, close: 1.1380 },
            { time: '2024-01-04', open: 1.1380, high: 1.1390, low: 1.1375, close: 1.1382 },
            { time: '2024-01-05', open: 1.1382, high: 1.1395, low: 1.1378, close: 1.1388 },
            { time: '2024-01-08', open: 1.1388, high: 1.1400, low: 1.1385, close: 1.1395 },
            { time: '2024-01-09', open: 1.1395, high: 1.1405, low: 1.1390, close: 1.1398 },
            { time: '2024-01-10', open: 1.1398, high: 1.1408, low: 1.1392, close: 1.1402 },
            { time: '2024-01-11', open: 1.1402, high: 1.1410, low: 1.1395, close: 1.1405 },
            { time: '2024-01-12', open: 1.1405, high: 1.1415, low: 1.1400, close: 1.1410 },
            { time: '2024-01-15', open: 1.1410, high: 1.1420, low: 1.1405, close: 1.1415 },
            { time: '2024-01-16', open: 1.1415, high: 1.1425, low: 1.1410, close: 1.1418 },
            { time: '2024-01-17', open: 1.1418, high: 1.1428, low: 1.1412, close: 1.1420 },
            { time: '2024-01-18', open: 1.1420, high: 1.1430, low: 1.1415, close: 1.1425 },
            { time: '2024-01-19', open: 1.1425, high: 1.1435, low: 1.1420, close: 1.1430 },
            { time: '2024-01-22', open: 1.1430, high: 1.1425, low: 1.1415, close: 1.1420 },
            { time: '2024-01-23', open: 1.1420, high: 1.1415, low: 1.1405, close: 1.1410 },
            { time: '2024-01-24', open: 1.1410, high: 1.1405, low: 1.1395, close: 1.1400 },
            { time: '2024-01-25', open: 1.1400, high: 1.1395, low: 1.1385, close: 1.1390 },
            { time: '2024-01-26', open: 1.1390, high: 1.1385, low: 1.1375, close: 1.1380 },
            { time: '2024-01-29', open: 1.1380, high: 1.1385, low: 1.1370, close: 1.1375 },
            { time: '2024-01-30', open: 1.1375, high: 1.1380, low: 1.1365, close: 1.1370 },
            { time: '2024-01-31', open: 1.1370, high: 1.1375, low: 1.1360, close: 1.1365 },
            { time: '2024-02-01', open: 1.1365, high: 1.1370, low: 1.1355, close: 1.1360 },
            { time: '2024-02-02', open: 1.1360, high: 1.1365, low: 1.1350, close: 1.1355 }
        ];
		sampleData = <?php echo $sample_data; ?>

        class CandlestickAnalyzer {
            constructor(data, minPeriod = 5, trendThreshold = 0.003) {
                this.data = data;
                this.minPeriod = minPeriod;
                this.trendThreshold = trendThreshold;
                this.chart = null;
                this.candleSeries = null;
            }

            analyzeTrends() {
                const segments = this.segmentData();
                const results = [];

                segments.forEach((segment, index) => {
                    const analysis = this.analyzeSegment(segment, index);
                    results.push(analysis);
                });

                return results;
            }

            segmentData() {
                const segments = [];
                const segmentSize = Math.max(this.minPeriod, Math.floor(this.data.length / 6));
                
                for (let i = 0; i < this.data.length; i += segmentSize) {
                    const end = Math.min(i + segmentSize, this.data.length);
                    if (end - i >= this.minPeriod) {
                        segments.push(this.data.slice(i, end));
                    }
                }
                
                return segments;
            }

            analyzeSegment(segment, index) {
                const closes = segment.map(d => d.close);
                const highs = segment.map(d => d.high);
                const lows = segment.map(d => d.low);
                
                const trend = this.calculateTrend(closes);
                const trendType = this.determineTrendType(trend.slope, trend.rSquared);
                const supportResistance = this.findSupportResistance(highs, lows, closes);
                const stats = this.calculateStatistics(closes, highs, lows);
                
                return {
                    segmentIndex: index,
                    startTime: segment[0].time,
                    endTime: segment[segment.length - 1].time,
                    candleCount: segment.length,
                    trend: {
                        type: trendType,
                        slope: trend.slope,
                        strength: trend.rSquared,
                        angle: Math.atan(trend.slope) * 180 / Math.PI
                    },
                    supportResistance: supportResistance,
                    statistics: stats,
                    priceRange: {
                        startPrice: closes[0],
                        endPrice: closes[closes.length - 1],
                        changePercent: ((closes[closes.length - 1] - closes[0]) / closes[0]) * 100
                    }
                };
            }

            calculateTrend(prices) {
                const n = prices.length;
                const x = Array.from({length: n}, (_, i) => i);
                
                const sumX = x.reduce((a, b) => a + b, 0);
                const sumY = prices.reduce((a, b) => a + b, 0);
                const sumXY = x.reduce((sum, xi, i) => sum + xi * prices[i], 0);
                const sumX2 = x.reduce((sum, xi) => sum + xi * xi, 0);
                
                const slope = (n * sumXY - sumX * sumY) / (n * sumX2 - sumX * sumX);
                const intercept = (sumY - slope * sumX) / n;
                
                // Calculate R-squared
                const meanY = sumY / n;
                let ssRes = 0, ssTot = 0;
                
                for (let i = 0; i < n; i++) {
                    const predicted = slope * x[i] + intercept;
                    ssRes += Math.pow(prices[i] - predicted, 2);
                    ssTot += Math.pow(prices[i] - meanY, 2);
                }
                
                const rSquared = 1 - (ssRes / ssTot);
                
                return { slope, intercept, rSquared };
            }

            determineTrendType(slope, rSquared) {
                const avgPrice = this.data.reduce((sum, d) => sum + d.close, 0) / this.data.length;
                const normalizedSlope = slope / avgPrice;
                
                if (rSquared < 0.25) return 'Sideways';
                
                if (normalizedSlope > this.trendThreshold) return 'Uptrend';
                if (normalizedSlope < -this.trendThreshold) return 'Downtrend';
                return 'Sideways';
            }

            findSupportResistance(highs, lows, closes) {
                const supports = this.findKeyLevels(lows, 'support');
                const resistances = this.findKeyLevels(highs, 'resistance');
                
                return {
                    supportLevels: supports,
                    resistanceLevels: resistances
                };
            }

            findKeyLevels(prices, type) {
                const levels = [];
                const lookback = 2;
                
                for (let i = lookback; i < prices.length - lookback; i++) {
                    const current = prices[i];
                    let isExtremum = true;
                    
                    for (let j = i - lookback; j <= i + lookback; j++) {
                        if (j === i) continue;
                        
                        if (type === 'support' && prices[j] < current) {
                            isExtremum = false;
                            break;
                        } else if (type === 'resistance' && prices[j] > current) {
                            isExtremum = false;
                            break;
                        }
                    }
                    
                    if (isExtremum) {
                        levels.push(current);
                    }
                }
                
                return this.filterSimilarLevels(levels);
            }

            filterSimilarLevels(levels) {
                const filtered = [];
                const threshold = 0.0005; // 0.05% threshold
                
                levels.forEach(level => {
                    const isSimilar = filtered.some(existing => 
                        Math.abs(level - existing) < threshold
                    );
                    if (!isSimilar) {
                        filtered.push(level);
                    }
                });
                
                return filtered;
            }

            calculateStatistics(prices, highs, lows) {
                const returns = [];
                for (let i = 1; i < prices.length; i++) {
                    returns.push((prices[i] - prices[i-1]) / prices[i-1]);
                }
                
                const meanReturn = returns.reduce((a, b) => a + b, 0) / returns.length;
                const variance = returns.reduce((sum, ret) => sum + Math.pow(ret - meanReturn, 2), 0) / returns.length;
                
                return {
                    maxPrice: Math.max(...highs),
                    minPrice: Math.min(...lows),
                    avgPrice: prices.reduce((a, b) => a + b, 0) / prices.length,
                    volatility: Math.sqrt(variance) * 100
                };
            }

            createChart() {
                const chartContainer = document.getElementById('chartContainer');
                
                this.chart = LightweightCharts.createChart(chartContainer, {
                    width: chartContainer.clientWidth-300,
                    height: 600,
                    layout: {
                        background: { type: 'solid', color: '#ffffff' },
                        textColor: '#333',
                    },
                    grid: {
                        vertLines: { color: '#f0f0f0' },
                        horzLines: { color: '#f0f0f0' },
                    },
                    crosshair: {
                        mode: LightweightCharts.CrosshairMode.Normal,
                    },
                    priceScale: {
                        borderColor: '#cccccc',
                    },
                    timeScale: {
                        borderColor: '#cccccc',
                        timeVisible: true,
                        secondsVisible: false,
                    },
                });

                this.candleSeries = this.chart.addCandlestickSeries({
                    upColor: '#26a69a',
                    downColor: '#ef5350',
                    borderVisible: false,
                    wickUpColor: '#26a69a',
                    wickDownColor: '#ef5350',
                });

                this.candleSeries.setData(this.data);
            }

            addTrendAnalysisToChart(analysisResults) {
                // Add background rectangles for trend zones
                analysisResults.forEach((result, index) => {
                    const color = this.getTrendColor(result.trend.type);
                    
                    // Create background rectangle for trend zone
                    const rectSeries = this.chart.addAreaSeries({
                        topColor: color,
                        bottomColor: color,
                        lineColor: 'transparent',
                        lineWidth: 0,
                        priceLineVisible: false,
                        lastValueVisible: false,
                    });

                    // Create data points for the rectangle
                    const startIndex = index * Math.floor(this.data.length / analysisResults.length);
                    const endIndex = Math.min((index + 1) * Math.floor(this.data.length / analysisResults.length), this.data.length - 1);
                    
                    const minPrice = Math.min(...this.data.slice(startIndex, endIndex + 1).map(d => d.low)) - 0.001;
                    const maxPrice = Math.max(...this.data.slice(startIndex, endIndex + 1).map(d => d.high)) + 0.001;

                    const rectData = [
                        { time: this.data[startIndex].time, value: minPrice },
                        { time: this.data[endIndex].time, value: minPrice }
                    ];

                    rectSeries.setData(rectData);
                });

                // Add support and resistance lines
                const allSupports = [];
                const allResistances = [];

                analysisResults.forEach(result => {
                    allSupports.push(...result.supportResistance.supportLevels);
                    allResistances.push(...result.supportResistance.resistanceLevels);
                });

                // Remove duplicates and add horizontal lines
                [...new Set(allSupports)].forEach(level => {
                    this.addHorizontalLine(level, '#27ae60', 'Support');
                });

                [...new Set(allResistances)].forEach(level => {
                    this.addHorizontalLine(level, '#e74c3c', 'Resistance');
                });
            }

            addHorizontalLine(price, color, label) {
                const lineSeries = this.chart.addLineSeries({
                    color: color,
                    lineWidth: 2,
                    lineStyle: LightweightCharts.LineStyle.Dashed,
                    priceLineVisible: false,
                    lastValueVisible: false,
                });

                const lineData = [
                    { time: this.data[0].time, value: price },
                    { time: this.data[this.data.length - 1].time, value: price }
                ];

                lineSeries.setData(lineData);
            }

            getTrendColor(trendType) {
                switch (trendType) {
                    case 'Uptrend': return 'rgba(39, 174, 96, 0.1)';
                    case 'Downtrend': return 'rgba(231, 76, 60, 0.1)';
                    case 'Sideways': return 'rgba(0, 0, 0, 0.1)';
                    default: return 'rgba(149, 165, 166, 0.1)';
                }
            }

            updateAnalysisDisplay(results) {
                // Update trend summary
                const trendCounts = {};
                results.forEach(r => {
                    trendCounts[r.trend.type] = (trendCounts[r.trend.type] || 0) + 1;
                });

                const trendSummaryEl = document.getElementById('trendSummary');
                trendSummaryEl.innerHTML = Object.entries(trendCounts)
                    .map(([trend, count]) => 
                        `<span class="trend-indicator ${trend.toLowerCase()}">${trend}: ${count}</span>`
                    ).join('');

                // Update statistics
                document.getElementById('totalSegments').textContent = results.length;
                const dominantTrend = Object.keys(trendCounts).reduce((a, b) => 
                    trendCounts[a] > trendCounts[b] ? a : b
                );
                document.getElementById('dominantTrend').textContent = dominantTrend;

                // Count support/resistance levels
                const allSupports = results.flatMap(r => r.supportResistance.supportLevels);
                const allResistances = results.flatMap(r => r.supportResistance.resistanceLevels);
                
                document.getElementById('supportCount').textContent = [...new Set(allSupports)].length;
                document.getElementById('resistanceCount').textContent = [...new Set(allResistances)].length;

                // Update S/R levels display
                const srLevelsEl = document.getElementById('srLevels');
                const uniqueSupports = [...new Set(allSupports)].slice(0, 5);
                const uniqueResistances = [...new Set(allResistances)].slice(0, 5);
                
                srLevelsEl.innerHTML = `
                    <div style="margin-bottom: 15px;">
                        <strong>🟢 Key Support Levels:</strong><br>
                        ${uniqueSupports.map(s => s.toFixed(4)).join(', ') || 'None identified'}
                    </div>
                    <div>
                        <strong>🔴 Key Resistance Levels:</strong><br>
                        ${uniqueResistances.map(r => r.toFixed(4)).join(', ') || 'None identified'}
                    </div>
                `;

                // Update details table
                this.updateDetailsTable(results);
            }

            updateDetailsTable(results) {
                const tbody = document.getElementById('tableBody');
                tbody.innerHTML = results.map((result, index) => `
                    <tr>
                        <td>Segment ${index + 1}</td>
                        <td>
                            <span class="trend-indicator ${result.trend.type.toLowerCase()}">
                                ${result.trend.type}
                            </span>
                        </td>
                        <td>
                            ${(result.trend.strength * 100).toFixed(1)}%<br>
                            <small>R² = ${result.trend.strength.toFixed(3)}</small>
                        </td>
                        <td style="color: ${result.priceRange.changePercent >= 0 ? '#27ae60' : '#e74c3c'}; font-weight: bold;">
                            ${result.priceRange.changePercent.toFixed(2)}%
                        </td>
                        <td>${result.statistics.volatility.toFixed(2)}%</td>
                        <td>
                            <small>
                                S: ${result.supportResistance.supportLevels.slice(0, 2).map(s => s.toFixed(4)).join(', ')}<br>
                                R: ${result.supportResistance.resistanceLevels.slice(0, 2).map(r => r.toFixed(4)).join(', ')}
                            </small>
                        </td>
                    </tr>
                `).join('');
            }
        }

        // Initialize the application
        document.addEventListener('DOMContentLoaded', function() {
			
			
            const analyzer = new CandlestickAnalyzer(sampleData);
			
            
            // Create chart
            analyzer.createChart();
            
            // Analyze trends
            const results = analyzer.analyzeTrends();
            
            // Add analysis to chart
            analyzer.addTrendAnalysisToChart(results);
            
            // Update display
            analyzer.updateAnalysisDisplay(results);
            
            // Handle window resize
            window.addEventListener('resize', () => {
                analyzer.chart.applyOptions({
                    width: document.getElementById('chartContainer').clientWidth
                });
            });
			
        });

        // Function to load data from PHP (you can call this with your actual data)
        function loadDataFromPHP(phpData) {
            // Convert PHP data format to the required format
            const convertedData = Object.entries(phpData).map(([timestamp, ohlc]) => ({
                time: new Date(parseInt(timestamp) * 1000).toISOString().split('T')[0],
                open: ohlc.open,
                high: ohlc.high,
                low: ohlc.low,
                close: ohlc.close
            }));
            
            // Re-initialize analyzer with new data
            const analyzer = new CandlestickAnalyzer(convertedData);
            analyzer.createChart();
            const results = analyzer.analyzeTrends();
            analyzer.addTrendAnalysisToChart(results);
            analyzer.updateAnalysisDisplay(results);
        }
      </script>
        // PHP Integration Example:
        /*
        // In your PHP file, you can output the analysis data like this:
        
        <?php
        // Your candlestick data from Deriv.com
        $candlestick_data = [
            1640995200 => ['open' => 1.1345, 'high' => 1.1367, 'low' => 1.1340, 'close' => 1.1355],
            1640998800 => ['open' => 1.1355, 'high' => 1.1372, 'low' => 1.1351, 'close' => 1.1368],
            // ... more data
        ];

        // Create analyzer instance
        $analyzer = new CandlestickAnalyzer($candlestick_data);
        $results = $analyzer->analyzeTrends();
        
        // Output data for JavaScript
        echo "<script>";
        echo "const phpCandlestickData = " . json_encode($candlestick_data) . ";";
        echo "const phpAnalysisResults = " . json_encode($results) . ";";
        echo "loadDataFromPHP(phpCandlestickData);";
        echo "</script>";
        ?>
        */