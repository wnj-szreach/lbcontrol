<?php

	/*
	* proxy_control.php
	*/
	require_once(dirname(__FILE__) . '/../../config.php');
	require_once('mclib.php');
	require_once($CFG->dirroot.'/my/lib.php');
	//require_once($CFG->libdir.'/sheep.php');
	
	// 校验登录
	require_login();

	// 获取参数
	$rid = $_GET['rid'];
	$opt = $_GET['opt'];
	if(empty($rid) || empty($opt)) {
		echo 'rid or opt is empty';
		exit;
	}

	// 不具有Moodle管理员权限
	if(!has_capability('moodle/site:config', context_system::instance()) 
	&& !has_capability('block/lbcontrol:addinstance', context_system::instance())) {

		$result = mediacenter_request(10405, array('UserName'=>$USER->username, 'RoomId'=>$rid));
		if($result == null) {
			exit;
		}

		// 对该教室不具有管理权限
		if($result->Result != 1) {
			echo 'has no auths';
			exit;
		}
	}
	
	$params =  array(
		'RoomId'=>$rid, 
		'UserName'=>($USER->username), 
		'UserOption'=>1,
		'Publish'=>0
	);
	$result = mediacenter_request($opt, $params);

	if($result == null) {
		exit;
	}

	$msg = $result->Result;		

	// 关于返回的国际化，以后再说
	echo $msg;
	exit;

?>
