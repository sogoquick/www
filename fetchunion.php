<?php
$refresh_true = $argv[1];
if ($refresh_true || !file_exists('/tmp/186.log')) {
    exec("rm /tmp/186.log");
    if ($argv[2] == 186) {
        exec("/usr/bin/curl 'http://num.10010.com/NumApp/chseNumList/serchNums?province=31&cityCode=310&preFeeSel=&keyValue=186&roleValue=&searchType=02&sortType=numAsc&searchValue=' |grep -o '1[8|5][0-3|5-9]\{9\}' > /tmp/186.log");
    } else {
    exec("/usr/bin/curl 'http://num.10010.com/NumApp/chseNumList/serchNums?province=31&cityCode=310&sortType=numAsc' |grep -o '1[8|5][0-3|5-9]\{9\}' > /tmp/186.log");
    }
}
$content = file_get_contents('/tmp/186.log',true);
$content = explode("\n",$content);
array_pop($content);
$mobilenum = $content;
$mailbody = "";
foreach ($mobilenum as $key => $val) {
    //需找6,8的多的号码
    $count = 0;
    $sixcount = 0;
    str_replace('8','',$val.$count);
    str_replace('6','',$val.$sixcount);
    if (preg_match('/^\\d*(1688|2688|2088|2008|5188|10010|10001|666|888|668|686|688|866|868|886|999)\\d*$/',$val)){
        echo $val."\t----Perfect----\n";
        $mailbody .=$val."(Perfect)\n";
    } elseif(preg_match('/^\\d*(\\d)\\1\\1(\\d)\\2\\2\\d*$/',$val)) {
         echo $val."\t----AAABBB----\n";
         $mailbody .= $val."(AAABBB)\n";
    } elseif(preg_match('/^\\d*(\\d)\\1(\\d)\\2\\d*$/',$val)) {
        echo $val."\t----AABB----\n";
        $mailbody .= $val."(AABB)\n";
    } elseif(preg_match('/^(\\d)(\\d)\\1\\2\\1\\2\\1\\2$/',$val)) { 
        echo $val."\t----ABABAB----\n";
        $mailbody .= $val."(ABABAB)\n";
    } elseif(preg_match('/^(\\d)(\\d)(\\d)\\1\\2\\3$/',$val)) { 
        echo $val."\t----ABCABC----\n";
        $mailbody .= $val."(ABCABC)\n";
    } elseif(preg_match('/^(\\d)(\\d)\\2\\1\\2\\2$/',$val)) {
        echo $val."\t----ABBABB----\n";
        $mailbody .= $val."(ABBABB)\n";
    } elseif(preg_match('/^(\\d)\\1(\\d)\\1\\1\\2$/',$val)) {
        echo $val."\t----AABAAB----\n";
        $mailbody .= $val."(AABAAB)\n";
    } elseif(preg_match('/^\\d*(\\d)\\1{2,}\\d*$/',$val)) {
        echo $val."\t----AAAA----\n";
        $mailbody .= $val."(AAAA)\n";
    } elseif(preg_match('/(?:(?:0(?=1)|1(?=2)|2(?=3)|3(?=4)|4(?=5)|5(?=6)|6(?=7)|7(?=8)|8(?=9)|9(?=0)){2,}|(?:0(?=9)|9(?=8)|8(?=7)|7(?=6)|6(?=5)|5(?=4)|4(?=3)|3(?=2)|2(?=1)|1(?=0)){2,})\\d/',$val)) {
        echo $val."\t----ABCD----\n";
        $mailbody .= $val."(ABCD)\n";
    } elseif(preg_match('/^[0-9]*(518|918|168|618)$/',$val)) {
        echo $val."\t----18end----\n";
        $mailbody .=$val."(18end)\n";
    } elseif($count >= 3) {
        echo $val."\t----$count----\n";
        $mailbody .= $val."($count)\n";
    } elseif($sixcount > 3) {
        echo $val."\t----$sixcount----\n";
        $mailbody .= $val."($sixcount)\n";
    }
}
$mailbody = strip_tags($mailbody);
if ($mailbody) {
    require "mail.php";
    SendMail::get_instance()->send_mail("1582070474@qq.com","靓号",$mailbody);
}
