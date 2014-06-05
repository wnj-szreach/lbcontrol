<?php
class block_lbcontrol extends block_base{

    function init() {
        $this->title = get_string('pluginname', 'block_lbcontrol');
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

}


