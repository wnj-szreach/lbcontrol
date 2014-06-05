<?php

/*
* assign.php(copy from /cohort/assign.php)
*/
require_once(dirname(__FILE__) . '/../../config.php');
require_once($CFG->dirroot.'/blocks/lbcontrol/locallib.php');
//require_once($CFG->libdir.'/sheep.php');

require_login();

$id = required_param('id', PARAM_RAW_TRIMMED);

//$context = context::instance_by_id($cohort->contextid, MUST_EXIST);
//$PAGE->set_context($context);
$context = context_system::instance();
$PAGE->set_context($context);

require_capability('block/lbcontrol:addinstance', $context);

$PAGE->set_url('/blocks/lbcontrol/assign.php', array('id'=>$id));

//navigation_node::override_active_url(new moodle_url('/blocks//lbcontrol/managerooms.php'));
$PAGE->navbar->add(get_string('navigate_auth', 'block_lbcontrol'));

$PAGE->set_title(get_string('managerooms', 'block_lbcontrol'));
$PAGE->set_heading($COURSE->fullname);

$room = mediacenter_request(10114, array('RoomId'=>$id));
if($room == null) {
	echo 'data not found';
	exit;	
}

echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('assignto', 'block_lbcontrol', (string)$room->Room->ClassRoomName));

echo $OUTPUT->notification(get_string('removeuserwarning', 'block_lbcontrol'));

// Get the user_selector we will need.
$potentialuserselector = new room_candidate_selector('addselect', array('roomid'=>$id, 'extrafields'=>''));
$existinguserselector = new room_existing_selector('removeselect', array('roomid'=>$id, 'extrafields'=>''));

// 添加操作
if (optional_param('add', false, PARAM_BOOL) && confirm_sesskey()) {
	$userstoassign = $potentialuserselector->get_selected_users();
	if (!empty($userstoassign)) {

		foreach ($userstoassign as $adduser) {
			room_add_auth_users($id, $userstoassign);
		}

		$potentialuserselector->invalidate_selected_users();
		$existinguserselector->invalidate_selected_users();
	}
}

// 移除操作
if (optional_param('remove', false, PARAM_BOOL) && confirm_sesskey()) {
	$userstoremove = $existinguserselector->get_selected_users();
	if (!empty($userstoremove)) {
		foreach ($userstoremove as $removeuser) {
			room_remove_auth_users($id, $userstoremove);
		}
		$potentialuserselector->invalidate_selected_users();
		$existinguserselector->invalidate_selected_users();
	}
}

// Print the form.
?>
<form id="assignform" method="post" action="<?php echo $PAGE->url ?>"><div>
	<input type="hidden" name="sesskey" value="<?php echo sesskey() ?>" />

  <table summary="" class="generaltable generalbox boxaligncenter" cellspacing="0">
	<tr>
	  <td id="existingcell">
		  <p><label for="removeselect"><?php print_string('currentusers', 'cohort'); ?></label></p>
		  <?php $existinguserselector->display() ?>
	  </td>
	  <td id="buttonscell">
		  <div id="addcontrols">
			  <input name="add" id="add" type="submit" value="<?php echo $OUTPUT->larrow().'&nbsp;'.s(get_string('add')); ?>" title="<?php p(get_string('add')); ?>" /><br />
		  </div>

		  <div id="removecontrols">
			  <input name="remove" id="remove" type="submit" value="<?php echo s(get_string('remove')).'&nbsp;'.$OUTPUT->rarrow(); ?>" title="<?php p(get_string('remove')); ?>" />
		  </div>
	  </td>
	  <td id="potentialcell">
		  <p><label for="addselect"><?php print_string('potusers', 'cohort'); ?></label></p>
		  <?php $potentialuserselector->display() ?>
	  </td>
	</tr>
	<tr><td colspan="3" id='backcell'>
    	<a href="managerooms.php"><?php echo get_string('backtoallrooms', 'block_lbcontrol'); ?></a>
	</td></tr>
  </table>
</div></form>

<?php

echo $OUTPUT->footer();
?>
