<?php
function processString($s){
  return preg_replace('@((https?://)?([-\w]+\.[-\w\.]+)+\w(:\d+)?(/([-\w/_\.]*(\?\S+)?)?)*)@', '<a target="_blank" href="$1">$1</a>', $s);
}
$unsafe=array(".", "!", "/", ";", "!", "?", "$", "#", "*", "'", '"');
$clean_room=str_replace( $unsafe, "", strip_tags((strlen($_GET['room']) > 10) ? substr($_GET['room'],0,10) : $_GET['room']));
$clean_data=processString(strip_tags( (strlen($_GET['chat']) > 500) ? substr($_GET['chat'],0,500) : $_GET['chat'] ));
$clean_user=strip_tags( (strlen($_GET['user']) > 15)  ? substr($_GET['user'],0,15) :  $_GET['user'] );

if  ($clean_data == "" || $clean_user == "" ) exit;
$file_data = "<div itemscope itemtype=\"https://schema.org/CommunicateAction\" class=\"textline\"><div class=\"usertime\"><span itemprop=\"participant\" class=\"username\">".$clean_user."</span><span itemprop=\"startTime\" class=\"timestamp\">".time()."</span>:</div><div itemprop=\"about\" class=\"usertext\">".$clean_data."</div></div><br>\n".file_get_contents('rooms/'.$clean_room.'.txt');
file_put_contents('rooms/'.$clean_room.'.txt',$file_data);
?>
