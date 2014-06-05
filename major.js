// 刷新列表
function freshList() {
    var url = "../blocks/lbcontrol/proxy_list.php";
	$.ajax({
	  	type: 'GET',
	  	url: url,
	  	data: null,
	  	success: function(result){
			$('#roomDataList').html(result);
			
			// 显示查看文件连接
			if($('#roomDataList li').length > 0) {
				$('.view_file').show();
			}else {
				$('.view_file').hide();
			}
		}
	});
}

// 直播预览
function openlive(url) {
	window.open(url,"vod","height=600,width=800,left=200,top=100,fullscreen=no,toolbar=no,menubar=no,scrollbars=no,resizable=yes,location=no,status=no");
}

// 启动录制、暂停录制、继续录制、停止录制
function roomControl(rid, opt) {
    var url = "../blocks/lbcontrol/proxy_control.php?" + "&rid=" + rid + "&opt=" + opt;
	$.ajax({
	  	type: 'GET',
	  	url: url,
	  	data: null,
	  	success: function(result){
			if(result == 'success') {
				freshList();
			}else{
				alert(result);
			}
		}
	});
};


// 动态改变数据列表
$(document).ready(function(){
	if($('#roomDataList').length > 0) {// 只有该页面才发定时器
		// 首次获取数据
		freshList();
		// 定时器刷新数据
		setInterval('freshList()', 6000);
	}

});
