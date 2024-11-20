@vite(['resources/css/app.css', 'resources/js/app.js'])
<x-app-layout>
<head>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>

<div style="display: flex; align-items: center; justify-content: center; flex-direction: column;" class="pt-14">
    <div class="max-w-2xl mx-auto">
        <h1 class="text-3xl font-bold mb-4 text-center">ご家族に<br>新規登録のご案内を送る</h1>
        <div class="share-buttons flex justify-center space-x-4 mt-4">
            <a href="mailto:?subject=新規登録のご案内&body=ご家族に新規登録のご案内を送ります。" 
               id="share-email" 
               title="Email" 
               class="text-white bg-blue-500 p-2 rounded flex items-center justify-center">
                <i class="fas fa-envelope text-3xl"></i>
            </a>
            <button type="button" 
                    id="share-line"
                    class="text-white bg-green-500 p-2 rounded flex items-center justify-center">
                <i class="fab fa-line text-3xl"></i>
            </button>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const shareUrl = "{!! $url !!}";
        const defaultMessage = "新連絡帳システムのご案内です。以下のリンクから新規登録してください。有効期限は本メッセージ送信後24時間以内となります。有効期限切れの場合は施設管理者に再送をご依頼ください:";

        // Emailシェア
        document.getElementById('share-email').addEventListener('click', function (e) {
            e.preventDefault();
            const subject = encodeURIComponent('新規登録のご案内');
            const body = encodeURIComponent(`${defaultMessage} ${shareUrl}`);
            window.location.href = `mailto:?subject=${subject}&body=${body}`;
        });

        // LINEシェア
        document.getElementById('share-line').addEventListener('click', function (e) {
            e.preventDefault();
            const isMobile = /iPhone|iPad|iPod|Android/i.test(navigator.userAgent);
            if (isMobile) {
                window.location.href = `line://msg/text/${encodeURIComponent(defaultMessage + "\n" + shareUrl)}`;
            } else {
                const lineShareUrl = `https://social-plugins.line.me/lineit/share?url=${encodeURIComponent(shareUrl)}&text=${encodeURIComponent(defaultMessage)}`;
                window.open(lineShareUrl, '_blank');
            }
        });
    });
</script>
</x-app-layout>