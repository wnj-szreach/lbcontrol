<?php

defined('MOODLE_INTERNAL') || die;

if ($ADMIN->fulltree) {
    $settings->add(new admin_setting_configtext(
						'block_lbcontrol_host',
						get_string('host', 'block_lbcontrol'),
                   		get_string('host_addr', 'block_lbcontrol'),
						'',
						PARAM_RAW_TRIMMED
					));

    /*$settings->add(new admin_setting_configcheckbox(
						'block_lbcontrol_useadmin',
						get_string('useadmin', 'block_lbcontrol'),
                   		get_string('useadmin_detail', 'block_lbcontrol'),
						0
					));
*/

    $link1 ='<p><a href="'.$CFG->wwwroot.'/blocks/lbcontrol/managerooms.php">'.get_string('managerooms', 'block_lbcontrol').'</a></p>';
    $settings->add(new admin_setting_heading('block_lbcontrol_link1', '', $link1));

    $link2 ='<p><a href="'.$CFG->wwwroot.'/blocks/lbcontrol/report.php">'.get_string('view_report', 'block_lbcontrol').'</a></p>';
    $settings->add(new admin_setting_heading('block_lbcontrol_link2', '', $link2));

}

