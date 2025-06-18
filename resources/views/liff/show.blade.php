<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>LIFF</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta property="og:title" content="LIFF | LIFFページ">
    <meta property="og:description" content="LINEアカウントでログインし、LIFFの機能をご利用いただけます。">
    <meta property="og:type" content="website" />
    <meta property="og:url" content="{{ request()->url() }}" />
    <meta property="og:image" content="{{ asset('img/liff-ogp.png') }}" />
    <meta property="og:site_name" content="LIFF" />

    <script src="https://static.line-scdn.net/liff/edge/2/sdk.js"></script>
    <style>
        body {
            font-family: Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            background-color: #f0f0f0;
        }
        .loading-spinner {
            display: inline-block;
            width: 20px;
            height: 20px;
            border: 2px solid #ccc;
            border-top-color: #333;
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin-right: 10px;
        }
        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }
        .error-message {
            color: red;
            margin-top: 10px;
        }
        #messageContainer {
            display: none;
            justify-content: center;
            align-items: center;
        }
        #closeButton {
            display: none;
            margin-top: 20px;
            padding: 10px 20px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        #closeButton:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>
<div id="liffAppContent">
    <div id="loadingContainer">
        <span class="loading-spinner"></span>
    </div>
    <div id="messageContainer" style="display: none;">
        <button id="closeButton" onclick="liff.closeWindow()">トーク画面へ戻る</button>
    </div>
    <div id="errorContainer" style="display: none;">
        <p class="error-message">LIFFページの初期化に失敗しました</p>
    </div>
</div>

<script>
    function getUrlParameter(name) {
        name = name.replace(/[\[]/, '\\[').replace(/[\]]/, '\\]');
        var regex = new RegExp('[\\?&]' + name + '=([^&#]*)');
        var results = regex.exec(location.search);
        return results === null ? '' : decodeURIComponent(results[1].replace(/\+/g, ' '));
    }

    async function sendLogRequest(idToken, acid) {
        try {
            const response = await fetch('/api/liff/v1/actions', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ idToken: idToken, acid: acid }),
            });

            if (!response.ok) {
                throw new Error('Network response was not ok');
            }

            console.log('Log request sent successfully');
        } catch (error) {
            console.error('Error sending log request:', error);
        }
    }

    async function initializeLiff(liffId) {
        try {
            await liff.init({ liffId: liffId });
            if (!liff.isLoggedIn()) {
                liff.login();
            } else {
                const idToken = liff.getIDToken();
                const acid = getUrlParameter('acid');

                if (idToken && acid) {
                    await sendLogRequest(idToken, acid);
                }

                const redirectUrl = getUrlParameter('redirect_url');
                if (redirectUrl) {
                    if (liff.isInClient()) {
                        window.location.href = redirectUrl;
                        setTimeout(() => {
                            document.getElementById('loadingContainer').style.display = 'none';
                            document.getElementById('messageContainer').style.display = 'flex';
                            document.getElementById('closeButton').style.display = 'inline-block';
                        }, 1000);
                    } else {
                        window.location.href = redirectUrl;
                    }
                }
            }
        } catch (error) {
            console.error('LIFF Initialization failed', error);
            document.getElementById('loadingContainer').style.display = 'none';
            document.getElementById('errorContainer').style.display = 'block';
        }
    }

    initializeLiff('{{ $liffId }}');
</script>
</body>
</html>