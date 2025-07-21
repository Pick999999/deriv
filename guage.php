<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Speed Gauge</title>
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background-color: #f0f0f0;
            margin: 0;
            font-family: Arial, sans-serif;
        }
        .gauge-container {
            position: relative;
            width: 300px;
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        canvas {
            display: block;
            margin-bottom: 20px;
        }
        .control-panel {
            display: flex;
            align-items: center;
            margin-top: 10px;
        }
        input {
            width: 100px;
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 4px;
            margin-right: 10px;
            text-align: center;
        }
        label {
            margin-right: 10px;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="gauge-container">
        <canvas id="gaugeCanvas" width="300" height="300"></canvas>
        <div class="control-panel">
            <label for="valueInput">ค่า (0-100):</label>
            <input type="number" id="valueInput" min="0" max="100" value="60" step="1">
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const canvas = document.getElementById('gaugeCanvas');
            const ctx = canvas.getContext('2d');
            const valueInput = document.getElementById('valueInput');
            
            const centerX = canvas.width / 2;
            const centerY = canvas.height / 2;
            const radius = Math.min(centerX, centerY) * 0.85;
            
            // Current value (can be changed as needed)
            let currentValue = parseInt(valueInput.value);
            
            // Define gauge properties
            const startAngle = 135; // Degrees - corrected start angle (7:30 position)
            const endAngle = 405;   // Degrees - corrected end angle (4:30 position)
            const gaugeArc = endAngle - startAngle; // 270 degrees total arc
            
            function drawGauge() {
                // Clear canvas
                ctx.clearRect(0, 0, canvas.width, canvas.height);
                
                // Draw outer circle
                ctx.beginPath();
                ctx.arc(centerX, centerY, radius, 0, Math.PI * 2);
                ctx.fillStyle = '#404040';
                ctx.fill();
                
                // Draw inner circle (black background)
                ctx.beginPath();
                ctx.arc(centerX, centerY, radius * 0.85, 0, Math.PI * 2);
                ctx.fillStyle = '#202020';
                ctx.fill();
                
                // Draw scale
                drawScale();
                
                // Draw center circle
                ctx.beginPath();
                ctx.arc(centerX, centerY, radius * 0.5, 0, Math.PI * 2);
                ctx.fillStyle = '#101010';
                ctx.fill();
                
                // Draw center text
                drawCenterText();
                
                // Draw bottom section with wrench icon
                drawBottomSection();
                
                // Draw needle
                drawNeedle(currentValue);
                
                // Draw center cap
                ctx.beginPath();
                ctx.arc(centerX, centerY, radius * 0.1, 0, Math.PI * 2);
                ctx.fillStyle = '#505050';
                ctx.fill();
                ctx.strokeStyle = '#707070';
                ctx.lineWidth = 2;
                ctx.stroke();
            }
            
            function drawScale() {
                // Draw tick marks and numbers
                ctx.textAlign = 'center';
                ctx.textBaseline = 'middle';
                
                for (let i = 0; i <= 100; i += 5) {
                    // Convert value to angle
                    const angle = ((i / 100) * gaugeArc + startAngle) * Math.PI / 180;
                    
                    // Determine radius based on tick type
                    const outerRadius = radius * 0.85;
                    const innerRadius = i % 10 === 0 ? radius * 0.7 : radius * 0.75;
                    
                    const outerX = centerX + outerRadius * Math.cos(angle);
                    const outerY = centerY + outerRadius * Math.sin(angle);
                    const innerX = centerX + innerRadius * Math.cos(angle);
                    const innerY = centerY + innerRadius * Math.sin(angle);
                    
                    // Draw tick
                    ctx.beginPath();
                    ctx.moveTo(outerX, outerY);
                    ctx.lineTo(innerX, innerY);
                    ctx.strokeStyle = '#808080';
                    ctx.lineWidth = i % 10 === 0 ? 2 : 1;
                    ctx.stroke();
                    
                    // Draw number for major ticks
                    if (i % 10 === 0) {
                        const textRadius = radius * 0.62;
                        const textX = centerX + textRadius * Math.cos(angle);
                        const textY = centerY + textRadius * Math.sin(angle);
                        
                        ctx.fillStyle = '#FFFFFF';
                        ctx.font = `${radius * 0.1}px Arial`;
                        
                        // Special labels for specific positions
                        if (i === 50) {
                            // 1Gb at middle bottom position
                            ctx.fillText('1Gb', centerX, centerY + radius * 0.3);
                        } else if (i === 0) {
                            // 0 at 7:30 position
                            ctx.fillText('0', textX, textY);
                        } else if (i === 100) {
                            // 10Gb at 4:30 position
                            ctx.fillText('10Gb', textX, textY);
                        } else {
                            // Regular numbers
                            ctx.fillText(i.toString(), textX, textY);
                        }
                    }
                }
            }
            
            function drawCenterText() {
                ctx.textAlign = 'center';
                ctx.textBaseline = 'middle';
                
                // Main text
                ctx.fillStyle = '#FFFFFF';
                ctx.font = `bold ${radius * 0.12}px Arial`;
                ctx.fillText('แบนด์วิดท์คงเหลือ', centerX, centerY - radius * 0.1);
                
                // Blue "เร็วไหม" text
                ctx.fillStyle = '#3498db';
                ctx.font = `${radius * 0.1}px Arial`;
                ctx.fillText('เร็วไหม', centerX, centerY + radius * 0.1);
                
                // Draw refresh icon
                drawRefreshIcon(centerX, centerY + radius * 0.22, radius * 0.05);
            }
            
            function drawRefreshIcon(x, y, size) {
                ctx.beginPath();
                ctx.arc(x, y, size, 0, Math.PI * 1.5);
                ctx.strokeStyle = '#3498db';
                ctx.lineWidth = size * 0.4;
                ctx.stroke();
                
                // Arrow tip
                const arrowX = x;
                const arrowY = y - size;
                ctx.beginPath();
                ctx.moveTo(arrowX, arrowY);
                ctx.lineTo(arrowX + size * 0.6, arrowY - size * 0.4);
                ctx.lineTo(arrowX - size * 0.6, arrowY - size * 0.4);
                ctx.fillStyle = '#3498db';
                ctx.fill();
            }
            
            function drawBottomSection() {
                // Draw semicircle at the bottom
                ctx.beginPath();
                ctx.arc(centerX, centerY, radius * 0.35, 0, Math.PI, true);
                ctx.lineTo(centerX - radius * 0.35, centerY);
                ctx.fillStyle = '#303030';
                ctx.fill();
                
                // Draw wrench icon
                drawWrenchIcon(centerX, centerY + radius * 0.18, radius * 0.12);
            }
            
            function drawWrenchIcon(x, y, size) {
                ctx.save();
                ctx.translate(x, y);
                ctx.rotate(Math.PI / 4); // Rotate 45 degrees
                
                // Handle
                ctx.beginPath();
                ctx.rect(-size * 0.15, -size, size * 0.3, size * 1.5);
                ctx.fillStyle = '#4682B4';
                ctx.fill();
                
                // Head
                ctx.beginPath();
                ctx.arc(0, -size, size * 0.5, 0, Math.PI * 2);
                ctx.fillStyle = '#4682B4';
                ctx.fill();
                
                // Inner circle of head
                ctx.beginPath();
                ctx.arc(0, -size, size * 0.25, 0, Math.PI * 2);
                ctx.fillStyle = '#303030';
                ctx.fill();
                
                ctx.restore();
            }
            
            function drawNeedle(value) {
                // Convert value to angle
                const angle = ((value / 100) * gaugeArc + startAngle) * Math.PI / 180;
                
                // Draw needle
                ctx.save();
                ctx.translate(centerX, centerY);
                ctx.rotate(angle);
                
                // Needle shape
                ctx.beginPath();
                ctx.moveTo(-radius * 0.1, 0);
                ctx.lineTo(0, -radius * 0.02);
                ctx.lineTo(radius * 0.7, 0);
                ctx.lineTo(0, radius * 0.02);
                ctx.closePath();
                ctx.fillStyle = '#FFFFFF';
                ctx.fill();
                
                ctx.restore();
            }
            
            // Draw the gauge initially
            drawGauge();
            
            // Animate the needle to initial value
            animateNeedle(0, currentValue);
            
            // Handle input change
            valueInput.addEventListener('input', function() {
                const newValue = parseInt(this.value);
                
                // Validate input range
                if (isNaN(newValue) || newValue < 0) {
                    this.value = 0;
                    animateNeedle(currentValue, 0);
                    currentValue = 0;
                } else if (newValue > 100) {
                    this.value = 100;
                    animateNeedle(currentValue, 100);
                    currentValue = 100;
                } else {
                    animateNeedle(currentValue, newValue);
                    currentValue = newValue;
                }
            });
            
            // Animate the needle with smooth transition
            function animateNeedle(fromValue, toValue) {
                let value = fromValue;
                const targetValue = toValue;
                const increment = fromValue < toValue ? 1 : -1;
                const interval = 10;
                
                // Clear any existing animation
                if (window.needleAnimation) {
                    clearInterval(window.needleAnimation);
                }
                
                window.needleAnimation = setInterval(() => {
                    value += increment;
                    if ((increment > 0 && value >= targetValue) || 
                        (increment < 0 && value <= targetValue)) {
                        value = targetValue;
                        clearInterval(window.needleAnimation);
                    }
                    
                    drawGauge();
                    drawNeedle(value);
                }, interval);
            }
        });
    </script>
</body>
</html>