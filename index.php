<?php
// فعال کردن نمایش خطاها برای دیباگ (فقط برای تست، تو محیط واقعی خاموشش کن)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['message'])) {
    $userMessage = trim($_POST['message']);
    
    // وصل شدن به API xAI
    $apiKey = "xai-8YrzXdZmuz5wgtxp8e5vCr8c4qfrFzC2NvweGaF9aDnohkENrVt1mT1cT2OsQ3IQtn3oL4a2pabloUQJ"; // کلید API تو
    $url = "https://api.x.ai/v1/chat/completions"; // آدرس API از مستندات
    
    $data = [
        "messages" => [
            [
                "role" => "system",
                "content" => "You are Grok, a chatbot inspired by the Hitchhiker's Guide to the Galaxy."
            ],
            [
                "role" => "user",
                "content" => $userMessage
            ]
        ],
        "model" => "grok-2-latest",
        "stream" => false,
        "temperature" => 0
    ];
    
    // استفاده از cURL
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Content-Type: application/json",
        "Authorization: Bearer $apiKey"
    ]);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curlError = curl_error($ch);
    curl_close($ch);
    
    if ($httpCode == 200 && !$curlError) {
        $responseData = json_decode($response, true);
        $grokResponse = $responseData['choices'][0]['message']['content'] ?? "یه مشکلی پیش اومد، بعداً دوباره امتحان کن!";
        echo $grokResponse;
    } else {
        echo "خطا تو وصل شدن به API: کد $httpCode - خطا: $curlError";
    }
    exit;
}
?>
<html>
<head>
    <title>گروک ☬ SHΞN™</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        @font-face {
            font-family: 'PixelFont';
            src: url('https://fonts.googleapis.com/css2?family=Press+Start+2P&display=swap');
        }
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            background: #1a1a1a;
            color: #00ff00;
            font-family: 'PixelFont', 'Courier New', monospace;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 100vh;
            overflow: hidden;
            background-image: linear-gradient(45deg, #1a1a1a 25%, #222 25%, #222 50%, #1a1a1a 50%, #1a1a1a 75%, #222 75%, #222);
            background-size: 20px 20px;
        }
        #chatbox {
            width: 90%;
            max-width: 600px;
            height: 70vh;
            background: #111;
            border: 4px solid #00ff00;
            border-radius: 8px;
            padding: 10px;
            overflow-y: auto;
            margin-bottom: 10px;
            box-shadow: 0 0 10px #00ff00;
            direction: rtl;
            text-align: right;
        }
        #chatbox p {
            margin: 5px 0;
            font-size: 12px;
            line-height: 1.5;
        }
        #input {
            width: 90%;
            max-width: 600px;
            padding: 10px;
            background: #000;
            color: #00ff00;
            border: 2px solid #00ff00;
            border-radius: 8px;
            font-family: 'PixelFont', 'Courier New', monospace;
            font-size: 12px;
            direction: rtl;
            text-align: right;
        }
        #send {
            padding: 10px 20px;
            background: #00ff00;
            color: #000;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            margin-top: 10px;
            font-family: 'PixelFont', 'Courier New', monospace;
            font-size: 12px;
        }
        #send:hover {
            background: #00cc00;
        }
        @media (max-width: 600px) {
            #chatbox, #input {
                width: 95%;
            }
            #chatbox p, #input, #send {
                font-size: 10px;
            }
        }
    </style>
</head>
<body>
    <div id="chatbox"></div>
    <input id="input" type="text" placeholder="زودتر بگو، کار زیاد دارم...">
    <button id="send" onclick="sendMessage()">بفرست براش</button>
    <script>
        function sendMessage() {
            let input = document.getElementById('input');
            let message = input.value.trim();
            if (message === "") return;
            let chatbox = document.getElementById('chatbox');
            chatbox.innerHTML += "<p><b>تو:</b> " + message + "</p>";
            let xhr = new XMLHttpRequest();
            xhr.open("POST", "", true);
            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
            xhr.onreadystatechange = function() {
                if (xhr.readyState == 4 && xhr.status == 200) {
                    chatbox.innerHTML += "<p><b>گروک ☬ SHΞN™:</b> " + xhr.responseText + "</p>";
                    chatbox.scrollTop = chatbox.scrollHeight;
                }
            };
            xhr.send("message=" + encodeURIComponent(message));
            input.value = "";
        }
    </script>
</body>
</html>
