<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<HTML xmlns="http://www.w3.org/1999/xhtml">
	<HEAD>
		<TITLE>多媒体编辑器</TITLE>
		<SCRIPT language=JAVASCRIPT src="editfunc.js"></SCRIPT>
		<SCRIPT language=JAVASCRIPT src="colorSelect.js"></SCRIPT>
		<SCRIPT language=JAVASCRIPT src="portraitSelect.js"></SCRIPT>

		<META http-equiv=Content-Type content="text/html; charset=gb2312">
<STYLE type=text/css>
A {
FONT-SIZE: 12px
}
IMG {
BORDER-TOP-WIDTH: 0px; BORDER-LEFT-WIDTH: 0px; BORDER-BOTTOM-WIDTH: 0px; BORDER-RIGHT-WIDTH: 0px
}
TD.icon {
VERTICAL-ALIGN: middle; WIDTH: 24px; HEIGHT: 24px; TEXT-ALIGN: center
}
TD.sp {
VERTICAL-ALIGN: middle; WIDTH: 8px; HEIGHT: 24px; TEXT-ALIGN: center
}
TD.xz {
VERTICAL-ALIGN: middle; WIDTH: 47px; HEIGHT: 24px; TEXT-ALIGN: center
}
TD.bq {
VERTICAL-ALIGN: middle; WIDTH: 49px; HEIGHT: 24px; TEXT-ALIGN: center
}
DIV A.n {
PADDING-RIGHT: 2px; DISPLAY: block; PADDING-LEFT: 2px; PADDING-BOTTOM: 2px; COLOR: #000000; LINE-HEIGHT: 16px; PADDING-TOP: 2px; HEIGHT: 16px; TEXT-DECORATION: none
}
DIV A.n:hover {
BACKGROUND: #e5e5e5
}
</STYLE>
<STYLE type=text/css>
.ico {
VERTICAL-ALIGN: middle; WIDTH: 24px; HEIGHT: 24px; TEXT-ALIGN: center
}
.ico2 {
VERTICAL-ALIGN: middle; WIDTH: 27px; HEIGHT: 24px; TEXT-ALIGN: center
}
.ico3 {
VERTICAL-ALIGN: middle; WIDTH: 25px; HEIGHT: 24px; TEXT-ALIGN: center
}
.ico4 {
VERTICAL-ALIGN: middle; WIDTH: 8px; HEIGHT: 24px; TEXT-ALIGN: center
}
BODY {
MARGIN: 0px
}
</STYLE>

<META content="MSHTML 6.00.2800.1106" name=GENERATOR>
</HEAD>
<BODY>
<TABLE style="BORDER-RIGHT: #c5c5c5 1px solid; BORDER-TOP: #c5c5c5 1px solid; BORDER-LEFT: #c5c5c5 1px solid; BORDER-BOTTOM: #c5c5c5 1px solid" cellSpacing=0 cellPadding=0 width="100%" background=images/bg.gif border=0>
<TBODY>
<TR>
<TD style="PADDING-LEFT: 5px" height=31>
<TABLE cellSpacing=0 cellPadding=0 border=0>
<TBODY>
<TR>
<TD class=ico><IMG onmousedown=fSetBorderMouseDown(this) onmouseover=fSetBorderMouseOver(this) title=剪切 onClick="format('Cut')" onmouseout=fSetBorderMouseOut(this) height=20 src="images/1.gif" width=20></TD>
<TD class=ico><IMG onmousedown=fSetBorderMouseDown(this) onmouseover=fSetBorderMouseOver(this) title=复制 onClick="format('Copy')" onmouseout=fSetBorderMouseOut(this) height=20 src="images/2.gif" width=20></TD>
<TD class=ico><IMG onmousedown=fSetBorderMouseDown(this) onmouseover=fSetBorderMouseOver(this) title=粘贴 onClick="format('Paste')" onmouseout=fSetBorderMouseOut(this)	height=20 src="images/3.gif" width=20></TD>
<TD class=ico><IMG height=20 src="images/line.gif" width=4></TD>
<TD class=ico2><IMG onmousedown=fSetBorderMouseDown(this) id=imgFontface onmouseover=fSetBorderMouseOver(this) title=字体 onClick="fGetEv(event);fDisplayElement('fontface','')" onmouseout=fSetBorderMouseOut(this) height=20 src="images/4.gif" width=25></TD>
<TD class=ico2><IMG onmousedown=fSetBorderMouseDown(this) id=imgFontsize onmouseover=fSetBorderMouseOver(this) title=字号 onClick="fGetEv(event);fDisplayElement('fontsize','')" onmouseout=fSetBorderMouseOut(this) height=20 src="images/5.gif" width=25></TD>
<TD class=ico><IMG onmousedown=fSetBorderMouseDown(this) onmouseover=fSetBorderMouseOver(this) title=加粗 onClick="format('Bold');" onmouseout=fSetBorderMouseOut(this) height=20 src="images/6.gif" width=20></TD>
<TD class=ico><IMG onmousedown=fSetBorderMouseDown(this) onmouseover=fSetBorderMouseOver(this) title=斜体 onClick="format('Italic');" onmouseout=fSetBorderMouseOut(this) height=20 src="images/7.gif" width=20></TD>
<TD class=ico><IMG onmousedown=fSetBorderMouseDown(this) onmouseover=fSetBorderMouseOver(this) title=下划线 onClick="format('Underline')" onmouseout=fSetBorderMouseOut(this) height=20 src="images/8.gif" width=20></TD>
<TD class=ico><IMG onmousedown=fSetBorderMouseDown(this) onmouseover=fSetBorderMouseOver(this) title=左对齐 onClick="format('Justifyleft')" onmouseout=fSetBorderMouseOut(this) height=20 src="images/9.gif" width=20></TD>
<TD class=ico><IMG onmousedown=fSetBorderMouseDown(this) onmouseover=fSetBorderMouseOver(this) title=中间对齐 onClick="format('Justifycenter')" onmouseout=fSetBorderMouseOut(this) height=20 src="images/10.gif" width=20></TD>
<TD class=ico><IMG onmousedown=fSetBorderMouseDown(this) onmouseover=fSetBorderMouseOver(this) title=右对齐 onClick="format('Justifyright')" onmouseout=fSetBorderMouseOut(this) height=20 src="images/11.gif" width=20></TD>
<TD class=ico><IMG onmousedown=fSetBorderMouseDown(this) onmouseover=fSetBorderMouseOver(this) title=数字编号 onClick="format('Insertorderedlist')" onmouseout=fSetBorderMouseOut(this) height=20 src="images/12.gif" width=20></TD>
<TD class=ico><IMG onmousedown=fSetBorderMouseDown(this) onmouseover=fSetBorderMouseOver(this) title=项目编号 onClick="format('Insertunorderedlist')" onmouseout=fSetBorderMouseOut(this) height=20 src="images/13.gif" width=20></TD>
<TD class=ico><IMG onmousedown=fSetBorderMouseDown(this) onmouseover=fSetBorderMouseOver(this) title=增加缩进 onClick="format('Outdent')" onmouseout=fSetBorderMouseOut(this) height=20 src="images/14.gif" width=20></TD>
<TD class=ico><IMG onmousedown=fSetBorderMouseDown(this) onmouseover=fSetBorderMouseOver(this) title=减少缩进 onClick="format('Indent')" onmouseout=fSetBorderMouseOut(this) height=20 src="images/15.gif" width=20></TD>
<TD class=ico><IMG height=20 src="line.gif" width=4></TD>
<TD class=ico3><IMG onmousedown=fSetBorderMouseDown(this) onmouseover=fSetBorderMouseOver(this) title=字体颜色 onclick=foreColor(event) onmouseout=fSetBorderMouseOut(this) height=20 src="images/16.gif" width=23></TD>
<TD class=ico2><IMG onmousedown=fSetBorderMouseDown(this) onmouseover=fSetBorderMouseOver(this) title=背景颜色 onclick=backColor(event) onmouseout=fSetBorderMouseOut(this) height=20 src="images/17.gif" width=24></TD>
<TD class=ico><IMG onmousedown=fSetBorderMouseDown(this) onmouseover=fSetBorderMouseOver(this) title=增加链接 onclick=createLink() onmouseout=fSetBorderMouseOut(this) height=20 src="images/18.gif" width=21></TD>
<TD class=ico><IMG onmousedown=fSetBorderMouseDown(this) onmouseover=fSetBorderMouseOver(this) title=增加图片 onclick=createImg() onmouseout=fSetBorderMouseOut(this) height=20 src="images/19.gif" width=21></TD>
<TD class=ico3><IMG onmousedown=fSetBorderMouseDown(this) onmouseover=fSetBorderMouseOver(this) title=魔法表情 onclick=addPortrait(event) onmouseout=fSetBorderMouseOut(this) height=20 src="images/20.gif" width=23></TD>
<TD class=ico4><IMG height=20 src="images/line.gif" width=4></TD>
<TD style="FONT-SIZE: 12px"><INPUT language=javascript onmouseover=fSetModeTip(this) onclick=setMode(this.checked) onmouseout=fHideTip() type=checkbox name=switchMode></TD>
</TR>
</TBODY>
</TABLE>
</TD>
</TR>
</TBODY>
</TABLE>
<DIV id=dvForeColor style="DISPLAY: none; LEFT: -500px; WIDTH: 100px; POSITION: absolute; TOP: -500px; HEIGHT: 100px">
<TABLE style="BORDER-RIGHT: #888888 1px solid; BORDER-TOP: #888888 1px solid; BORDER-LEFT: #888888 1px solid; BORDER-BOTTOM: #888888 1px solid" height=25 cellSpacing=0 cellPadding=0 width=218>
<TBODY>
<TR>
<TD id=tdView width=110>&nbsp;</TD>
<TD id=tdColorCode align=middle bgColor=#ffffff></TD>
</TR>
</TBODY>
</TABLE>
</DIV>
<DIV id=dvPortrait style="DISPLAY: none; LEFT: -500px; WIDTH: 100px; POSITION: absolute; TOP: -500px; HEIGHT: 100px"></DIV>
<DIV id=fontface style="BORDER-RIGHT: #838383 1px solid; PADDING-RIGHT: 1px; BORDER-TOP: #838383 1px solid; DISPLAY: none; PADDING-LEFT: 1px; Z-INDEX: 99; BACKGROUND: #ffffff; LEFT: 2px; PADDING-BOTTOM: 1px; BORDER-LEFT: #838383 1px solid; WIDTH: 110px; PADDING-TOP: 1px; BORDER-BOTTOM: #838383 1px solid; POSITION: absolute; TOP: 35px; HEIGHT: 270px"><A class=n style="FONT: 12px '宋体'" onClick="format('fontname',this.innerHTML);this.parentNode.style.display='none'" href="javascript:void(0)">宋体</A><A class=n style="FONT: 12px '黑体'" onClick="format('fontname',this.innerHTML);this.parentNode.style.display='none'" href="javascript:void(0)">黑体</A><A class=n style="FONT: 12px '楷体'" onClick="format('fontname',this.innerHTML);this.parentNode.style.display='none'" href="javascript:void(0)">楷体</A><A class=n style="FONT: 12px '隶书'" onClick="format('fontname',this.innerHTML);this.parentNode.style.display='none'" href="javascript:void(0)">隶书</A><A class=n style="FONT: 12px '幼圆'" onClick="format('fontname',this.innerHTML);this.parentNode.style.display='none'" href="###">幼圆</A><A class=n style="FONT: 12px Arial" onClick="format('fontname',this.innerHTML);this.parentNode.style.display='none'" href="###">Arial</A><A class=n style="FONT: 12px 'Arial Narrow'" onClick="format('fontname',this.innerHTML);this.parentNode.style.display='none'" href="###">Arial Narrow</A><A class=n style="FONT: 12px 'Arial Black'" onClick="format('fontname',this.innerHTML);this.parentNode.style.display='none'" href="###">Arial Black</A><A class=n style="FONT: 12px 'Comic Sans MS'" onClick="format('fontname',this.innerHTML);this.parentNode.style.display='none'" href="###">Comic Sans MS</A><A class=n style="FONT: 12px Courier" onClick="format('fontname',this.innerHTML);this.parentNode.style.display='none'" href="###">Courier</A><A class=n style="FONT: 12px System" onClick="format('fontname',this.innerHTML);this.parentNode.style.display='none'" href="###">System</A><A class=n style="FONT: 12px 'Times New Roman'" onClick="format('fontname',this.innerHTML);this.parentNode.style.display='none'" href="javascript:void(0)">Times New Roman</A><A class=n style="FONT: 12px Verdana" onClick="format('fontname',this.innerHTML);this.parentNode.style.display='none'" href="javascript:void(0)">Verdana</A></DIV>
		<DIV id=fontsize style="BORDER-RIGHT: #838383 1px solid; PADDING-RIGHT: 1px; BORDER-TOP: #838383 1px solid; DISPLAY: none; PADDING-LEFT: 1px; BACKGROUND: #ffffff; LEFT: 26px; PADDING-BOTTOM: 1px; BORDER-LEFT: #838383 1px solid; WIDTH: 115px; PADDING-TOP: 1px; BORDER-BOTTOM: #838383 1px solid; POSITION: absolute; TOP: 35px; HEIGHT: 160px"><A class=n style="FONT-SIZE: xx-small; LINE-HEIGHT: 120%" onClick="format('fontsize',1);this.parentNode.style.display='none'" href="javascript:void(0)">极小</A><A class=n style="FONT-SIZE: x-small; LINE-HEIGHT: 120%" onClick="format('fontsize',2);this.parentNode.style.display='none'" href="javascript:void(0)">特小</A><A class=n style="FONT-SIZE: small; LINE-HEIGHT: 120%" onClick="format('fontsize',3);this.parentNode.style.display='none'" href="javascript:void(0)">小</A><A class=n style="FONT-SIZE: medium; LINE-HEIGHT: 120%" onClick="format('fontsize',4);this.parentNode.style.display='none'" href="##">中</A><A class=n style="FONT-SIZE: large; LINE-HEIGHT: 120%" onClick="format('fontsize',5);this.parentNode.style.display='none'" href="javascript:void(0)">大</A><A class=n style="FONT-SIZE: x-large; LINE-HEIGHT: 120%" onClick="format('fontsize',6);this.parentNode.style.display='none'" href="javascript:void(0)">特大</A><A class=n style="FONT-SIZE: xx-large; LINE-HEIGHT: 140%" onClick="format('fontsize',7);this.parentNode.style.display='none'" href="javascript:void(0)">极大</A></DIV>
<DIV id=divEditor>
<SCRIPT language=JavaScript>
<!--
if(document.all){
document.write('<table width="<?php echo $_GET['w'] ?>" height:<?php echo $_GET['h'] ?> border="0" cellspacing="0" cellpadding="0" ><tr><td style="border:1px solid #C5C5C5; border-top:0;"><IFRAME class="HtmlEditor" ID="HtmlEditor" name="HtmlEditor" style="height:<?php echo $_GET['h'] ?>;width:<?php echo $_GET['w'] ?>" frameBorder="0" marginHeight=0 marginWidth=0 src="blankpage.htm"></IFRAME></td></tr></table>');
}else{
document.write('<table width="<?php echo $_GET['w'] ?>" height:<?php echo $_GET['h'] ?> border="0" cellspacing="0" cellpadding="0" ><tr><td style="border:1px solid #C5C5C5; border-top:0;background-color:#ffffff"><IFRAME class="HtmlEditor" ID="HtmlEditor" name="HtmlEditor" style="height:<?php echo $_GET['h'] ?>;width:<?php echo $_GET['w'] ?>;margin-left:1px;margin-bottom:1px;" frameBorder="0" marginHeight=0 marginWidth=0 src="blankpage.htm"></IFRAME></td></tr></table>');
}
//-->
</SCRIPT>
</DIV>
<SCRIPT language=JavaScript>
<!--
if(document.all){
document.write('<textarea ID="sourceEditor" style="height:280px;width:100%;display:none">');
}else{
document.write('<textarea ID="sourceEditor" style="height:282px;width:100%;display:none">');
}
//-->
</SCRIPT>
	</BODY>
</HTML>