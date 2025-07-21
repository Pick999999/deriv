<?php
ob_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

class CandlestickTrendAnalyzer {
    
    private $data;
    private $minPeriod;
    private $trendThreshold;
    
    public function __construct($candlestickData, $minPeriod = 10, $trendThreshold = 0.005) {
        $this->data = $candlestickData;
        $this->minPeriod = $minPeriod;
        $this->trendThreshold = $trendThreshold; // 0.5% threshold for trend determination
    }
    
    /**
     * วิเคราะห์เทรนด์และแนวรับ-แนวต้าน
     */
    public function analyzeTrends() {
        $results = [];
        $dataCount = count($this->data);
        
        if ($dataCount < $this->minPeriod) {
            return $results;
        }
        
        // แบ่งข้อมูลเป็นช่วงๆ
        $segments = $this->segmentData();
        
        foreach ($segments as $segment) {
            $analysis = $this->analyzeSegment($segment);
            $results[] = $analysis;
        }
        
        return $results;
    }
    
    /**
     * แบ่งข้อมูลเป็นช่วงๆ สำหรับการวิเคราะห์
     */
    private function segmentData() {
        $segments = [];
        $dataCount = count($this->data);
        $segmentSize = max($this->minPeriod, intval($dataCount / 8)); // แบ่งเป็น 8 ส่วน หรือขั้นต่ำ minPeriod
        
        for ($i = 0; $i < $dataCount; $i += $segmentSize) {
            $end = min($i + $segmentSize, $dataCount);
            if ($end - $i >= $this->minPeriod) {
                $segments[] = array_slice($this->data, $i, $end - $i, true);
            }
        }
        
        return $segments;
    }
    
    /**
     * วิเคราะห์ส่วนของข้อมูล
     */
    private function analyzeSegment($segment) {
        $prices = array_column($segment, 'close');
        $highs = array_column($segment, 'high');
        $lows = array_column($segment, 'low');
        $timestamps = array_keys($segment);
        
        // คำนวณ linear regression เพื่อหาทิศทาง
        $trend = $this->calculateTrend($prices);
        $trendType = $this->determineTrendType($trend['slope'], $trend['r_squared']);
        
        // หาแนวรับและแนวต้าน
        $supportResistance = $this->findSupportResistance($highs, $lows, $prices);
        
        // คำนวณ statistics
        $stats = $this->calculateStatistics($prices, $highs, $lows);
        
        return [
            'period' => [
                'start' => $timestamps[0],
                'end' => end($timestamps),
                'candles' => count($segment)
            ],
            'trend' => [
                'type' => $trendType,
                'slope' => $trend['slope'],
                'strength' => $trend['r_squared'],
                'angle' => rad2deg(atan($trend['slope']))
            ],
            'support_resistance' => $supportResistance,
            'statistics' => $stats,
            'price_range' => [
                'start_price' => $prices[0],
                'end_price' => end($prices),
                'change_percent' => ((end($prices) - $prices[0]) / $prices[0]) * 100
            ]
        ];
    }
    
    /**
     * คำนวณ Linear Regression
     */
    private function calculateTrend($prices) {
        $n = count($prices);
        $x = range(0, $n - 1);
        
        $sum_x = array_sum($x);
        $sum_y = array_sum($prices);
        $sum_xy = 0;
        $sum_x2 = 0;
        $sum_y2 = 0;
        
        for ($i = 0; $i < $n; $i++) {
            $sum_xy += $x[$i] * $prices[$i];
            $sum_x2 += $x[$i] * $x[$i];
            $sum_y2 += $prices[$i] * $prices[$i];
        }
        
        $slope = ($n * $sum_xy - $sum_x * $sum_y) / ($n * $sum_x2 - $sum_x * $sum_x);
        $intercept = ($sum_y - $slope * $sum_x) / $n;
        
        // คำนวณ R-squared
        $ss_res = 0;
        $ss_tot = 0;
        $mean_y = $sum_y / $n;
        
        for ($i = 0; $i < $n; $i++) {
            $predicted = $slope * $x[$i] + $intercept;
            $ss_res += pow($prices[$i] - $predicted, 2);
            $ss_tot += pow($prices[$i] - $mean_y, 2);
        }
        
        $r_squared = 1 - ($ss_res / $ss_tot);
        
        return [
            'slope' => $slope,
            'intercept' => $intercept,
            'r_squared' => $r_squared
        ];
    }
    
    /**
     * กำหนดประเภทของเทรนด์
     */
    private function determineTrendType($slope, $r_squared) {
        $avg_price = array_sum(array_column($this->data, 'close')) / count($this->data);
        $normalized_slope = $slope / $avg_price;
        
        // ต้องมีความแน่นอนพอสมควร (R² > 0.3) จึงจะถือว่าเป็น trend
        if ($r_squared < 0.3) {
            return 'Sideways';
        }
        
        if ($normalized_slope > $this->trendThreshold) {
            return 'Uptrend';
        } elseif ($normalized_slope < -$this->trendThreshold) {
            return 'Downtrend';
        } else {
            return 'Sideways';
        }
    }
    
    /**
     * หาแนวรับและแนวต้าน
     */
    private function findSupportResistance($highs, $lows, $closes) {
        $supports = $this->findKeyLevels($lows, 'support');
        $resistances = $this->findKeyLevels($highs, 'resistance');
        
        // กรองระดับที่อยู่ใกล้ราคาปิดล่าสุด
        $current_price = end($closes);
        $price_range = (max($highs) - min($lows)) * 0.1; // 10% ของ range
        
        $active_supports = array_filter($supports, function($level) use ($current_price, $price_range) {
            return $level <= $current_price && ($current_price - $level) <= $price_range;
        });
        
        $active_resistances = array_filter($resistances, function($level) use ($current_price, $price_range) {
            return $level >= $current_price && ($level - $current_price) <= $price_range;
        });
        
        return [
            'support_levels' => array_values($active_supports),
            'resistance_levels' => array_values($active_resistances),
            'nearest_support' => empty($active_supports) ? null : max($active_supports),
            'nearest_resistance' => empty($active_resistances) ? null : min($active_resistances)
        ];
    }
    
    /**
     * หาระดับสำคัญ (Support/Resistance)
     */
    private function findKeyLevels($prices, $type = 'support') {
        $levels = [];
        $lookback = 3; // ดูข้อมูลก่อนหน้า-หลัง 3 periods
        
        for ($i = $lookback; $i < count($prices) - $lookback; $i++) {
            $current = $prices[$i];
            $is_extremum = true;
            
            // ตรวจสอบว่าเป็น local minimum (support) หรือ maximum (resistance)
            for ($j = $i - $lookback; $j <= $i + $lookback; $j++) {
                if ($j == $i) continue;
                
                if ($type == 'support' && $prices[$j] < $current) {
                    $is_extremum = false;
                    break;
                } elseif ($type == 'resistance' && $prices[$j] > $current) {
                    $is_extremum = false;
                    break;
                }
            }
            
            if ($is_extremum) {
                $levels[] = $current;
            }
        }
        
        // ลบระดับที่ใกล้เคียงกัน
        $filtered_levels = [];
        $threshold = (max($prices) - min($prices)) * 0.01; // 1% threshold
        
        foreach ($levels as $level) {
            $is_unique = true;
            foreach ($filtered_levels as $existing) {
                if (abs($level - $existing) < $threshold) {
                    $is_unique = false;
                    break;
                }
            }
            if ($is_unique) {
                $filtered_levels[] = $level;
            }
        }
        
        return $filtered_levels;
    }
    
    /**
     * คำนวณสถิติต่างๆ
     */
    private function calculateStatistics($prices, $highs, $lows) {
        return [
            'max_price' => max($highs),
            'min_price' => min($lows),
            'avg_price' => array_sum($prices) / count($prices),
            'volatility' => $this->calculateVolatility($prices),
            'price_range' => max($highs) - min($lows)
        ];
    }
    
    /**
     * คำนวณความผันผวน (Volatility)
     */
    private function calculateVolatility($prices) {
        $returns = [];
        for ($i = 1; $i < count($prices); $i++) {
            $returns[] = ($prices[$i] - $prices[$i-1]) / $prices[$i-1];
        }
        
        $mean_return = array_sum($returns) / count($returns);
        $variance = 0;
        
        foreach ($returns as $return) {
            $variance += pow($return - $mean_return, 2);
        }
        
        return sqrt($variance / count($returns)) * 100; // เป็นเปอร์เซ็นต์
    }
    
    /**
     * สร้าง HTML Table สำหรับแสดงผล
     */
    public function generateHTMLTable($analysis_results) {
        $html = '<div style="font-family: Arial, sans-serif;">';
        $html .= '<h2>Candlestick Trend Analysis Report</h2>';
        
        if (empty($analysis_results)) {
            return $html . '<p>No data available for analysis.</p></div>';
        }
        
        $html .= '<table border="1" cellpadding="10" cellspacing="0" style="border-collapse: collapse; width: 100%;">';
        $html .= '<thead style="background-color: #f2f2f2;">';
        $html .= '<tr>';
        $html .= '<th>Period</th>';
        $html .= '<th>Trend Type</th>';
        $html .= '<th>Trend Strength</th>';
        $html .= '<th>Price Change</th>';
        $html .= '<th>Support Levels</th>';
        $html .= '<th>Resistance Levels</th>';
        $html .= '<th>Volatility</th>';
        $html .= '<th>Price Range</th>';
        $html .= '</tr>';
        $html .= '</thead>';
        $html .= '<tbody>';
        
        foreach ($analysis_results as $index => $result) {
            $trend_color = $this->getTrendColor($result['trend']['type']);
            $change_color = $result['price_range']['change_percent'] >= 0 ? 'green' : 'red';
            
            $html .= '<tr>';
            $html .= '<td>Segment ' . ($index + 1) . '<br><small>' . $result['period']['candles'] . ' candles</small></td>';
            $html .= '<td style="background-color: ' . $trend_color . '; color: white; font-weight: bold;">' . 
                     $result['trend']['type'] . '<br><small>R² = ' . number_format($result['trend']['strength'], 3) . '</small></td>';
            $html .= '<td>' . $this->getStrengthText($result['trend']['strength']) . '<br><small>Angle: ' . 
                     number_format($result['trend']['angle'], 1) . '°</small></td>';
            $html .= '<td style="color: ' . $change_color . '; font-weight: bold;">' . 
                     number_format($result['price_range']['change_percent'], 2) . '%<br><small>' . 
                     number_format($result['price_range']['start_price'], 4) . ' → ' . 
                     number_format($result['price_range']['end_price'], 4) . '</small></td>';
            
            // Support Levels
            $supports = $result['support_resistance']['support_levels'];
            $support_text = empty($supports) ? 'None' : implode('<br>', array_map(function($s) { 
                return number_format($s, 4); 
            }, array_slice($supports, 0, 3)));
            if (count($supports) > 3) $support_text .= '<br>+' . (count($supports) - 3) . ' more';
            $html .= '<td>' . $support_text . '</td>';
            
            // Resistance Levels
            $resistances = $result['support_resistance']['resistance_levels'];
            $resistance_text = empty($resistances) ? 'None' : implode('<br>', array_map(function($r) { 
                return number_format($r, 4); 
            }, array_slice($resistances, 0, 3)));
            if (count($resistances) > 3) $resistance_text .= '<br>+' . (count($resistances) - 3) . ' more';
            $html .= '<td>' . $resistance_text . '</td>';
            
            $html .= '<td>' . number_format($result['statistics']['volatility'], 2) . '%</td>';
            $html .= '<td>' . number_format($result['statistics']['min_price'], 4) . ' - ' . 
                     number_format($result['statistics']['max_price'], 4) . '</td>';
            $html .= '</tr>';
        }
        
        $html .= '</tbody>';
        $html .= '</table>';
        
        // เพิ่มสรุปภาพรวม
        $html .= $this->generateSummary($analysis_results);
        
        $html .= '</div>';
        return $html;
    }
    
    private function getTrendColor($trend_type) {
        switch ($trend_type) {
            case 'Uptrend': return '#28a745';
            case 'Downtrend': return '#dc3545';
            case 'Sideways': return '#6c757d';
            default: return '#6c757d';
        }
    }
    
    private function getStrengthText($r_squared) {
        if ($r_squared >= 0.7) return 'Strong';
        if ($r_squared >= 0.5) return 'Moderate';
        if ($r_squared >= 0.3) return 'Weak';
        return 'Very Weak';
    }
    
    private function generateSummary($results) {
        $trend_counts = array_count_values(array_column(array_column($results, 'trend'), 'type'));
        $total_segments = count($results);
        
        $html = '<div style="margin-top: 20px; padding: 15px; background-color: #f8f9fa; border-radius: 5px;">';
        $html .= '<h3>Summary</h3>';
        $html .= '<p><strong>Total Segments Analyzed:</strong> ' . $total_segments . '</p>';
        
        foreach ($trend_counts as $trend => $count) {
            $percentage = ($count / $total_segments) * 100;
            $html .= '<p><strong>' . $trend . ':</strong> ' . $count . ' segments (' . 
                     number_format($percentage, 1) . '%)</p>';
        }
        
        // หาเทรนด์ที่เด่นที่สุด
        $dominant_trend = array_keys($trend_counts, max($trend_counts))[0];
        $html .= '<p style="font-weight: bold; color: ' . $this->getTrendColor($dominant_trend) . ';">';
        $html .= 'Dominant Trend: ' . $dominant_trend . '</p>';
        
        $html .= '</div>';
        return $html;
    }
}

// ตัวอย่างการใช้งาน
/*
// ตัวอย่างข้อมูล candlestick จาก deriv.com
$sample_data = [
    1640995200 => ['open' => 1.1345, 'high' => 1.1367, 'low' => 1.1340, 'close' => 1.1355],
    1640998800 => ['open' => 1.1355, 'high' => 1.1372, 'low' => 1.1351, 'close' => 1.1368],
    1641002400 => ['open' => 1.1368, 'high' => 1.1385, 'low' => 1.1365, 'close' => 1.1380],
    // ... เพิ่มข้อมูลเพิ่มเติม
];
*/


 $st = "";   
 
 
 $sFileName = '../deriv/rawData.json';
 $file = fopen($sFileName,"r");
 while(! feof($file))  {
   $st .= fgets($file) ;
 }
 fclose($file);

$sample_data = JSON_DECODE($st);

// สร้าง analyzer
$analyzer = new CandlestickTrendAnalyzer($sample_data, 10, 0.005);

// วิเคราะห์เทรนด์
$results = $analyzer->analyzeTrends();

// สร้าง HTML table
$html_report = $analyzer->generateHTMLTable($results);

// แสดงผล
echo $html_report;


?>