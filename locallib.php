<?php

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/user/selector/lib.php');
require_once('mclib.php');


// 无权限用户控件
class room_candidate_selector extends user_selector_base {
	protected $roomid;

	public function __construct($name, $options) {
		$this->roomid = $options['roomid'];
		parent::__construct($name, $options);
	}

	public function find_users($search) {
		global $DB;
		
		// 内置变量修正
		$this->extrafields = array('username');

		list($wherecondition, $params) = $this->search_sql($search, 'u');
        $fields      = 'SELECT ' . $this->required_fields_sql('u');
        $countfields = 'SELECT COUNT(1)';

		// 发送请求查询用户
		$users = get_room_auth_users($this->roomid);
		if($users != null) {

			$user_str_list = '';
			foreach($users->User as $u) {
				$user_str_list .= ',\''.(string)$u->UserName.'\'';
			}
			if(!empty($user_str_list)) {
				$user_str_list = substr($user_str_list, 1);
				$wherecondition .= ' AND u.username NOT IN ('.$user_str_list.') ';
			}

			$sql = " FROM {user} u
					WHERE $wherecondition";

			list($sort, $sortparams) = users_order_by_sql('u', $search, $this->accesscontext);
			$order = ' ORDER BY ' . $sort;

			if (!$this->is_validating()) {
				$potentialmemberscount = $DB->count_records_sql($countfields . $sql, $params);
				if ($potentialmemberscount > 100) {
					return $this->too_many_results($search, $potentialmemberscount);
				}
			}

			$availableusers = $DB->get_records_sql($fields . $sql . $order, array_merge($params, $sortparams));

			if (empty($availableusers)) {
				return array();
			}

			if ($search) {
				$groupname = get_string('potusersmatching', 'cohort', $search);
			} else {
				$groupname = get_string('potusers', 'cohort');
			}

			return array($groupname => $availableusers);
		}
		
		return array();// 如果接口访问失败，返回空
	}

    protected function get_options() {
        $options = parent::get_options();
        $options['roomid'] = $this->roomid;
        $options['file'] = 'blocks/lbcontrol/locallib.php';
        return $options;
    }
}


// 有权限用户控件
class room_existing_selector extends user_selector_base {
	protected $roomid;

	public function __construct($name, $options) {
		$this->roomid = $options['roomid'];
		parent::__construct($name, $options);
	}

	public function find_users($search) {
		global $DB;

		// 内置变量修正
		$this->extrafields = array('username');

		list($wherecondition, $params) = $this->search_sql($search, 'u');
        $fields      = 'SELECT ' . $this->required_fields_sql('u');
        $countfields = 'SELECT COUNT(1)';

		// 发送请求查询用户
		$users = get_room_auth_users($this->roomid);
		if($users != null) {

			$user_str_list = '';
			foreach($users->User as $u) {
				$user_str_list .= ',\''.(string)$u->UserName.'\'';
			}
			if(!empty($user_str_list)) {
				$user_str_list = substr($user_str_list, 1);
				$wherecondition .= ' AND username IN ('.$user_str_list.') ';
			}else{
				$wherecondition .= ' AND 1=0 ';
			}

			$sql = " FROM {user} u
					WHERE $wherecondition";

			list($sort, $sortparams) = users_order_by_sql('u', $search, $this->accesscontext);
			$order = ' ORDER BY ' . $sort;

			if (!$this->is_validating()) {
				$potentialmemberscount = $DB->count_records_sql($countfields . $sql, $params);
				if ($potentialmemberscount > 100) {
					return $this->too_many_results($search, $potentialmemberscount);
				}
			}

			$availableusers = $DB->get_records_sql($fields . $sql . $order, array_merge($params, $sortparams));

			if (empty($availableusers)) {
				return array();
			}

			if ($search) {
				$groupname = get_string('currentusersmatching', 'cohort', $search);
			} else {
				$groupname = get_string('currentusers', 'cohort');
			}

			return array($groupname => $availableusers);
		}

		return array();// 接口调用失败返回空
	}

    protected function get_options() {
        $options = parent::get_options();
        $options['roomid'] = $this->roomid;
        $options['file'] = 'blocks/lbcontrol/locallib.php';
        return $options;
    }
}


// 根据roomid查找有权限的用户
function get_room_auth_users($roomid) {

	$xml_request  =     '<?xml version="1.0" encoding="UTF-8" ?>';
	$xml_request .=     '<RequestMsg>';
	$xml_request .=         '<MsgHead>';
	$xml_request .=             '<MsgCode>10404</MsgCode>';
	$xml_request .=         '</MsgHead>';
	$xml_request .=         '<MsgBody>';
	$xml_request .=             '<RoomId>'.$roomid.'</RoomId>';
	$xml_request .=         '</MsgBody>';
	$xml_request .=     '</RequestMsg>';

	try{
		$xml_response       = do_post_request(get_mc_host().'/XmlRpcService.action', $xml_request);
		$xml_object         = simplexml_load_string($xml_response);

		if($xml_object->MsgHead->ReturnCode == 1) {
			return $xml_object->MsgBody->Users;
		}
	}catch(Exception $e) {
		return null;
	}
	return null;
}

// 给多个用户添加教室管理权限
function room_add_auth_users($roomid, $users) {

	if(!empty($roomid) && !empty($users)) {

		$xml_request  =     '<?xml version="1.0" encoding="UTF-8" ?>';
		$xml_request .=     '<RequestMsg>';
		$xml_request .=         '<MsgHead>';
		$xml_request .=             '<MsgCode>10402</MsgCode>';// 教室列表
		$xml_request .=         '</MsgHead>';
		$xml_request .=         '<MsgBody>';
		$xml_request .=             '<RoomId>'.$roomid.'</RoomId>';
		$xml_request .=             '<UserOption>1</UserOption>';
		$xml_request .=             '<Users>';
		foreach($users as $u) {
			$xml_request .=             '<User><UserName>'.($u->username).'</UserName></User>';
		}
		$xml_request .=             '</Users>';
		$xml_request .=         '</MsgBody>';
		$xml_request .=     '</RequestMsg>';

		try{
			$xml_response       = do_post_request(get_mc_host().'/XmlRpcService.action', $xml_request);
			$xml_object         = simplexml_load_string($xml_response);
		}catch(Exception $e) {
		}

	}

}

// 取消多个用户的教室管理权限
function room_remove_auth_users($roomid, $users) {

	if(!empty($roomid) && !empty($users)) {

		$xml_request  =     '<?xml version="1.0" encoding="UTF-8" ?>';
		$xml_request .=     '<RequestMsg>';
		$xml_request .=         '<MsgHead>';
		$xml_request .=             '<MsgCode>10403</MsgCode>';// 教室列表
		$xml_request .=         '</MsgHead>';
		$xml_request .=         '<MsgBody>';
		$xml_request .=             '<RoomId>'.$roomid.'</RoomId>';
		$xml_request .=             '<UserOption>1</UserOption>';
		$xml_request .=             '<Users>';
		foreach($users as $u) {
			$xml_request .=             '<User><UserName>'.($u->username).'</UserName></User>';
		}
		$xml_request .=             '</Users>';
		$xml_request .=         '</MsgBody>';
		$xml_request .=     '</RequestMsg>';

		try{
			$xml_response       = do_post_request(get_mc_host().'/XmlRpcService.action', $xml_request);
			$xml_object         = simplexml_load_string($xml_response);
		}catch(Exception $e) {
		}

	}

}

?>
