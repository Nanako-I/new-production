<!DOCTYPE html>
<html>
<head>
    <title>エラー</title>
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            font-family: sans-serif;
            background-color: white;
        }
        .error-message {
            text-align: center;
            padding: 20px;
            font-size: 18px;
        }
    </style>
</head>
<body>
    <div class="error-message">
        {{ $message }}
    </div>
</body>
</html>