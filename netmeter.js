class NetworkMonitor {
    constructor() {
        this.isOnline = navigator.onLine;
        this.lastPingTime = 0;
        this.signalStrength = 0;

        // สร้าง UI elements
        this.createUI();

        // เพิ่ม event listeners
        window.addEventListener('online', () => this.handleOnline());
        window.addEventListener('offline', () => this.handleOffline());

        // เริ่มการตรวจสอบความเร็ว
        this.startSpeedTest();
    }

    createUI() {
        // สร้าง container
        this.container = document.createElement('div');
        this.container.style.cssText = `
            position: fixed;
            bottom: 20px;
            right: 20px;
            background: white;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.15);
            font-family: Arial, sans-serif;
            min-width: 280px;
        `;

        // แสดงสถานะการเชื่อมต่อ
        this.statusElement = document.createElement('div');
        this.statusElement.style.cssText = `
            font-size: 18px;
            margin-bottom: 15px;
            font-weight: bold;
        `;
        this.container.appendChild(this.statusElement);

        // สร้าง container สำหรับแถบสัญญาณ
        this.signalContainer = document.createElement('div');
        this.signalContainer.style.cssText = `
            display: flex;
            gap: 5px;
            margin-bottom: 15px;
            height: 40px;
            align-items: flex-end;
        `;

        // สร้างแถบสัญญาณ 4 แถบ
        this.signalBars = [];
        for (let i = 0; i < 4; i++) {
            const bar = document.createElement('div');
            bar.style.cssText = `
                width: 25px;
                background-color: #ddd;
                border-radius: 4px;
                transition: all 0.3s ease;
            `;
            this.signalBars.push(bar);
            this.signalContainer.appendChild(bar);
        }
        this.container.appendChild(this.signalContainer);

        // แสดงความเร็วล่าสุด
        this.speedElement = document.createElement('div');
        this.speedElement.style.cssText = `
            font-size: 16px;
            color: #666;
        `;
        this.container.appendChild(this.speedElement);

        // เพิ่มคำอธิบายสัญญาณ
        this.signalText = document.createElement('div');
        this.signalText.style.cssText = `
            font-size: 16px;
            margin-top: 10px;
            font-weight: bold;
        `;
        this.container.appendChild(this.signalText);

        document.body.appendChild(this.container);
    }

    handleOnline() {
        this.isOnline = true;
        this.showNotification('การเชื่อมต่ออินเทอร์เน็ตกลับมาแล้ว! 🎉');
        this.updateUI();
    }

    handleOffline() {
        this.isOnline = false;
        this.showNotification('ขาดการเชื่อมต่ออินเทอร์เน็ต! ⚠️');
        this.updateUI();
    }

    showNotification(message) {
        const notification = document.createElement('div');
        notification.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            background: ${this.isOnline ? '#4CAF50' : '#f44336'};
            color: white;
            padding: 15px 25px;
            border-radius: 8px;
            font-size: 16px;
            font-weight: bold;
            animation: slideIn 0.5s ease-out;
            box-shadow: 0 4px 15px rgba(0,0,0,0.2);
        `;
        notification.textContent = message;

        document.body.appendChild(notification);

        setTimeout(() => {
            notification.style.animation = 'slideOut 0.5s ease-in';
            setTimeout(() => notification.remove(), 500);
        }, 3000);
    }

    async startSpeedTest() {
        while (true) {
            if (this.isOnline) {
                const startTime = performance.now();
                try {
                    const response = await fetch('https://www.google.com/favicon.ico', {
                        mode: 'no-cors',
                        cache: 'no-cache'
                    });

                    const endTime = performance.now();
                    const pingTime = endTime - startTime;

                    this.calculateSignalStrength(pingTime);
                    this.lastPingTime = pingTime;

                } catch (error) {
                    this.signalStrength = 0;
                }
            }

            this.updateUI();
            await new Promise(resolve => setTimeout(resolve, 5000));
        }
    }

    calculateSignalStrength(pingTime) {
        if (pingTime < 100) {
            this.signalStrength = 4; // ดีมาก
        } else if (pingTime < 200) {
            this.signalStrength = 3; // ดี
        } else if (pingTime < 400) {
            this.signalStrength = 2; // พอใช้
        } else {
            this.signalStrength = 1; // แย่
        }
    }

    getSignalText(strength) {
        switch(strength) {
            case 4: return { text: 'สัญญาณดีมาก', color: '#4CAF50' };
            case 3: return { text: 'สัญญาณดี', color: '#8BC34A' };
            case 2: return { text: 'สัญญาณพอใช้', color: '#FFC107' };
            case 1: return { text: 'สัญญาณอ่อน', color: '#FF5722' };
            default: return { text: 'ไม่มีสัญญาณ', color: '#9E9E9E' };
        }
    }

    updateUI() {
        // อัพเดทสถานะการเชื่อมต่อ
        this.statusElement.textContent = `สถานะ: ${this.isOnline ? '🟢 ออนไลน์' : '🔴 ออฟไลน์'}`;

        // อัพเดทแถบสัญญาณ
        const barHeights = ['40%', '60%', '80%', '100%'];
        this.signalBars.forEach((bar, index) => {
            bar.style.height = barHeights[index];
            bar.style.backgroundColor = index < this.signalStrength ?
                this.getSignalText(this.signalStrength).color : '#ddd';
        });

        // อัพเดทข้อความแสดงความแรงสัญญาณ
        const signalInfo = this.getSignalText(this.signalStrength);
        this.signalText.textContent = signalInfo.text;
        this.signalText.style.color = signalInfo.color;

        // แสดงความเร็วล่าสุด
        this.speedElement.textContent = `ความเร็ว: ${Math.round(this.lastPingTime)} มิลลิวินาที`;
    }
}

// สร้าง style สำหรับ animations
const style = document.createElement('style');
style.textContent = `
    @keyframes slideIn {
        from { transform: translateX(100%); opacity: 0; }
        to { transform: translateX(0); opacity: 1; }
    }
    @keyframes slideOut {
        from { transform: translateX(0); opacity: 1; }
        to { transform: translateX(100%); opacity: 0; }
    }
`;
document.head.appendChild(style);

// เริ่มการทำงาน
//const networkMonitor = new NetworkMonitor();