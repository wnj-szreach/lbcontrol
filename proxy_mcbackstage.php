<?php
    /*
    * proxy_live.php
    */
	require_once(dirname(__FILE__) . '/../../config.php');
	require_once($CFG->dirroot.'/my/lib.php');
	require_once('mclib.php');
	
	// 校验登录
	require_login();

	$type = required_param('type', PARAM_TEXT);
	$menu = required_param('menu', PARAM_TEXT);

	$params = array(
		'Type'=>2, // 2为令牌方式登录
		'UserName'=>($USER->username), 
		'ClientIp'=>($_SERVER['REMOTE_ADDR']), 
		'Content'=>''
	);
	$result = mediacenter_request(10401, $params);// 请求媒体中心返回动态令牌

	if($result == null) {
		exit;
	}
	$sign = $result->Sign;

	// 使用sign跳转到媒体后台
	$mclogin_url = get_mc_host().'/backstage/Login.action?cmd=17006&username='.($USER->username).'&sign='.$sign.'&userOption=1';
	$mcback_url = get_mc_host().'/backstage/index.jsp?';
	if(!empty($type)) {
		$mcback_url .= 'type='.$type;
		if(!empty($menu)) {
			$mcback_url .= '&menu='.$menu;
		}
	}

?>

<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<script type="text/javascript" src="jquery.min.js"></script>
	<script type="text/javascript">
		$(document).ready(function(){
			jQuery.ajax({
		    	url:"<?php echo $mclogin_url; ?>",
				dataType:"jsonp",
				jsonp:"callback",
				timeout:5000,
				success:function(data) {
					if(data != '') {
						$('body').html(data);
					}else {
						window.location.href = "<?php echo $mcback_url; ?>";
					}
				}
			});
		});
	</script>
</head>
<body>
</body>
</html>
