import pandas as pd
import numpy as np
from datetime import datetime

def analyze_candlesticks(candle_data):
    # Convert list of dictionaries to DataFrame
    df = pd.DataFrame(candle_data)
    
    def calculate_ema(series, period):
        """Calculate EMA for a given series and period"""
        return series.ewm(span=period, adjust=False).mean()
    
    def calculate_rsi(data, period=14):
        """Calculate RSI for a given series and period"""
        # Calculate price changes
        delta = data['close'].diff()
        
        # Separate gains and losses
        gains = delta.where(delta > 0, 0)
        losses = -delta.where(delta < 0, 0)
        
        # Calculate average gains and losses
        avg_gains = gains.rolling(window=period).mean()
        avg_losses = losses.rolling(window=period).mean()
        
        # Calculate RS and RSI
        rs = avg_gains / avg_losses
        rsi = 100 - (100 / (1 + rs))
        
        return rsi
    
    def get_candle_color(row):
        """Determine candle color based on open and close prices"""
        if row['close'] > row['open']:
            return 'Green'
        elif row['close'] < row['open']:
            return 'Red'
        return 'Equal'
    
    def detect_turn_points(series):
        """Detect turn points in a series"""
        turns = pd.Series(index=series.index, dtype=str)
        
        for i in range(1, len(series)-1):
            if series.iloc[i-1] > series.iloc[i] and series.iloc[i+1] > series.iloc[i]:
                turns.iloc[i] = 'TurnDown'
            elif series.iloc[i-1] < series.iloc[i] and series.iloc[i+1] < series.iloc[i]:
                turns.iloc[i] = 'TurnUp'
            else:
                turns.iloc[i] = ''
                
        turns.iloc[0] = ''
        turns.iloc[-1] = ''
        return turns
    
    def get_slope_direction(slope):
        """Determine slope direction"""
        if abs(slope) < 0.0001:
            return 'Pararell'
        return 'Up' if slope > 0 else 'Down'
    
    def count_colors_between_turns(df, turn_points, current_idx):
        """Count candle colors between turn points"""
        counts = {'Green': 0, 'Red': 0, 'Equal': 0}
        
        # Find last turn point
        last_turn_idx = None
        for i in range(current_idx-1, -1, -1):
            if turn_points.iloc[i]:
                last_turn_idx = i
                break
                
        if last_turn_idx is None:
            return counts
            
        # Count colors
        for i in range(last_turn_idx, current_idx + 1):
            color = df.iloc[i]['candle_color']
            counts[color] += 1
            
        return counts

    # Calculate basic indicators
    df['ema3'] = calculate_ema(df['close'], 3)
    df['ema5'] = calculate_ema(df['close'], 5)
    df['ema7'] = calculate_ema(df['close'], 7)
    df['rsi'] = calculate_rsi(df)
    
    # Calculate candle colors
    df['candle_color'] = df.apply(get_candle_color, axis=1)
    
    # Detect turn points
    df['ema3_turns'] = detect_turn_points(df['ema3'])
    df['ema5_turns'] = detect_turn_points(df['ema5'])
    df['ema7_turns'] = detect_turn_points(df['ema7'])
    
    # Calculate slopes
    df['ema3_slope'] = df['ema3'].diff()
    df['ema5_slope'] = df['ema5'].diff()
    df['ema7_slope'] = df['ema7'].diff()
    
    # Initialize result list
    result = []
    
    for i in range(len(df)):
        row = df.iloc[i]
        
        # Convert epoch to time
        minute_no = datetime.fromtimestamp(row['epoch']).strftime('%H:%M')
        
        # Get previous candle colors
        prev_colors = [
            df.iloc[i-j]['candle_color'] if i-j >= 0 else ""
            for j in range(1, 4)
        ]
        
        # Calculate EMA crossovers
        ema3_ema5_cross = (
            "Golden Cross" if (i > 0 and
                             row['ema3'] > row['ema5'] and
                             df.iloc[i-1]['ema3'] <= df.iloc[i-1]['ema5'])
            else "Death Cross" if (i > 0 and
                                 row['ema3'] < row['ema5'] and
                                 df.iloc[i-1]['ema3'] >= df.iloc[i-1]['ema5'])
            else ""
        )
        
        ema5_ema7_cross = (
            "Golden Cross" if (i > 0 and
                             row['ema5'] > row['ema7'] and
                             df.iloc[i-1]['ema5'] <= df.iloc[i-1]['ema7'])
            else "Death Cross" if (i > 0 and
                                 row['ema5'] < row['ema7'] and
                                 df.iloc[i-1]['ema5'] >= df.iloc[i-1]['ema7'])
            else ""
        )
        
        # Count colors between turn points
        color_counts = count_colors_between_turns(df, df['ema3_turns'], i)
        
        # Calculate distances from last turn points
        ema3_turn_distance = 0
        ema5_turn_distance = 0
        
        for j in range(i-1, -1, -1):
            if df.iloc[j]['ema3_turns']:
                ema3_turn_distance = i - j
                break
                
        for j in range(i-1, -1, -1):
            if df.iloc[j]['ema5_turns']:
                ema5_turn_distance = i - j
                break
        
        result.append({
            'CandleID': str(row['CandleID']),
            'MinuteNo': minute_no,
            'ema3': f"{row['ema3']:.2f}",
            'ema5': f"{row['ema5']:.2f}",
            'ema7': f"{row['ema7']:.2f}",
            'rsi': f"{row['rsi']:.2f}" if not pd.isna(row['rsi']) else "",
            'สีของแท่งเทียน': row['candle_color'],
            'สีของแท่งเทียน ย้อนหลังไป 1 แท่ง': prev_colors[0],
            'สีของแท่งเทียน ย้อนหลังไป 2 แท่ง': prev_colors[1],
            'สีของแท่งเทียน ย้อนหลังไป 3 แท่ง': prev_colors[2],
            'ema3-ema5': f"{(row['ema3'] - row['ema5']):.2f}",
            'ema5-ema7': f"{(row['ema5'] - row['ema7']):.2f}",
            'ประเภทจุดกลับตัวของ ema3': row['ema3_turns'],
            'ประเภทจุดกลับตัวของ ema5': row['ema5_turns'],
            'ประเภทจุดกลับตัวของ ema7': df['ema7_turns'].iloc[i],
            'ประเภทจุดกลับตัวของ ema3 ย้อนหลังไป 1 แท่ง': df['ema3_turns'].iloc[i-1] if i > 0 else "",
            'ประเภทจุดกลับตัวของ ema5 ย้อนหลังไป 1 แท่ง': df['ema5_turns'].iloc[i-1] if i > 0 else "",
            'Slope Value ของ ema3': f"{row['ema3_slope']:.2f}",
            'Slope Value ของ ema5': f"{row['ema5_slope']:.2f}",
            'Slope Value ของ ema7': f"{df['ema7_slope'].iloc[i]:.2f}",
            'Slope Direction ของ ema3': get_slope_direction(row['ema3_slope']),
            'Slope Direction ของ ema5': get_slope_direction(row['ema5_slope']),
            'Slope Direction ของ ema7': get_slope_direction(df['ema7_slope'].iloc[i]),
            'เป็นจุดตัดกันของ ema3,ema5แบบไหน': ema3_ema5_cross,
            'เป็นจุดตัดกันของ ema5,ema7แบบไหน': ema5_ema7_cross,
            'ระยะห่างจากจุดกลับตัวของ ema3 จุดสุดท้าย': ema3_turn_distance,
            'ระยะห่างจากจุดกลับตัวของ ema5 จุดสุดท้าย': ema5_turn_distance,
            'จำนวนแท่งเทียนสีเขียว': color_counts['Green'],
            'จำนวนแท่งเทียนสีแดง': color_counts['Red'],
            'จำนวนแท่งเทียน Equal': color_counts['Equal']
        })
    
    return result

# Example usage:
candle_data = [
    {
        "CandleID": 1,
        "close": 95210.65,
        "epoch": 1736376240,
        "high": 95228.25,
        "low": 95205.65,
        "open": 95205.95
    }
    # ... more candle data ...
]

analysis = analyze_candlesticks(candle_data)
print(analysis)