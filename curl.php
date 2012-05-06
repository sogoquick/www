<?php

function get_content_by_curl($url){
    $hd =  curl_init();
    curl_setopt($hd,CURLOPT_URL,$url);
    curl_setopt($hd,CURLOPT_HEADER,1);
    $header[] = 'Accept:text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8';
    $header[] = 'Connection:keep-alive';
    $Header[] = 'Accept-Language:zh-CN,zh;q=0.8,en-US;q=0.6,en;q=0.4';
    //$header[] = 'Cookie:__gads=ID=6017110462e4e5aa:T=1327904210:S=ALNI_Mat7P-rLBnlfUUTCIS0NNPVmNMYfg; afalastestcityid=11; aQQ_ajkguid=EB7BC2D5-513D-0DA3-0E75-C1C5D3619935; ved_loupans=239712%7C1331603713374%2C205003%7C1328775577025; ctid=11; Hm_lvt_7d4fbfe06457b1b1d72f1ba41625b04f=1333268559867; __utma=1.1268947705.1332148499.1333013397.1333267572.24; aQQ_ajkauthinfos=fxBlbDeIv6yLpCzNTpjBUL5T9CJPWg8qDLSC7M9WxGbO; aQQ_afweb_uid=3729248; sessid=C8651B18-D980-BA8D-DCA2-7676CB15A7C6; isp=true';
    $header[] = 'Cookie:aQQ_ajkguid=3402974C-894B-2C76-7EA8-9CB14E550409; __gads=ID=6017110462e4e5aa:T=1327904210:S=ALNI_Mat7P-rLBnlfUUTCIS0NNPVmNMYfg; afalastestcityid=11; aQQ_ajkguid=EB7BC2D5-513D-0DA3-0E75-C1C5D3619935; ved_loupans=239712%7C1331603713374%2C205003%7C1328775577025; ved_loupans=200915%7C1332244223559%2C239712%7C1331603713374%2C205003%7C1328775577025; afalastestcityid=11; ved_trips=217207%2C200915%2C240349; ctid=11; aQQ_ajkauthinfos=fxBlbDeIv6yLpCzNTpjBUL5T9CJPWg8qDLSC7M9WxGbO; aQQ_afweb_uid=3729248; sessid=C8651B18-D980-BA8D-DCA2-7676CB15A7C6; visited_props=303%7C1336291742576; ctid=20; __utma=1.1268947705.1332148499.1333267572.1336291714.25; __utmb=1.3.9.1336291714; __utmc=1; __utmz=1.1336291714.25.1.utmcsr=(direct)|utmccn=(direct)|utmcmd=(none); Hm_lvt_7d4fbfe06457b1b1d72f1ba41625b04f=1336291742851; Hm_lpvt_7d4fbfe06457b1b1d72f1ba41625b04f=1336291742851; isp=true; aQQ_ajkauthinfos=LBg6PTODsf6LpS%2FFRJrMUL5T9CJPWg8qDLSC7M9WxGbO; aQQ_afweb_uid=3611865; isp=true';
    $header[] = 'User-Agent:Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/535.19 (KHTML, like Gecko) Chrome/18.0.1025.168 Safari/535.19';
    $header[] = 'Referer:http://my.aifang.com/seo/user/';
    curl_setopt($hd,CURLOPT_HTTPHEADER,$header);
    $content = curl_exec($hd);
    curl_close($hd);
    return $content;
}
echo get_content_by_curl('http://my.aifang.com/seo/user/s?p=3');
