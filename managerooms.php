<?php
	/*
	* managerooms.php
	*/
	require_once(dirname(__FILE__) . '/../../config.php');
	require_once('mclib.php');
	//require_once($CFG->dirroot.'/my/lib.php');

	require_login();

    $context = context_system::instance();
    $PAGE->set_context($context);

	require_capability('block/lbcontrol:addinstance', $context);

	$PAGE->set_url('/blocks/lbcontrol/managerooms.php');
	$PAGE->navbar->add(get_string('navigate_0', 'block_lbcontrol'));
	$PAGE->navbar->add(get_string('navigate_1', 'block_lbcontrol'));
	$PAGE->navbar->add(get_string('navigate_2', 'block_lbcontrol'));
	$PAGE->navbar->add(get_string('navigate_3', 'block_lbcontrol'));
	$PAGE->navbar->add(get_string('navigate_room', 'block_lbcontrol'));
	$PAGE->set_title(get_string('managerooms', 'block_lbcontrol'));
	$PAGE->set_pagelayout('admin');

	echo $OUTPUT->header();
	echo $OUTPUT->heading(get_string('room_assign', 'block_lbcontrol', ''));

	echo $OUTPUT->notification(get_string('removeuserwarning', 'block_lbcontrol'));

    $PAGE->requires->js('/blocks/lbcontrol/jquery.min.js');
    $PAGE->requires->js('/blocks/lbcontrol/major.js');

	echo '<div>';

	$roomList = mediacenter_request(10113, array('PageSize'=>128, 'PageNum'=>1));
	if($roomList != null) {
		$index = 0;

		$text = '<table class="block_lbcontrol_report">';
		$text .= '<tr><th>'.get_string('no', 'block_lbcontrol').'</th><th>'.get_string('classroomname', 'block_lbcontrol').'</th><th>'.get_string('operation', 'block_lbcontrol').'</th></tr>';
		foreach($roomList->Rooms->Room as $r) {
				$index = $index + 1;

				$text .= '<tr>';
				$text .= '<td>'.$index.'</td>';
				$text .= '<td>'.$r->ClassRoomName.'</td>';
				$text .= '<td><a href="assign.php?id='.($r->Id).'">'.get_string('assign', 'block_lbcontrol').'</a></td>';
				$text .= '</tr>';
		}
		$text .= '</table>';
		echo $text;
	}

	echo '</div>';
	echo $OUTPUT->footer();
    
?>
