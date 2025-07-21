<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ไมล์รถยนต์แบบสมบูรณ์</title>
    <style>
        body {
            display: flex;
            flex-direction: column;
            align-items: center;
            min-height: 100vh;
            background-color: #f0f0f0;
            font-family: Arial, sans-serif;
            padding: 20px;
        }
        
        .speedometer-container {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 40px;
            margin: 20px 0;
        }
        
        .speedometer-wrapper {
            margin: 20px;
            text-align: center;
        }
        
        .speedometer {
            position: relative;
            width: 300px;
            height: 300px;
        }
        
        .dial {
            position: absolute;
            width: 100%;
            height: 100%;
            border-radius: 50%;
            background: #222;
            border: 8px solid #444;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.5);
        }
        
        .needle {
            position: absolute;
            width: 4px;
            height: 120px;
            background: red;
            left: 50%;
            top: 50%;
            transform-origin: 50% 0;
            transform: translateX(-50%) rotate(-45deg);
            z-index: 10;
            border-radius: 4px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.3);
            transition: transform 0.5s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        .needle::after {
            content: '';
            position: absolute;
            width: 20px;
            height: 20px;
            background: #333;
            border-radius: 50%;
            left: 50%;
            top: 0;
            transform: translate(-50%, -50%);
            border: 2px solid #555;
        }
        
        .marks {
            position: absolute;
            width: 100%;
            height: 100%;
        }
        
        .mark {
            position: absolute;
            width: 2px;
            height: 15px;
            background: white;
            left: 50%;
            top: 10px;
            transform-origin: 50% 140px;
        }
        
        .mark.big {
            height: 25px;
            width: 4px;
            background: #fff;
        }
        
        .numbers {
            position: absolute;
            width: 100%;
            height: 100%;
            color: white;
            font-weight: bold;
        }
        
        .number {
            position: absolute;
            font-size: 16px;
            text-align: center;
            width: 30px;
            height: 30px;
            line-height: 30px;
            margin-left: -15px;
            margin-top: -15px;
        }
        
        .value-display {
            position: absolute;
            bottom: 30px;
            left: 50%;
            transform: translateX(-50%);
            background: #000;
            color: white;
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 24px;
            font-weight: bold;
            border: 2px solid #444;
        }
        
        .controls {
            margin-top: 20px;
            text-align: center;
        }
        
        input[type="range"] {
            width: 300px;
        }
        
        .auto-adjust-btn {
            margin-top: 10px;
            padding: 8px 15px;
            background: #4CAF50;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            margin: 5px;
        }
        
        .auto-adjust-btn:hover {
            background: #45a049;
        }
        
        h2 {
            color: #333;
            margin-bottom: 5px;
        }
    </style>
</head>
<body>
    <h1>Speedometer ที่แก้ไขแล้ว</h1>
    
    <div class="speedometer-container">
        <div class="speedometer-wrapper">
            <h2>มาตรวัดความเร็ว</h2>
            <div class="speedometer" id="speedo1"></div>
            <div class="controls">
                <input type="range" class="speed-input" min="0" max="100" value="0" step="1">
                <div>
                    <button class="auto-adjust-btn" data-target="0">0</button>
                    <button class="auto-adjust-btn" data-target="30">30</button>
                    <button class="auto-adjust-btn" data-target="50">50</button>
                    <button class="auto-adjust-btn" data-target="70">70</button>
                    <button class="auto-adjust-btn" data-target="100">100</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        class Speedometer {
            constructor(containerId, options = {}) {
                this.container = document.getElementById(containerId);
                if (!this.container) {
                    console.error(`Container with ID ${containerId} not found`);
                    return;
                }
                
                this.options = {
                    minValue: 0,
                    maxValue: 100,
                    initialValue: 0,
                    ...options
                };
                
                this.currentValue = this.options.initialValue;
                this.isAutoAdjusting = false;
                this.minAngle = -135; // มุมเริ่มต้น (ซ้ายสุด)
                this.maxAngle = 135;   // มุมสิ้นสุด (ขวาสุด)
				this.minAngle = -135; // มุมเริ่มต้น (ซ้ายสุด)
                this.maxAngle = 135;   // มุมสิ้นสุด (ขวาสุด)

                this.angleRange = this.maxAngle - this.minAngle;
                
                this.init();
            }
            
            init() {
                this.container.innerHTML = '';
                
                // สร้างส่วนประกอบต่างๆ
                const dial = document.createElement('div');
                dial.className = 'dial';
                this.container.appendChild(dial);
                
                this.marksContainer = document.createElement('div');
                this.marksContainer.className = 'marks';
                this.container.appendChild(this.marksContainer);
                
                this.numbersContainer = document.createElement('div');
                this.numbersContainer.className = 'numbers';
                this.container.appendChild(this.numbersContainer);
                
                this.needle = document.createElement('div');
                this.needle.className = 'needle';
                this.container.appendChild(this.needle);
                
                this.valueDisplay = document.createElement('div');
                this.valueDisplay.className = 'value-display';
                this.valueDisplay.textContent = this.currentValue;
                this.container.appendChild(this.valueDisplay);
                
                this.createMarks();
                this.setValue(this.options.initialValue);
            }
            
            createMarks() {
                for (let i = this.options.minValue; i <= this.options.maxValue; i += 5) {
                    const mark = document.createElement('div');
                    mark.className = i % 10 === 0 ? 'mark big' : 'mark';
                    mark.style.transform = `rotate(${this.getValueAngle(i)}deg)`;
                    this.marksContainer.appendChild(mark);
                    
                    if (i % 20 === 0 || i === this.options.minValue || i === this.options.maxValue) {
                        const number = document.createElement('div');
                        number.className = 'number';
                        number.textContent = i;
                        
                        const angle = this.getValueAngle(i) * Math.PI / 180;
                        const radius = 110;
                        const center = 150;
                        const x = center + Math.sin(angle) * radius;
                        const y = center - Math.cos(angle) * radius;
                        
                        number.style.left = `${x}px`;
                        number.style.top = `${y}px`;
                        this.numbersContainer.appendChild(number);
                    }
                }
            }
            
            getValueAngle(value) {
                const normalizedValue = Math.max(this.options.minValue, Math.min(this.options.maxValue, value));
                const percentage = (normalizedValue - this.options.minValue) / 
                                 (this.options.maxValue - this.options.minValue);
                return this.minAngle + (this.angleRange * percentage);
            }
            
            setValue(value) {
                this.currentValue = Math.max(this.options.minValue, Math.min(this.options.maxValue, value));
                const angle = this.getValueAngle(this.currentValue)-180;
                this.needle.style.transform = `translateX(-50%) rotate(${angle}deg)`;
                this.valueDisplay.textContent = this.currentValue;
            }
            
            autoAdjustTo(targetValue, duration = 1000) {
                if (this.isAutoAdjusting) return;
                
                this.isAutoAdjusting = true;
                const startValue = this.currentValue;
                const change = targetValue - startValue;
                const startTime = performance.now();
                
                const animate = (currentTime) => {
                    const elapsed = currentTime - startTime;
                    const progress = Math.min(elapsed / duration, 1);
                    
                    const easedProgress = this.easeInOutCubic(progress);
                    const current = startValue + (change * easedProgress);
                    
                    this.setValue(current);
                    
                    if (progress < 1) {
                        requestAnimationFrame(animate);
                    } else {
                        this.isAutoAdjusting = false;
                    }
                };
                
                requestAnimationFrame(animate);
            }
            
            easeInOutCubic(t) {
                return t < 0.5 ? 4 * t * t * t : 1 - Math.pow(-2 * t + 2, 3) / 2;
            }
        }

        // เริ่มต้นเมื่อโหลดหน้าเสร็จ
        document.addEventListener('DOMContentLoaded', function() {
            const speedo1 = new Speedometer('speedo1', { initialValue: 0 });
            const slider = document.querySelector('.speed-input');
            
            slider.addEventListener('input', function() {
                speedo1.setValue(parseInt(this.value));
            });
            
            document.querySelectorAll('.auto-adjust-btn').forEach(button => {
                button.addEventListener('click', function() {
                    const targetValue = parseInt(this.getAttribute('data-target'));
                    speedo1.autoAdjustTo(targetValue);
                    slider.value = targetValue;
                });
            });
        });
    </script>
</body>
</html>