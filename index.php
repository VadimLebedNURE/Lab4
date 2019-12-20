<?php
$file='';
$file_ip='';
$view_res=0;
if (isset($_POST['view_res']) and is_numeric($_POST['id'])) $view_res=1;
if (isset($_POST['vote']) and is_numeric($_POST['item'])
and is_numeric($_POST['id'])) {
 $f_name="vote".$_POST['id'].".txt";
 $f_ip="vote".$_POST['id']."_ip.txt";
 $ip=$_SERVER['REMOTE_ADDR'];

 $fh_ip=fopen($f_ip,"a+");
 flock($fh_ip,LOCK_EX);
 fseek($fh_ip,0);
 while (!feof($fh_ip)) $file_ip=fread($fh_ip,4096);
 if (array_search($ip,explode(",", $file_ip))!==FALSE) {
     $message="<b>Вы уже голосовали!</b><br>";
 }
 else if (file_exists($f_name)) {
    $fh=fopen($f_name,"a+");
    flock($fh,LOCK_EX);
    fseek($fh,0);
    while (!feof($fh)) $file=fread($fh,4096);
    $file=explode(",", $file);
    if ($_POST['item']>=0 and $_POST['item']<count($file)) $file[$_POST['item']]+=1;
    $file=implode(",",$file);
    ftruncate($fh,0);
    fwrite($fh,$file);
    flock($fh,LOCK_UN);
    fclose($fh);

    $file_ip.=$ip.',';
    fwrite($fh_ip,$ip.',');
    $message="<b>Ваш голос учтен!</b><br>";
 }
 $view_res=1;
 flock($fh_ip,LOCK_UN);
 fclose($fh_ip);
}

if ($view_res==1) {
 $f_name="vote".$_POST['id'].".txt";
    if (file_exists($f_name)) {
    $fh=fopen($f_name,"a+");
    flock($fh,LOCK_EX);
    fseek($fh,0);
    while (!feof($fh)) $file=fread($fh,4096);
    flock($fh,LOCK_UN);
    fclose($fh);
    $file=explode(",", $file);
    $summ=0;
    for ($n=0; $n<count($file); $n++) $summ+=$file[$n];
    if ($summ==0) $summ=1;
    for ($n=0; $n<count($file); $n++) $file[$n]=' - <b>'.$file[$n].
    '</b> ('.round(($file[$n]*100/$summ), 2).'%)';
 }
}

echo '<form method="POST" style="margin:0 0 0 35px;">Какой язык программирования вам нравится больше?
    <input type="hidden" name="id" value="1">';
echo '<table><tr><td><input type="radio" name="item" value="0" checked>
    С#</td><td>'.$file[0].'</td></tr>';
echo '<tr><td><input type="radio" name="item" value="1">C++</td><td>'.
    $file[1].'</td></tr>';
echo '<tr><td><input type="radio" name="item" value="2">Ruby</td><td>'.
    $file[2].'</td></tr>';
echo '<tr><td><input type="radio" name="item" value="3">Java</td><td>'.
    $file[3].'</td></tr>';
echo '<tr><td><input type="radio" name="item" value="4">Python</td><td>'.
    $file[4].'</td></tr>';
echo '<tr><td colspan="2"><input type="submit" name="view_res" value="Результат">
    <input type="submit" name="vote" value="Голосовать">';
echo '</td></tr></table>'.$message.'</form>';
?>