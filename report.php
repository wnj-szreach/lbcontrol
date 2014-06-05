<?php
	/*
	* mediacenter report
	*/
	require_once(dirname(__FILE__) . '/../../config.php');
	require_once('mclib.php');

	require_login();

	$context = get_context();
	$PAGE->set_context($context);

	require_capability('block/lbcontrol:addinstance', $context);

	$PAGE->set_url('/blocks/lbcontrol/report.php');
	$PAGE->navbar->add(get_string('navigate_0', 'block_lbcontrol'));
	$PAGE->navbar->add(get_string('navigate_1', 'block_lbcontrol'));
	$PAGE->navbar->add(get_string('navigate_2', 'block_lbcontrol'));
	$PAGE->navbar->add(get_string('navigate_3', 'block_lbcontrol'));
	$PAGE->navbar->add(get_string('navigate_report', 'block_lbcontrol'));
	$PAGE->set_title(get_string('report_title', 'block_lbcontrol'));
	$PAGE->set_pagelayout('admin');

	$PAGE->requires->css('/blocks/lbcontrol/vertical-bar.css');
	echo $OUTPUT->header();
    echo $OUTPUT->heading(get_string('report_title', 'block_lbcontrol'));

	$PAGE->requires->js('/blocks/lbcontrol/jquery.min.js');
	$PAGE->requires->js('/blocks/lbcontrol/jquery.verticalchart.js');

	// 用来存储统计结果
	$total_list = array();
	$pre2 = date('Y-m', strtotime("-2 month"));
	$pre1 = date('Y-m', strtotime("-1 month"));
	$pre0 = date('Y-m', strtotime("-0 month"));
	$total_list[$pre2] = 0;
	$total_list[$pre1] = 0;
	$total_list[$pre0] = 0;

	$params = array(
		'PageSize'=>128 
		,'PageNum'=>1
		,'Type'=>160
		,'Keyword'=>get_string('file_live','block_lbcontrol')
		,'BeginDate'=>date('Y-m', strtotime("-2 month")).'-01'
	);
	$result = mediacenter_request(10301, $params);
	
	if($result != null) {
		$index = 0;

		foreach($result->Logs->Log as $l) {
			$index = $index + 1;

			if(strpos($l->Time, $pre2) === 0) {
				$total_list[$pre2] = $total_list[$pre2] + 1;
			}else if(strpos($l->Time, $pre1) === 0) {
				$total_list[$pre1] = $total_list[$pre1] + 1;
			}else if(strpos($l->Time, $pre0) === 0) {
				$total_list[$pre0] = $total_list[$pre0] + 1;
			}
		}
	}

	$total_max = max($total_list[$pre0], $total_list[$pre1]);
	$total_max = max($total_list[$pre2], $total_max);
?>

<div style="width:100%;"><div id="verticalbar-chart-wrapper" style="margin:0 0 0 25px; width:50%;"></div></div>
<script type="text/javascript">
window.onload = function() {
	var total_max = Math.ceil(<?php echo $total_max; ?> * 1.2);
	var total_len = total_max.toString().length;
	var Hi = Math.pow(10, total_len);
	
	$('#verticalbar-chart-wrapper').verticalchart({
	  XData : ['<?php echo $pre2; ?>','<?php echo $pre1; ?>','<?php echo $pre0; ?>'],
	  YData : [0, Hi/10, Hi/10*2, Hi/10*3, Hi/10*4, Hi/10*5, Hi/10*6, Hi/10*7, Hi/10*8, Hi/10*9, Hi],
	  barA :  [<?php echo $total_list[$pre2]; ?>,<?php echo $total_list[$pre1]; ?>,<?php echo $total_list[$pre0]; ?>]
	 });
	$('#verticalbar-chart-wrapper').css({'width':'90%'});
}
</script>

<?php
	echo '<div><a href="proxy_mcbackstage.php?type=menu_system&menu=menu_systemLog" target="_blank">';
	echo get_string('log_more', 'block_lbcontrol');
	echo '</a></div>';
	echo $OUTPUT->footer();

?>
