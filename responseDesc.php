<?php
//********************** response.msg_type = 'time'
{
  "msg_type": "time",
  "time": 1746777381
} 
//*********************** response.msg_type = 'candle'
{
  "candles": [
    {
      "close": 1567.23,
      "epoch": 1746777480,
      "high": 1568.28,
      "low": 1566.54,
      "open": 1566.81
    },
     
  ],
  "echo_req": {
    "count": 5,
    "end": "latest",
    "granularity": 60,
    "style": "candles",
    "ticks_history": "R_100"
  },
  "msg_type": "candles",
  "pip_size": 2
}

//************************* OHLC 
 
{
  "echo_req": {
    "adjust_start_time": 1,
    "count": 60,
    "end": "latest",
    "granularity": 60,
    "start": 1,
    "style": "candles",
    "subscribe": 1,
    "ticks_history": "R_100"
  },
  "msg_type": "ohlc",
  "ohlc": {
    "close": "1558.59",
    "epoch": 1746777986,
    "granularity": 60,
    "high": "1562.84",
    "id": "f55858b0-82ac-aac8-7bd2-af8ca5295d97",
    "low": "1558.59",
    "open": "1562.84",
    "open_time": 1746777960,
    "pip_size": 2,
    "symbol": "R_100"
  },
  "subscription": {
    "id": "f55858b0-82ac-aac8-7bd2-af8ca5295d97"
  }
}

{
  "echo_req": {
    "adjust_start_time": 1,
    "count": 60,
    "end": "latest",
    "granularity": 60,
    "start": 1,
    "style": "candles",
    "subscribe": 1,
    "ticks_history": "R_100"
  },
  "msg_type": "ohlc",
  "ohlc": {
    "close": "1559.10",
    "epoch": 1746777988,
    "granularity": 60,
    "high": "1562.84",
    "id": "f55858b0-82ac-aac8-7bd2-af8ca5295d97",
    "low": "1558.59",
    "open": "1562.84",
    "open_time": 1746777960,
    "pip_size": 2,
    "symbol": "R_100"
  },
  "subscription": {
    "id": "f55858b0-82ac-aac8-7bd2-af8ca5295d97"
  }
}

{
  "echo_req": {
    "adjust_start_time": 1,
    "count": 60,
    "end": "latest",
    "granularity": 60,
    "start": 1,
    "style": "candles",
    "subscribe": 1,
    "ticks_history": "R_100"
  },
  "msg_type": "ohlc",
  "ohlc": {
    "close": "1559.34",
    "epoch": 1746777990,
    "granularity": 60,
    "high": "1562.84",
    "id": "f55858b0-82ac-aac8-7bd2-af8ca5295d97",
    "low": "1558.59",
    "open": "1562.84",
    "open_time": 1746777960,
    "pip_size": 2,
    "symbol": "R_100"
  },
  "subscription": {
    "id": "f55858b0-82ac-aac8-7bd2-af8ca5295d97"
  }
}

{
  "echo_req": {
    "adjust_start_time": 1,
    "count": 60,
    "end": "latest",
    "granularity": 60,
    "start": 1,
    "style": "candles",
    "subscribe": 1,
    "ticks_history": "R_100"
  },
  "msg_type": "ohlc",
  "ohlc": {
    "close": "1559.01",
    "epoch": 1746777992,
    "granularity": 60,
    "high": "1562.84",
    "id": "f55858b0-82ac-aac8-7bd2-af8ca5295d97",
    "low": "1558.59",
    "open": "1562.84",
    "open_time": 1746777960,
    "pip_size": 2,
    "symbol": "R_100"
  },
  "subscription": {
    "id": "f55858b0-82ac-aac8-7bd2-af8ca5295d97"
  }
}

// Buy 
{
    "balance_after": 9285.5,
    "buy_price": 1,
    "contract_id": 281353727388,
    "longcode": "Win payout if Volatility 100 Index after 1 tick is strictly higher than entry spot.",
    "payout": 1.95,
    "purchase_time": 1747020394,
    "shortcode": "CALL_R_100_1.95_1747020394_1T_S0P_0",
    "start_time": 1747020394,
    "transaction_id": 560806430668
}
// proposal_open_contract ต้องดึง response.proposal_open_contract จะได้ 
{
    "account_id": 191869168,
    "audit_details": {
        "all_ticks": [
            {
                "epoch": 1747020782,
                "tick": 1511.83,
                "tick_display_value": "1511.83"
            },
            {
                "epoch": 1747020784,
                "flag": "highlight_time",
                "name": "Start Time",
                "tick": 1511.51,
                "tick_display_value": "1511.51"
            },
            {
                "epoch": 1747020786,
                "flag": "highlight_tick",
                "name": "Entry Spot",
                "tick": 1511.36,
                "tick_display_value": "1511.36"
            },
            {
                "epoch": 1747020788,
                "flag": "highlight_tick",
                "name": "End Time and Exit Spot",
                "tick": 1511.81,
                "tick_display_value": "1511.81"
            }
        ]
    },
    "barrier": "1511.36",
    "barrier_count": 1,
    "bid_price": 1.95,
    "buy_price": 1,
    "contract_id": 281354024068,
    "contract_type": "CALL",
    "currency": "USD",
    "current_spot": 1511.81,
    "current_spot_display_value": "1511.81",
    "current_spot_time": 1747020788,
    "date_expiry": 1747020788,
    "date_settlement": 1747020788,
    "date_start": 1747020784,
    "display_name": "Volatility 100 Index",
    "entry_spot": 1511.36,
    "entry_spot_display_value": "1511.36",
    "entry_tick": 1511.36,
    "entry_tick_display_value": "1511.36",
    "entry_tick_time": 1747020786,
    "exit_tick": 1511.81,
    "exit_tick_display_value": "1511.81",
    "exit_tick_time": 1747020788,
    "expiry_time": 1747020788,
    "id": "c87c1e0f-cba5-2aa1-a777-c04644075f7f",
    "is_expired": 1,
    "is_forward_starting": 0,
    "is_intraday": 1,
    "is_path_dependent": 0,
    "is_settleable": 1,
    "is_sold": 0,
    "is_valid_to_cancel": 0,
    "is_valid_to_sell": 1,
    "longcode": "Win payout if Volatility 100 Index after 1 tick is strictly higher than entry spot.",
    "payout": 1.95,
    "profit": 0.95,
    "profit_percentage": 95,
    "purchase_time": 1747020784,
    "shortcode": "CALL_R_100_1.95_1747020784_1T_S0P_0",
    "status": "open",
    "tick_count": 1,
    "tick_stream": [
        {
            "epoch": 1747020786,
            "tick": 1511.36,
            "tick_display_value": "1511.36"
        },
        {
            "epoch": 1747020788,
            "tick": 1511.81,
            "tick_display_value": "1511.81"
        }
    ],
    "transaction_ids": {
        "buy": 560807024548
    },
    "underlying": "R_100"
}
