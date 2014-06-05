<?php
    /*
    * proxy_live.php
    */
	require_once(dirname(__FILE__) . '/../../config.php');
	require_once($CFG->dirroot.'/my/lib.php');
	require_once('mclib.php');
	require_once($CFG->libdir.'/sheep.php');
	
	
	/*$params = array(
		'OptType'=>0,
		'Users'=>array{
			array(
				'UserName'=>'sheep1',
				'FullName'=>'绵羊1',
				'Email'=>'sheep1@szreach.com'
			),
			array(
				'UserName'=>'sheep2',
				'FullName'=>'绵羊2',
				'Email'=>'sheep2@szreach.com'
			)
		}
	);*/
	$params = array(
		'OptType'=>2,
		'Users'=>array(
			array(
				'UserName'=>'sheep1',
				'FullName'=>'绵羊1',
				'Email'=>'sheep1@szreach.com'
			),
			array(
				'UserName'=>'sheep2',
				'FullName'=>'绵羊2',
				'Email'=>'sheep2@szreach.com'
			)
		)
	);
	//$rs = $mc->request(10101, $params);
	$rs = mediacenter_request(10102, array());
	if($rs != null) {
		print_r($rs);
		//echo 2;
	}
?>
