document.write("<script type=\"text/javascript\" src=\"/js/jq/jquery.js\"></script>");
document.write("<script type=\"text/javascript\" src=\"/js/jq/facebox.js\"></script>");
//document.write("<script type=\"text/javascript\" src=\"/js/jq/jquery.fmsg.js\"></script>");
//document.write("<script type=\"text/javascript\" src=\"/js/jq/jquery.hint.js\"></script>");
document.write("<script type=\"text/javascript\" src=\"/js/jq/nicejforms.js\"></script>");
function check_msgbox()
{
	$.getJSON('/api_json/msg/check',function(data){
		if(parseInt(data.result) > 0){ $('#msgstate').attr('src', '/im/icon/1616/newmail.gif'); }
		else $('#msgstate').attr('src', '/im/icon/1616/mail.gif');
		$('#json_msgbox').text(data.result);
	});
}
function online_state()
{
	$('.online_state').each(function(i){ var uid = $(this).attr('rel'); $.getJSON('/api_json/online/check?uid='+uid, function(data){ $('.online_state[rel="'+uid+'"]').attr('src','/im/icon/1616/'+data.state+'.gif').attr('title',data.state); }); });
}

function friend_add(uid)
{
	$.post('/space_friends/add/?uid='+uid, function(data){
		$.facebox(data);
	});
}
