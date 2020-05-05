<?php
include __DIR__.'config.php';

$json_string = file_get_contents('php://input');
$json_object = json_decode($json_string);
$replyToken = $json_object->{"events"}[0]->{"replyToken"};
$message_type = $json_object->{"events"}[0]->{"message"}->{"type"};    //メッセージタイプ

if ($message_type == "text"){
   $response_format_text = [generateFlex($message_text)];
   $message_text = $json_object->{"events"}[0]->{"message"}->{"text"};    //メッセージ内容
}else{
    $response_format_text = [[
        "type" => "test",
        "text" => "🤔"
    ]];
}

echo sending_messages(LINE_ACCESS_TOKEN, $replyToken, $response_format_text);

function generateFlex($url){
    return [
        "type"=> "flex",
        "altText"=> "Flex tester",
        "contents"=>[
        "type"=> "bubble",
        "body"=> [
          "type"=> "box",
          "layout"=> "vertical",
          "contents"=> [
            [
              "type"=> "text",
              "text"=> "hello, world",
              "wrap"=> true,
              "align"=> "center"
            ]
          ]
        ],
        "footer"=> [
          "type"=> "box",
          "layout"=> "vertical",
          "contents"=> [
            [
              "type"=> "button",
              "action"=> [
                "type"=> "uri",
                "label"=> "GO!",
                "uri"=> trim($url)
              ]
            ]
          ]
        ]
        ]
    ];
}

function pushing_messages($accessToken, $to, $response_format_text){
    //レスポンスフォーマット

    //ポストデータ
    $post_data = [
        "to" => $to,
        "messages" => $response_format_text
    ];

    //curl実行
    $ch = curl_init("https://api.line.me/v2/bot/message/push");
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($post_data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Content-Type: application/json; charser=UTF-8',
        'Authorization: Bearer ' . $accessToken
    ));
    $result = curl_exec($ch);
    curl_close($ch);
    return $result;
}

function sending_messages($accessToken, $replyToken, $response_format_text){
    //レスポンスフォーマット

    //ポストデータ
    $post_data = [
        "replyToken" => $replyToken,
        "messages" => $response_format_text
    ];

    //curl実行
    $ch = curl_init("https://api.line.me/v2/bot/message/reply");
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($post_data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Content-Type: application/json; charser=UTF-8',
        'Authorization: Bearer ' . $accessToken
    ));
    $result = curl_exec($ch);
    curl_close($ch);
    return $result;
}