<?php
require('mail.php');
$fetch = new fetch();
$content = $fetch->fetch('http://www.ftchinese.com');
class fetch {

    private $waithtofetch;

    private $fetchnav = '/home/sogo/code/www/ftnav';
    
    private $ftcontent = '/home/sogo/code/www/ft-';
    
    private $bodyrule = '';
    //没有分页的规则
    private $bodyruleno = '/<div class="content" id="bodytext">[\t\s*\n]*<div>(.*)[\t\s*\n]*<div class="story_list">/isU';
    //有分页时规则
    private $bodyrulepage = '/<div class="content" id="bodytext">[\t\s*\n]*<div>(.*)[\t\s*\n]*<div class=clearFloat>/isU';

    private $domain = 'http://www.ftchinese.com';

    private $filetype = '.txt';

    private $havefetch = '/home/sogo/code/www/havefetch.log';

    private $proxy_num = 1;

    public function __construct() {
        $this->ftcontent .= date('Ymd');
        if(file_exists($this->ftcontent)) {
            unlink($this->ftcontent);
        }
        $this->waittofetch = 5;
    }

    /**
     * 抓取入口 
     * 
     * @param mixed $url 
     * @access public
     * @return void
     */
    public function fetch($url) {
        $nav = $this->fetch_nav_content($url);
        //按导航抓取
        foreach ($nav as $key => $val) {
           $eachlink = $this->fetch_each_link_content($val['link'],$val['name']); 
           if(file_exists($this->havefetch)) {
               $havefetch = file_get_contents($this->havefetch,true);
           } else {
               $havefetch = '';
           }
           $success = false;
           //按每页内容抓取
           if($eachlink) {
                foreach ($eachlink as $k => $v) {
                   $body = $this->fetch_content($v['link']);
                   if(preg_match('/'.$v['article_id'].'/',$havefetch)){
                        print_r('已经抓取'.$v['article_id']."\n");
                        continue;
                   }
                   if($body) {
                        print_r('开始抓取'.$v['article_id']."\n");
                        file_put_contents($this->havefetch,print_r($v['article_id'].",\n",true),FILE_APPEND); 
                        file_put_contents($this->ftcontent.$val['name'].$this->filetype,print_r($v['name'],true)."\n",FILE_APPEND); 
                        file_put_contents($this->ftcontent.$val['name'].$this->filetype,print_r($v['link'],true)."\n\n",FILE_APPEND); 
                        file_put_contents($this->ftcontent.$val['name'].$this->filetype,print_r($body,true)."\n\n",FILE_APPEND); 
                        $success = true;
                   }
                }
           }
           //如果抓取的为老数据，或者失败，删掉该类文件
           $filename = $this->ftcontent.$val['name'].$this->filetype;
           if(!$success) {
               if(file_exists($filename)) {
                   unlink($filename);
               }
           } else {
                SendMail::get_instance()->send($filename);
           }
        }
    }

    public function fetch_content($url) {
        $content = $this->get_file_content_pass_wall($url);
        preg_match_all('/<a class=storytime href="(.*)">(.*)<\/a>/',$content,$publishtime);
        preg_match_all('/([0-9]+)年([0-9]+)月([0-9]+)日/',$publishtime[2][0],$time);
        $publishtime = mktime('0','0','0',$time[2][0],$time[3][0],$time[1][0]);
        $wish = mktime('0','0','0',date('m')-1,date('d'),date('Y'));
       if($wish > $publishtime) {
            return '';
       }
        preg_match_all('/<div class="pagination">(.*)<\/div>/isU',$content,$pages);
        $this->bodyrule = $this->bodyruleno;
        if($pages && count($pages[1]) > 0) {
            $this->bodyrule = $this->bodyrulepage;
            $body = $this->get_body_content($content);
            preg_match_all('/<a href="(.*)?">(.*)?<\/a>/U',$pages[1][0],$pageurls);
            $pageurls = array_unique($pageurls[1]);
            $body .= $this->fetch_page_content($pageurls);
        } else {
            $body = $this->get_body_content($content);
        }
        return $body;

    }

    /**
     *获取分页content内容
     *@params array $urls
     *@return string
     * */
    public function fetch_page_content($urls) {
        $result = '';
        foreach ($urls as $key => $val) {
            if(!preg_match('/http:/',$val)) {
                $val = $this->domain.$val;
            }
            $content = $this->get_file_content_pass_wall($val);
            $body = $this->get_body_content($content);
            $result .= $body;
        }
        return $result;
    }

    /**
     *获取需要的内容部分
     *@param string $content
     * */
    public function get_body_content($content) {
        $result = '';
        if(empty($content)) {
            return $result;
        }
        preg_match_all($this->bodyrule,$content,$body);
        if(isset($body[1][0])) {
             $body = preg_replace('/<p>/',"    ",$body[1][0]);
             $body = preg_replace("/\n\n/","\n",$body);
             $body = strip_tags($body);
             $result = $body;
        }
        return $result;
    }

	public function fetch_each_link_content($url,$channel) {
		$content = $this->get_file_content_pass_wall($url);
        preg_match_all('/<h3><a target="(.*)" href="(.*)?">(.*)?<\/a>/',$content,$match);
        $savefile = $this->ftcontent.$channel.$this->filetype;
        if(file_exists($savefile)) {
            unlink($savefile);
        }
        foreach ($match[3] as $key => $val) {
            if($val) {
                $nav[$key]['name'] = $val;
                file_put_contents($savefile,print_r($val,true)."\n",FILE_APPEND);
                if(preg_match('/http:/',$match[2][$key])) {
                    $nav[$key]['link'] = $match[2][$key];
                } else {
                    $nav[$key]['link'] = $this->domain.$match[2][$key];
                }
                preg_match_all('/[0-9]+/',$match[2][$key],$id);
                $nav[$key]['article_id'] = $id[0][0];
            }
        }
        return $nav;
    }

    /**
     *获取导航url 
     * */
	public function fetch_nav_content($url) {
        if(file_exists($this->fetchnav)) {
            $nav = file_get_contents($this->fetchnav,true);
            $nav = json_decode($nav,true);
            if(count($nav) > 0) {
                return $nav;
            }
        }
		$content = $this->get_file_content_pass_wall($url);
        preg_match_all('/<div id="navigation" class="pagediv">(.*)<\/div>/isU',$content,$navcontent);
        preg_match_all('/<li(.*)><a href="(.*)">(.*)<\/a><\/li>/',$navcontent[1][0],$match);
        foreach ($match[2] as $key => $val) {
            if(!preg_match('/http:/',$val)){
                $val = $this->domain.$val;
            }
            if(!preg_match('/channel/',$val) || preg_match('/rss|slides|video/',$val)) {
                continue;
            }
            $nav[$key]['link'] = $val;
            $nav[$key]['name'] = $match[3][$key];
        }
        if(file_exists($this->fetchnav)){
            unlink($this->fetchnav);
        }
        file_put_contents($this->fetchnav,print_r(json_encode($nav),true),FILE_APPEND);
        return $nav;
	}
    /**
     * 对获取网页file_get_content包装
     * @return text
     * **/
    public function get_file_content($url) {
        $proxy = $this->get_proxy_list();
        $ctx = stream_context_create(array(
                'http' => array(
                        'timeout' => 10,
                        'proxy' => $proxy,
                        'request_fulluri' => True
                )
        ));

        $result = @file_get_contents($url,false,$ctx);

        if($result) {
             //设置抓取内容时间间隔
             sleep($this->waittofetch);
        }else{
            $this->proxy_num++;
            if($this->proxy_num > 3) {
                $this->proxy_num = 0;
                return '';
            }
            $this->get_file_content($url);
        }
        return $result;
    }

    public function get_file_content_pass_wall($url) {
        $ctx = stream_context_create(array(
                'http' => array(
                        'timeout' => 10,
                        'proxy' => 'tcp://127.0.0.1:8087',
                        'request_fulluri' => True
                )
        ));

        $result = @file_get_contents($url,false,$ctx);

        if($result) {
              sleep($this->waittofetch);//设置抓取内容时间间隔
        }
        return $result;
    }

    /**
     * 代理
     * return 'tcp://192.168.1.128:3128';
     */
    private function get_proxy_list() {
        $proxy = array(
            0 => '180.96.19.25:8080',1 => '221.180.22.239:3128', 2 => '116.236.205.100:80', 3 => '60.209.7.54:8080', 4 => '60.210.169.246:8888', 5 => '124.244.242.72:3128'
        );
        $i = rand(0,5);
        return 'tcp://'.$proxy[$i];
        
    }

}
