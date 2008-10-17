jQuery.flashMsg = function(type, content){
	
	// 建立显示容器
	if($('#flash_msg_container').length == 0)
	{
		$('body:first').prepend('<div id="flash_msg_container"><b id="flash_msg"></b></div>');
	}
	
	// 公共样式
	$('#flash_msg_container').css({position:'absolute', top:'0px', width:'100%', zIndex:1000, margin: '1px auto', textAlign:'center'});
	$('#flash_msg').css({margin:'0 auto', padding:'5px 20px', fontSize:'14px', letterSpacing:'1px'});
	
	// 类型确定
	if(type == 'success')
	{
		$('#flash_msg').css({background:'#EAFFA8', color:'#333', border:'2px solid #C8F44D'});
	}
	if(type == 'error')
	{
		$('#flash_msg').css({background:'#FFB0A8', color:'#333', border:'2px solid #FF8275'});
	}
	if(type == 'notice')
	{
		$('#flash_msg').css({background:'#FFF1A8', color:'#333', border:'2px solid #FFCC66'});
	}
	
	// 写入内容
	$('#flash_msg').html(content);
	
	// 淡出
	$('#flash_msg').fadeOut(5000, function(){ $('#flash_msg_container').remove(); });
	
}
