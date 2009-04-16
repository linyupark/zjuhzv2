document.write("<script type=\"text/javascript\" src=\"/js/jq/jquery.js\"></script>");
document.write("<script type=\"text/javascript\" src=\"/js/jq/facebox.js\"></script>");
document.write("<script type=\"text/javascript\" src=\"/js/jq/scrolltopcontrol.js\"></script>");
//document.write("<script type=\"text/javascript\" src=\"/js/jq/jquery.fmsg.js\"></script>");
//document.write("<script type=\"text/javascript\" src=\"/js/jq/jquery.hint.js\"></script>");
function check_msgbox()
{
	$.getJSON('/api_json/msg/check',function(data){
		if(parseInt(data.result) > 0){ 
			$('#msgstate').attr('src', '/im/icon/1616/newmail.gif');
			newMsg(1);
		}
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
function oplink()
{
	$('.oplink a').css('opacity',0.7).hover(function(){$(this).css('opacity',1);}).mouseout(function(){$('.oplink a').css('opacity',0.7);})
}

var fmsg = null;
var g_blinkswitch = 0;
var g_blinktitle = document.title;
function startNewMsg()
{
	document.title = g_blinkswitch % 2==0 ? "【　　　】" + g_blinktitle : "【新消息】" + g_blinktitle;
	g_blinkswitch++;
}
function newMsg(flag)
{
	if(flag == 1)
	fmsg = setInterval(startNewMsg, 1000);
	else clearInterval(fmsg);
}

function setCookie(c_name,value,expiredays)
{
	var exdate=new Date();
	exdate.setDate(exdate.getDate()+expiredays);
	document.cookie=c_name+ "=" +escape(value)+((expiredays==null) ? "" : ";expires="+exdate);
}
