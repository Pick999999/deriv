<!DOCTYPE html>
<html>
<head>
    <title>Forex Strength Gauge</title>
    <style>
        .gauge-container {
            width: 400px;
            margin: 20px auto;
            text-align: center;
        }
        canvas {
            display: block;
            margin: 0 auto;
        }
    </style>
</head>
<body>
    <div class="gauge-container">
        <canvas id="gaugeCanvas" width="300" height="300"></canvas>
        <div id="strengthValue" style="font-size: 24px; margin-top: 10px;"></div>
    </div>

    <script>
        class ForexGauge {
            constructor(canvasId) {
                this.canvas = document.getElementById(canvasId);
                this.ctx = this.canvas.getContext('2d');
                this.centerX = this.canvas.width / 2;
                this.centerY = this.canvas.height / 2;
                this.radius = Math.min(this.centerX, this.centerY) * 0.8;
                this.value = 0;
                
                // สร้าง gradient สำหรับสีของเกจ
                this.gradient = this.ctx.createLinearGradient(0, 0, this.canvas.width, 0);
                this.gradient.addColorStop(0, '#ff4444');    // แดง (อ่อนแอ)
                this.gradient.addColorStop(0.5, '#ffeb3b');  // เหลือง (กลาง)
                this.gradient.addColorStop(1, '#4CAF50');    // เขียว (แข็งแกร่ง)
            }

            drawGauge() {
                this.ctx.clearRect(0, 0, this.canvas.width, this.canvas.height);
                
                // วาดเส้นโค้งหลัก
                this.ctx.beginPath();
                this.ctx.strokeStyle = '#e0e0e0';
                this.ctx.lineWidth = 20;
                this.ctx.arc(this.centerX, this.centerY, this.radius, Math.PI, 2 * Math.PI);
                this.ctx.stroke();

                // วาดเส้นโค้งที่แสดงค่า
                this.ctx.beginPath();
                this.ctx.strokeStyle = this.gradient;
                this.ctx.lineWidth = 20;
                const valueInRadians = Math.PI + (this.value / 100) * Math.PI;
                this.ctx.arc(this.centerX, this.centerY, this.radius, Math.PI, valueInRadians);
                this.ctx.stroke();

                // วาดขีดบอกระดับ
                for (let i = 0; i <= 10; i++) {
                    const angle = Math.PI + (i / 10) * Math.PI;
                    const startRadius = this.radius - 30;
                    const endRadius = this.radius + 10;
                    
                    this.ctx.beginPath();
                    this.ctx.strokeStyle = '#666';
                    this.ctx.lineWidth = 2;
                    this.ctx.moveTo(
                        this.centerX + Math.cos(angle) * startRadius,
                        this.centerY + Math.sin(angle) * startRadius
                    );
                    this.ctx.lineTo(
                        this.centerX + Math.cos(angle) * endRadius,
                        this.centerY + Math.sin(angle) * endRadius
                    );
                    this.ctx.stroke();

                    // เพิ่มตัวเลขกำกับ
                    const labelRadius = this.radius + 25;
                    this.ctx.fillStyle = '#333';
                    this.ctx.font = '12px Arial';
                    this.ctx.textAlign = 'center';
                    this.ctx.fillText(
                        i * 10,
                        this.centerX + Math.cos(angle) * labelRadius,
                        this.centerY + Math.sin(angle) * labelRadius
                    );
                }

                // วาดเข็ม
                const needleAngle = Math.PI + (this.value / 100) * Math.PI;
                const needleLength = this.radius - 40;
                
                this.ctx.beginPath();
                this.ctx.strokeStyle = '#ff0000';
                this.ctx.lineWidth = 3;
                this.ctx.moveTo(this.centerX, this.centerY);
                this.ctx.lineTo(
                    this.centerX + Math.cos(needleAngle) * needleLength,
                    this.centerY + Math.sin(needleAngle) * needleLength
                );
                this.ctx.stroke();

                // วาดจุดกลาง
                this.ctx.beginPath();
                this.ctx.fillStyle = '#333';
                this.ctx.arc(this.centerX, this.centerY, 5, 0, 2 * Math.PI);
                this.ctx.fill();

                // แสดงค่าตัวเลข
                document.getElementById('strengthValue').textContent = 
                    `EUR/USD Strength: ${this.value.toFixed(1)}`;
            }

            updateValue(newValue) {
                this.value = Math.max(0, Math.min(100, newValue));
                this.drawGauge();
            }
        }

        // สร้างและเริ่มต้นใช้งาน
        const gauge = new ForexGauge('gaugeCanvas');
        
        // จำลองการอัพเดทค่าแบบ realtime
        setInterval(() => {
            // สุ่มค่าระหว่าง 0-100 สำหรับการทดสอบ
            const newValue = Math.random() * 100;
            gauge.updateValue(newValue);
        }, 1000);
    </script>
</body>
</html>