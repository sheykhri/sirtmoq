<?php
function bot($method,$datas=[]){
$url = "https://api.telegram.org/bot".TOKEN."/".$method;
$ch = curl_init();
curl_setopt($ch,CURLOPT_URL,$url);
curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
curl_setopt($ch,CURLOPT_POSTFIELDS,$datas);
$res = curl_exec($ch);
if(curl_error($ch)){
var_dump(curl_error($ch));
}else{
return json_decode($res);
}
}

function get_rand(){
global $words, $cid;
$gap = $words[rand(0,count($words)-1)];
if((mb_stripos($gap,"’")!==false) or (mb_stripos($gap,"‘")!==false)){
get_rand();
}else{
bot('sendMessage',[
'chat_id'=>ADMIN_ID,
'text'=>$gap,
]);
EX('DELETE FROM `games` WHERE `chat_id` = "'.$cid.'"');
EX('INSERT INTO `games` ("gap","chat_id","now","xato","togri","id_list") VALUES ("'.$gap.'","'.$cid.'","0","","","")');
}
}

function ACL($text=false){
global $ida;
if($text)
bot('answerCallbackQuery',[
'callback_query_id'=>$ida,
'text'=>$text,
'show_alert'=>'false',
]);
else bot('answerCallbackQuery',['callback_query_id'=>$ida]);
}

function getMember(){
global $cid,$fid;
$get = bot('getChatMember',[
'chat_id'=>$cid,
'user_id'=>$fid,
]);
$result = $get->result->status;
return $result;
}

function deleteM($last){
global $cid;
bot('deleteMessage',[
'chat_id'=>$cid,
'message_id'=>$last,
]);
}

function ENDQ(){
global $cid,$mid;
$db = new SQLite3("baza.sqlite");
$result = $db->querySingle('SELECT `message_id` FROM `end_game` WHERE `chat_id` = "'.$cid.'" AND `message_id` = "'.$mid.'"');
$db->close();
return $result;
}

function QS($type,$from){
global $cid;
$db = new SQLite3("baza.sqlite");
$result = $db->querySingle('SELECT `'.$type.'` FROM `'.$from.'` WHERE `chat_id` = "'.$cid.'"');
$db->close();
return $result;
}

function QQ($from){
global $cid;
$db = new SQLite3("baza.sqlite");
$result = $db->query('SELECT * FROM `'.$from.'` WHERE `chat_id` = "'.$cid.'"')->fetchArray();
$db->close();
return $result;
}

function UP($type,$input){
global $cid;
$db = new SQLite3("baza.sqlite");
$db->exec('UPDATE `'.$type.'` SET '.$input.' WHERE `chat_id` = "'.$cid.'"');
$db->close();
}

function IN($type,$values){
EX('INSERT INTO `'.$type.'` '.$values);
}

function EX($query){
$db = new SQLite3("baza.sqlite");
$db->exec($query);
$db->close();
}
?>