<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Glowing Border CSS</title>
    <style>
        body {
            background-color: #1a1a1a;
            font-family: Arial, sans-serif;
            padding: 40px;
            display: flex;
            flex-direction: column;
            gap: 30px;
            align-items: center;
        }

        /* Basic Glow */
        .glow-basic {
            border: 2px solid #00ff40;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px #00ff40;
            background-color: rgba(0, 255, 64, 0.05);
            color: white;
            text-align: center;
            width: 300px;
        }

        /* Medium Glow */
        .glow-medium {
            border: 2px solid #00ff40;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 
                0 0 10px #00ff40,
                0 0 20px #00ff40,
                0 0 30px #00ff40;
            background-color: rgba(0, 255, 64, 0.05);
            color: white;
            text-align: center;
            width: 300px;
        }

        /* Strong Glow */
        .glow-strong {
            border: 3px solid #00ff40;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 
                0 0 5px #00ff40,
                0 0 10px #00ff40,
                0 0 20px #00ff40,
                0 0 40px #00ff40;
            background-color: rgba(0, 255, 64, 0.1);
            color: white;
            text-align: center;
            width: 300px;
        }

        /* Animated Glow */
        .glow-animated {
            border: 2px solid #00ff40;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 
                0 0 5px #00ff40,
                0 0 10px #00ff40,
                0 0 20px #00ff40;
            background-color: rgba(0, 255, 64, 0.05);
            color: white;
            text-align: center;
            width: 300px;
            animation: glowPulse 2s ease-in-out infinite alternate;
        }

        @keyframes glowPulse {
            from {
                box-shadow: 
                    0 0 5px #00ff40,
                    0 0 10px #00ff40,
                    0 0 20px #00ff40;
            }
            to {
                box-shadow: 
                    0 0 10px #00ff40,
                    0 0 20px #00ff40,
                    0 0 40px #00ff40,
                    0 0 60px #00ff40;
            }
        }

        /* Hover Effect */
        .glow-hover {
            border: 2px solid #00ff40;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 5px #00ff40;
            background-color: rgba(0, 255, 64, 0.05);
            color: white;
            text-align: center;
            width: 300px;
            transition: box-shadow 0.3s ease;
            cursor: pointer;
        }

        .glow-hover:hover {
            box-shadow: 
                0 0 10px #00ff40,
                0 0 20px #00ff40,
                0 0 40px #00ff40,
                0 0 60px #00ff40;
        }

        h1 {
            color: #00ff40;
            text-align: center;
            margin-bottom: 30px;
        }

        .label {
            color: #ccc;
            margin-bottom: 10px;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <h1>CSS Border เรืองแสงสีเขียว</h1>
    
    <div class="label">Basic Glow:</div>
    <div class="glow-basic">
        เงาเรืองแสงแบบธรรมดา
    </div>

    <div class="label">Medium Glow:</div>
    <div class="glow-medium">
        เงาเรืองแสงแบบกลาง
    </div>

    <div class="label">Strong Glow:</div>
    <div class="glow-strong">
        เงาเรืองแสงแบบแรง
    </div>

    <div class="label">Animated Glow:</div>
    <div class="glow-animated">
        เงาเรืองแสงแบบเคลื่อนไหว
    </div>

    <div class="label">Hover Effect:</div>
    <div class="glow-hover">
        เลื่อนเมาส์มาดู!
    </div>
</body>
</html>