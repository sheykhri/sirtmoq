<?php
date_default_timezone_set('Asia/Tashkent');
define('TOKEN','343853597:AAFKI-6OmhC0zrL2C-QSVHMTNPY6EWlLS8Y');
define('ADMIN_ID','211920167');

$update = json_decode(file_get_contents('php://input'));
if(isset($update)){
$message = $update->message;
if(isset($message)){
$nomer = $message->contact->phone_number;
$nid = $message->contact->user_id;
$cid = $message->chat->id;
$c_type = $message->chat->type;
$mid = $message->message_id;
$name = $message->chat->first_name;
$user = $message->from->username;
$fid = $message->from->id;
$tx = $message->text;
$entities = $message->entities;
$etx = explode(" ",$tx);
}
$callback = $update->callback_query;
if(isset($callback)){
$fid = $callback->from->id;
$name = $callback->from->first_name;
$ida = $callback->id;
$mid = $callback->message->message_id;
$tx = $callback->message->text;
$cid = $callback->message->chat->id;
$user = $callback->message->chat->username;
$imid = $callback->inline_message_id;
$data = $callback->data;
$data = explode("()",$data);
}
}

include "words.php";
include "graphics.php";
include "function.php";
include "index.php";
?>