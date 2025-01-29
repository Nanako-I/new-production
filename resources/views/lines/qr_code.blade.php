<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QRコード</title>
</head>
<body>
    <h1>友だち追加用QRコード</h1>
    <p>以下のQRコードをスキャンして、LINEボットを友だち追加してください。</p>
    <img src="https://api.qrserver.com/v1/create-qr-code/?data={{ urlencode($qrCodeUrl) }}&size=200x200" alt="QR Code">
    
    <p>LINEボットの友だち追加を終えたら、<a href="{{ route('line.success') }}">こちら</a>をクリックしてください。</p>
</body>
</html>