<?php
//require_once($CFG->libdir.'/sheep.php');
class block_lbcontrol extends block_base{

    private $host       = null;
    private $url        = null;

    function init() {
		global $CFG;
        $this->title = get_string('pluginname', 'block_lbcontrol');
		
		//$this->host 			= $CFG->block_lbcontrol_host;//这样获取配置页的配置
		//$this->useadmin 		= $CFG->block_lbcontrol_useadmin;

		// 先使用repository的配置
        $this->host             = get_config('mediacenter', 'host');//这样获取配置页的配置
        //$this->useadmin         = get_config('mediacenter', 'useadmin');

		if ($this->host == null || $this->host == '') {
			$this->host = 'http://127.0.0.1';
		} else if (!(strpos($this->host, 'http') === 0)) {//地址修正
			$this->host = 'http://'.$this->host;	
		}

		$this->url = $this->host.'/XmlRpcService.action';
		//log_info(strtotime(date("Y-m-d",time()-3600*24)));
    }

    function get_content() {
        global $CFG, $USER, $PAGE;

        if ($this->content !== NULL) {
            return $this->content;
        }

        $this->content = new stdClass;
        $this->content->text = ''; 
        $this->content->footer = '';

        if (empty($this->instance)) {
            return $this->content;
        }


		$PAGE->requires->js('/blocks/lbcontrol/jquery.min.js');
		$PAGE->requires->js('/blocks/lbcontrol/major.js');

		$myfile = '../'.'blocks/lbcontrol/proxy_mcbackstage.php?type=menu_coursewareManager&menu=menu_myFileManager';
		$text = '';
		//$footer = '';
        $text .= "<ul class='block_lbcontrol_wrap' id='roomDataList'></ul>";//ul里面的数据用ajax填充
        $text .= "<a class='view_file' href='".$myfile."' target='_blank'>".get_string('view_file', 'block_lbcontrol')."</a>";
		$this->content->text = $text;
		//$this->content->footer = $footer;

        return $this->content;
    }
	
	// Since version 2.4
	function has_config() {
		return true;
	}

    // 把获取到的地址改成通过moodle方法的代理地址
    private function changeUrl2Moodle($url) {
        if($url != null) {
            $arr = explode('?', $url);
            //$arr2 = explode('repository', $_SERVER['PHP_SELF']);//娘的，处理可能的上下文，虽然PHP里面没有上下文的概念
            //$url = 'http://'.$_SERVER['HTTP_HOST'].$arr2[0].'blocks/lbcontrol/live.php?'.$arr[1];
			$qstr = str_replace('&preview=1', '', $arr[1]);
            $url = '../'.'blocks/lbcontrol/proxy_live.php?'.$qstr;
        }
        return $url;
    }

    //发送post请求
    private function do_post_request($url, $data, $optional_headers = null) {
        $params = array('http' => array(
                  'method' => 'POST',
                  'content' => $data
        ));
        if ($optional_headers !== null) {
            $params['http']['header'] = $optional_headers;
        }
        $ctx = stream_context_create($params);
        $fp = @fopen($url, 'rb', false, $ctx);
        if (!$fp) {
            throw new Exception("Problem with $url");
        }
        $response = @stream_get_contents($fp);
        if ($response === false) {
            throw new Exception("Problem reading data from $url");
        }
        return $response;
    }
}


