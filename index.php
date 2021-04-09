<?php
function closeConnection($message = 'OK!'){
if(php_sapi_name() === 'cli' || isset($GLOBALS['exited'])){
return;
}
ob_end_clean();
header('Connection: close');
ignore_user_abort(true);
ob_start();
ob_end_flush();
flush();
$GLOBALS['exited'] = true;
}

if(!file_exists('bot.lock')) {
touch('bot.lock');
}
$lock = fopen('bot.lock', 'r+');
$try = 1;
$locked = false;
while(!$locked){
$locked = flock($lock, LOCK_EX | LOCK_NB);
if(!$locked){
closeConnection();
if($try++ >= 2){
ACL("1 soniyada 1 harfga ruhsat berilgan.");
exit;
}
}
}

$alifbo = str_split("abcdefghijklmnopqrstuvxyz");

// PRIVATE START //
if($c_type == 'private'){

if($tx == "/start"){
$db = new SQLite3("baza.sqlite");
$db->query('CREATE TABLE end_game (chat_id TEXT, message_id TEXT)');
$db->close();
/*$db = new SQLite3("baza.sqlite");
$db->query('DROP TABLE games');
$db->query('CREATE TABLE last (message_id TEXT, chat_id TEXT)');
$db->query('CREATE TABLE games (gap TEXT, chat_id TEXT, now TEXT, xato TEXT, togri TEXT, natija TEXT, id_list TEXT)');
$db->query('CREATE TABLE hisobot (chat_id TEXT, ok TEXT, nok TEXT, jami TEXT)');
$db->query('CREATE TABLE sozlama (chat_id TEXT, all_time TEXT, only_admins TEXT)');
$db->close();*/
bot('sendMessage',[
'chat_id'=>$cid,
'text'=>"<b>🙋‍♂Assalomu alaykum.
🥺Bu o'yinda siz bechora bir bolani jonini saqlab qolishingiz kerak. Xar bir xato harf uchun bolaga sirtmoq tayyorlanadi. Xato harflar ummumiy jamlanmasi 10 donaga yetganida bola dorga osib qatil e'tiladi. Demak uni jamoangiz bilan qutqarib qoling.
👥Guruhda so'z topish o'yinini o'ynash uchun botni guruhga qo'shing.
👤O'zingiz o'ynash uchun</b> /boshlash",
'parse_mode'=>'HTML',
'reply_markup'=>json_encode(
['inline_keyboard'=>[
[['url'=>"https://telegram.me/GameUzBot?startgroup=new",'text'=>"🔹Guruhga qo'shish"],],
[['url'=>"https://telegram.me/TheGameUz",'text'=>"🔸Beta test guruhi"],],
]
]),
]);
exit;
}

if($tx == "/boshlash"){
get_rand();
foreach($alifbo as $harf){
$page[] = ["callback_data"=>"l()".$harf,"text"=>$harf];
}
$keyboard = array_merge(array_chunk($page,5));

$gap = QS('gap','games');
$xato = QS('xato','games');
$now = QS('now','games');
$uzun = strlen($gap);
$gap = str_repeat("*",$uzun);
$status = $graph["$now"];

bot('sendMessage',[
'chat_id'=>$cid,
'text'=>"🙋‍♂So'z topish o'yiniga xush kelibsiz!

🗣<b>So'z:</b> $gap
📜<b>Uzunligi:</b> $uzun
❎<b>Xato variantlar:</b> $xato
❌<b>Xatolar soni:</b> $now/10
👻<b>Holat:</b>

$status",
'parse_mode'=>'HTML',
'reply_markup'=>json_encode(['inline_keyboard'=>$keyboard]),
]);
$db->close();
}

} // PRIVATE END;


// GROUP START //
if($c_type == 'supergroup' OR $c_type == 'group'){

// GAME START //
if($tx == "/start@GameUzbot"){
$check = QS('chat_id','hisobot');
if(!$check){
IN('hisobot','VALUES ("'.$cid.'","0","0","0")');
IN('sozlama','VALUES ("'.$cid.'","n","y")');
}

$only_admins = QS('only_admins','sozlama');
$status = getMember();
if($status == "member" and $only_admins == "y"){
deleteM($mid);
exit;
}

$last = QS('message_id','last');
if(($last) AND ($status == "administrator" or $status == "creator")) deleteM($last); deleteM($last-1);

get_rand();
foreach($alifbo as $harf){
$page[] = ["callback_data"=>"h()".$harf,"text"=>$harf];
}
$keyboard = array_merge(array_chunk($page,5));

$gap = QS('gap','games');
$xato = QS('xato','games');
$now = QS('now','games');
$uzun = strlen($gap);
$gap = str_repeat("*",$uzun);
$status = $graph["$now"];

$mid = bot('sendMessage',[
'chat_id'=>$cid,
'text'=>"🙋‍♂So'z topish o'yiniga xush kelibsiz!

🗣<b>So'z:</b> $gap
📜<b>Uzunligi:</b> $uzun
❎<b>Xato variantlar:</b> $xato
❌<b>Xatolar soni:</b> $now/10
👻<b>Holat:</b>

$status",
'parse_mode'=>'HTML',
'reply_markup'=>json_encode(['inline_keyboard'=>$keyboard]),
]);
$mid = $mid->result->message_id;
if($last) UP('last','`message_id` = "'.$mid.'"');
else IN('last','VALUES ("'.$mid.'","'.$cid.'")');
}
// GAME END; //

// SOZLAMA START //
if($tx == "/sozlama@GameUzbot"){
$status = getMember();
if($status == "member" or $status == "restricted" or $status == "left" or $status == "kicked"){
deleteM($mid);
exit;
}

$all_time = QS('all_time','sozlama');
$only_admins = QS('only_admins','sozlama');
$AT = str_replace(['y','n'],['✅','❌'],$all_time);
$OA = str_replace(['y','n'],['✅','❌'],$only_admins);

$page[] = ["callback_data"=>"s()all_time","text"=>"1: ".$AT];
$page[] = ["callback_data"=>"s()only_admins","text"=>"2: ".$OA];
$keyboard = array_merge(array_chunk($page,1));

bot('sendMessage',[
'chat_id'=>$cid,
'text'=>"<b>😎Admin sozlamalari.</b>

<b>1:</b> Bir a'zo ko'p marotaba javob berishi.
<b>2:</b> O'yinni faqat adminlar boshlay oladi.",
'parse_mode'=>'HTML',
'reply_markup'=>json_encode(['inline_keyboard'=>$keyboard]),
]);
}
// SOZLAMA END; //

if($tx == "/hisobot@GameUzbot"){
$status = getMember();
if($status == "member" or $status == "restricted" or $status == "left" or $status == "kicked"){
deleteM($mid);
exit;
}

$db = new SQLite3("baza.sqlite");
$all = $db->query("SELECT * FROM `hisobot` ORDER BY ok ASC LIMIT 10");
$i=1;
$javob = "";
while($info = $all->fetchArray()){
$return = bot('getChat',[
'chat_id'=>$info['chat_id'],
]);
$name = $return->result->title;
$ok = $info['ok'];
$nok = $info['nok'];
$jami = $info['jami'];
if($jami!=0){
$javob .= "\n\n<b>$i. </b><code>$name</code>
✅: $ok ❎: $nok 📊: $jami";
$i++;
}
}

bot('sendMessage',[
'chat_id'=>$cid,
'text'=>"🏆<b>TOP $i GURUH</b>".$javob,
'parse_mode'=>'HTML',
]);
$db->close();
}
} // GROUP END;

// DATA START //
if($data['0'] == 's'){
$status = getMember();
if($status == "member" or $status == "restricted" or $status == "left" or $status == "kicked"){
ACL("Siz admin emassiz.");
exit;
}

ACL();

$sozlama = QS($data['1'],'sozlama');
if($sozlama == "y") $set = 'n';
else $set = 'y';
UP('sozlama','`'.$data['1'].'` = "'.$set.'"');
$all_time = QS('all_time','sozlama');
$only_admins = QS('only_admins','sozlama');
$AT = str_replace(['y','n'],['✅','❌'],$all_time);
$OA = str_replace(['y','n'],['✅','❌'],$only_admins);

$page[] = ["callback_data"=>"s()all_time","text"=>"1: ".$AT];
$page[] = ["callback_data"=>"s()only_admins","text"=>"2: ".$OA];
$keyboard = array_merge(array_chunk($page,1));

bot('editMessageText',[
'chat_id'=>$cid,
'message_id'=>$mid,
'text'=>"<b>😎Admin sozlamalari.</b>

<b>1:</b> Bir a'zo ko'p marotaba javob berishi.
<b>2:</b> O'yinni faqat adminlar boshlay oladi.",
'parse_mode'=>'HTML',
'reply_markup'=>json_encode(['inline_keyboard'=>$keyboard]),
]);
}

if($data['0'] == 'l'){
sleep(1);
if(ENDQ()) exit;

if($data['1'] == '*'){
ACL("Bu harf oldin ishlatilgan.");
exit;
}

ACL();

$gaplist = QS('gap','games');
$check = str_split($gaplist);
$gap = [];
$i=0;

foreach($check as $k=>$harf){
if($data['1'] == $harf){
$gap[$k] = $harf;
++$i;
$togri = QS('togri','games');
UP('games','`togri` = "'.$togri.$harf.'"');
}else{
$gap[$k] = "*";
}
}

$togri = str_split(QS('togri','games'));
$natija = $gap;
foreach($togri as $k=>$harf){
foreach($check as $key=>$n){
if($n == $harf) $natija[$key] = $harf;
}
}
$natija = implode("",$natija);
UP('games','`natija` = "'.$natija.'"');

$now = QS('now','games');
$xato = QS('xato','games');

if($i==0){
$xato .= $data['1'];
UP('games','`xato` = "'.$xato.'"');
$now += 1;
if($now>=10) $now = 10;
UP('games','`now` = "'.$now.'"');
}

$mxato = str_split($xato);
$togri = str_split(QS('togri','games'));

foreach($alifbo as $alif){
if(in_array($alif,$mxato)) $alif = "*";
if(in_array($alif,$togri)) $alif = "*";
$page[] = ["callback_data"=>"l()".$alif,"text"=>$alif];
}
$keyboard = array_merge(array_chunk($page,5));

$uzun = count($gap);
$natija = QS('natija','games');
$status = $graph["$now"];

if($natija == $gaplist or $now > 9){

if($now>9){
$result = "😐Mag'lubiyat naqadar og'riqli";
}else{
$result = "🥳G'alaba muborak azizim";
}

bot('editMessageText',[
'chat_id'=>$cid,
'message_id'=>$mid,
'text'=>"❗️$result

🗣<b>So'z:</b> $natija
🏷<b>Asl so'z</b> $gaplist
📜<b>Uzunligi:</b> $uzun
❎<b>Xato variantlar:</b> $xato
❌<b>Xatolar soni:</b> $now/10
👻<b>Holat:</b>

$status",
'parse_mode'=>'HTML',
]);

IN('end_game','VALUES ("'.$cid.'","'.$mid.'")');
EX('DELETE FROM `games` WHERE `chat_id` = "'.$cid.'"');
exit;
}

bot('editMessageText',[
'chat_id'=>$cid,
'message_id'=>$mid,
'text'=>"🙋‍♂So'z topish o'yiniga xush kelibsiz!

🗣<b>So'z:</b> $natija
📜<b>Uzunligi:</b> $uzun
❎<b>Xato variantlar:</b> $xato
❌<b>Xatolar soni:</b> $now/10
👻<b>Holat:</b>

$status",
'parse_mode'=>'HTML',
'reply_markup'=>json_encode(['inline_keyboard'=>$keyboard]),
]);
exit;
}

if($data['0'] == 'h'){
$status = getMember();
if($status == "restricted" or $status == "left" or $status == "kicked"){
ACL("Siz guruhga a'zo emassiz.");
exit;
}

sleep(1);
if(ENDQ()) exit;

$all_time = QS('all_time','sozlama');
if($all_time == 'n'){
$id_list = QS('id_list','games');
$baza = explode(',',$id_list);
if(in_array($fid,$baza)){
ACL("Siz harf taklif qilib bo'lgansiz.");
exit;
}else{
$id_list .= $fid.",";
UP('games','`id_list` = "'.$id_list.'"');
}
} //

if($data['1'] == '*'){
ACL("Bu harf oldin ishlatilgan.");
exit;
}

ACL();

$gaplist = QS('gap','games');
$check = str_split($gaplist);
$gap = [];
$i=0;

foreach($check as $k=>$harf){
if($data['1'] == $harf){
$gap[$k] = $harf;
++$i;
$togri = QS('togri','games');
UP('games','`togri` = "'.$togri.$harf.'"');
}else{
$gap[$k] = "*";
}
}

$togri = str_split(QS('togri','games'));
$natija = $gap;
foreach($togri as $k=>$harf){
foreach($check as $key=>$n){
if($n == $harf) $natija[$key] = $harf;
}
}
$natija = implode("",$natija);
UP('games','`natija` = "'.$natija.'"');

$now = QS('now','games');
$xato = QS('xato','games');

if($i==0){
$xato .= $data['1'];
UP('games','`xato` = "'.$xato.'"');
$now += 1;
if($now>=10) $now = 10;
UP('games','`now` = "'.$now.'"');
}

$mxato = str_split($xato);
$togri = str_split(QS('togri','games'));

foreach($alifbo as $alif){
if(in_array($alif,$mxato)) $alif = "*";
if(in_array($alif,$togri)) $alif = "*";
$page[] = ["callback_data"=>"h()".$alif,"text"=>$alif];
}
$keyboard = array_merge(array_chunk($page,5));

$uzun = count($gap);
$natija = QS('natija','games');;
$status = $graph["$now"];

if($natija == $gaplist or $now > 9){

$hisobot = QQ('hisobot');
if($now>9){
$result = "😐Mag'lubiyat naqadar og'riqli";
$nok = $hisobot['nok'] + 1;
$jami = $hisobot['jami'] + 1;
UP('hisobot','`nok` = "'.$nok.'", `jami` = "'.$jami.'"');
}else{
$result = "🥳G'alaba muborak azizlarim";
$ok = $hisobot['ok'] + 1;
$jami = $hisobot['jami'] + 1;
UP('hisobot','`ok` = "'.$ok.'", `jami` = "'.$jami.'"');
}

bot('editMessageText',[
'chat_id'=>$cid,
'message_id'=>$mid,
'text'=>"❗️$result

🗣<b>So'z:</b> $natija
🏷<b>Asl so'z</b> $gaplist
📜<b>Uzunligi:</b> $uzun
❎<b>Xato variantlar:</b> $xato
❌<b>Xatolar soni:</b> $now/10
👻<b>Holat:</b>

$status",
'parse_mode'=>'HTML',
]);

IN('end_game','VALUES ("'.$cid.'","'.$mid.'")');
EX('DELETE FROM `games` WHERE `chat_id` = "'.$cid.'"');
exit;
}

bot('editMessageText',[
'chat_id'=>$cid,
'message_id'=>$mid,
'text'=>"🙋‍♂So'z topish o'yiniga xush kelibsiz!

🗣<b>So'z:</b> $natija
📜<b>Uzunligi:</b> $uzun
❎<b>Xato variantlar:</b> $xato
❌<b>Xatolar soni:</b> $now/10
👻<b>Holat:</b>

$status",
'parse_mode'=>'HTML',
'reply_markup'=>json_encode(['inline_keyboard'=>$keyboard]),
]);
exit;
} // DATA END;
?>