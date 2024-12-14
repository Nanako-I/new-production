<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>エラー</title>
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            background-color: #f8f9fa;
            padding: 20px;
            box-sizing: border-box;
        }
        .error-container {
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            padding: 30px;
            max-width: 90%;
            width: 400px;
        }
        .error-message {
            text-align: center;
            font-size: 18px;
            color: #333;
            line-height: 1.5;
        }
        @media (max-width: 768px) {
            .error-message {
                font-size: 22px;
            }
        }
        @media (max-width: 480px) {
            .error-message {
                font-size: 24px;
            }
        }
    </style>
</head>
<body>
    <div class="error-container">
        <div class="error-message">
            {{ $message }}
        </div>
    </div>
</body>
</html>

