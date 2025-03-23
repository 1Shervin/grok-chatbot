<?php
// توکن بات تلگرام
$botToken = "7035848145:AAFJXb6FkA-CKABVvbuIpf863xUIY9l67EE"; // توکن باتت
$telegramApi = "https://api.telegram.org/bot$botToken/";

// توکن API xAI
$xaiApiKey = "xai-8YrzXdZmuz5wgtxp8e5vCr8c4qfrFzC2NvweGaF9aDnohkENrVt1mT1cT2OsQ3IQtn3oL4a2pabloUQJ"; // توکن xAI
$xaiUrl = "https://api.x.ai/v1/chat/completions"; // آدرس API xAI

// دریافت پیام از تلگرام
$update = file_get_contents("php://input");
$update = json_decode($update, true);

if (isset($update['message'])) {
    $chatId = $update['message']['chat']['id'];
    $message = $update['message']['text'];

    // ارسال پیام به xAI
    $data = [
        "messages" => [
            ["role" => "system", "content" => "You are Grok, a chatbot inspired by the Hitchhiker's Guide to the Galaxy."],
            ["role" => "user", "content" => $message]
        ],
        "model" => "grok-2-latest",
        "stream" => false,
        "temperature" => 0
    ];

    $ch = curl_init($xaiUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Content-Type: application/json",
        "Authorization: Bearer $xaiApiKey"
    ]);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curlError = curl_error($ch);
    curl_close($ch);

    // آماده کردن پاسخ
    if ($httpCode == 200 && !$curlError) {
        $responseData = json_decode($response, true);
        $grokResponse = $responseData['choices'][0]['message']['content'] ?? "یه مشکلی پیش اومد، بعداً دوباره امتحان کن!";
    } else {
        $grokResponse = "خطا تو وصل شدن به API: کد $httpCode - خطا: $curlError";
    }

    // ارسال پاسخ به تلگرام
    $sendData = [
        'chat_id' => $chatId,
        'text' => $grokResponse
    ];

    $ch = curl_init($telegramApi . "sendMessage");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $sendData);
    curl_exec($ch);
    curl_close($ch);
}
?>
