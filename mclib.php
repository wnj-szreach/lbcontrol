<?php
	/*
	* mclib.php
	*/
	require_once(dirname(__FILE__) . '/../../config.php');

	// 请求的公共方法
	function mediacenter_request($code, $params) {

		$rt = null;		

		if(empty($code)) {
			return $rt;
		}
		
		$xml_request  =     '<?xml version="1.0" encoding="UTF-8" ?>';
		$xml_request .=     '<RequestMsg>';
		$xml_request .=         '<MsgHead>';
		$xml_request .=             '<MsgCode>'.$code.'</MsgCode>';
		$xml_request .=         '</MsgHead>';
		$xml_request .=         '<MsgBody>';
		$xml_request .=         params2xml($params);
		$xml_request .=         '</MsgBody>';
		$xml_request .=     '</RequestMsg>';

		try {
			$xml_response       = do_post_request(get_mc_host().'/XmlRpcService.action', $xml_request);
			$xml_object         = simplexml_load_string($xml_response);
			
			if($xml_object->MsgHead->ReturnCode == 1) {
				$rt  = $xml_object->MsgBody;
			}else{
				echo $xml_object->MsgBody->FaultString;
			}
		}catch(Exception $e) {
			echo $e->getMessage();
		}

		return $rt;
	}

	// 获取并修正媒体中心地址
	function get_mc_host() {
		global $CFG;
		
		$mc_host  = $CFG->block_lbcontrol_host;
		if ($mc_host == null || $mc_host == '') {
			$mc_host = 'http://127.0.0.1';
		} else if (!(strpos($mc_host, 'http') === 0)) {//地址修正
			$mc_host = 'http://'.$mc_host;
		}
		return $mc_host;
	}

	// 参数转换为xml
	function params2xml($params, $_key = null) {
		$rt = '';
		if(empty($params)) {
			return '';
		}
		if(!is_array($params)) {// 使得参数可以接收字符串
			return $params;
		}
		
		foreach($params as $key => $val) {
			$__key = (is_numeric($key) && $_key != null)? substr($_key, 0, strlen($_key) - 1) : $key;

			$rt .= '<'.$__key.'>';
			if(!is_array($val)) {
				$rt .= $val;
			}else{
				$rt .= params2xml($val, $key);
			}
			$rt .= '</'.$__key.'>';
		}
		return $rt;
	}

    // 把获取到的地址改成通过moodle方法的代理地址
    function changeUrl2Moodle($url) {
        if($url != null) {
            $arr = explode('?', $url);
            //$arr2 = explode('repository', $_SERVER['PHP_SELF']);//娘的，处理可能的上下文，虽然PHP里面没有上下文的概念
            //$url = 'http://'.$_SERVER['HTTP_HOST'].$arr2[0].'blocks/lbcontrol/live.php?'.$arr[1];
            $qstr = str_replace('&preview=1', '', $arr[1]);
            $url = '../'.'blocks/lbcontrol/proxy_live.php?'.$qstr;
        }
        return $url;
    }

	// 发送POST请求的方法
	function do_post_request($url, $data, $optional_headers = null) {
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
?>
