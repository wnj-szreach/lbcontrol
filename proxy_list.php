<?php
    /*
    * proxy_list.php
    */
	require_once(dirname(__FILE__) . '/../../config.php');
	require_once('mclib.php');
	require_once($CFG->dirroot.'/my/lib.php');
	
	// 校验登录
	require_login();

	$params =  array(
		'PageSize'=>32, 
		'PageNum'=>1,
		'UserName'=>($USER->username) 
	);
	$result = mediacenter_request(10110, $params);

	$text = '';
	if($result != null) {
		foreach($result->RoomLives->RoomLive as $r) {
			$text .=      "<li>";
			$text .=          "<span class='filed_room_name'>".($r->ClassRoomName)."</span>";
			$text .=      		"<span class='filed_operation'>";
			if($r->ConFlag == "1"){
								//预览
				$text .=        " <img src='../blocks/lbcontrol/pix/play.png' ";
				$text .=        " title='".get_string('preview', 'block_lbcontrol')."' ";
				$text .=        " alt='".get_string('preview', 'block_lbcontrol')."' ";
				$text .=        " onclick=\"openlive('".changeUrl2Moodle($r->LiveUrls->LiveUrl[0])."')\" />";

								//录制
				if($r->RecordFlag == "0"){
					$text .=        " <img src='../blocks/lbcontrol/pix/record.png' ";
					$text .=        " title='".get_string('record', 'block_lbcontrol')."' ";
					$text .=        " alt='".get_string('record', 'block_lbcontrol')."' ";
					$text .=        " onclick=\"roomControl('".($r->Id)."','10201')\" />";
				}
								//暂停
				if($r->RecordFlag == "1"){
					$text .=        " <img src='../blocks/lbcontrol/pix/pause.png' ";
					$text .=        " title='".get_string('pause', 'block_lbcontrol')."' ";
					$text .=        " alt='".get_string('pause', 'block_lbcontrol')."' ";
					$text .=        " onclick=\"roomControl('".($r->Id)."','10202')\" />";
				}
								//继续
				if($r->RecordFlag == "2"){
					$text .=        " <img src='../blocks/lbcontrol/pix/continue.png' ";
					$text .=        " title='".get_string('continue', 'block_lbcontrol')."' ";
					$text .=        " alt='".get_string('continue', 'block_lbcontrol')."' ";
					$text .=        " onclick=\"roomControl('".($r->Id)."','10203')\" />";
				}
								//停止
				if($r->RecordFlag != "0"){
					$text .=        " <img src='../blocks/lbcontrol/pix/stop.png' ";
					$text .=        " title='".get_string('stop', 'block_lbcontrol')."' ";
					$text .=        " alt='".get_string('stop', 'block_lbcontrol')."' ";
					$text .=        " onclick=\"roomControl('".($r->Id)."','10204')\" />";
				}
			}else{
				$text .=        " <img src='../blocks/lbcontrol/pix/disconnect.png' ";
				$text .=        " title='".get_string('disconnect', 'block_lbcontrol')."' ";
				$text .=        " alt='".get_string('disconnect', 'block_lbcontrol')."' ";
				$text .=        " onclick=\"alert('".get_string('disconnect', 'block_lbcontrol')."')\" />";
			}
			$text .=      	"</span>";
			$text .=        "<span class='clr'></span>";
			$text .=      "</li>";
		}
	}
	echo $text;
	exit;
?>
