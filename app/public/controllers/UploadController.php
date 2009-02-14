<?php

	class UploadController extends Zend_Controller_Action 
	{
		function init()
		{
			if(Cmd::role() == 'guest') exit();
			//Zend_Layout::getMvcInstance()->disableLayout();
			$this->getHelper('viewRenderer')->setNoRender();
		}
		
		/**
		 * 群组logo上传
		 *
		 */
		function iconAction()
		{
			$path = UPLOADROOT.'/groups/'.$this->_getParam('gid');
			if(!file_exists($path)) mkdir($path, 0777);
			Alp_Upload::init(array(
				'maxsize' => 2000,
				'filename' => array('80'),
				'overwrite' => true,
				'path' => $path.'/'
			));
			if(!Alp_Upload::handle('icon'))
			{
				echo "<script>alert('".Alp_Sys::allMsg('',"")."')</script>";
			}
			else // 图像处理
			{
				Alp_Image::init($path.'/80')->output('80', 'gif', null, null, true);
				Alp_Image::init($path.'/80.gif')->resize('80', 80, 80);
				Alp_Image::init($path.'/80.gif')->resize('40', 40, 40);
				echo "<script>parent.ref()</script>";
			}
		}
		
		/**
		 * 头像上传
		 *
		 */
		function headAction()
		{
			$path = UPLOADROOT.'/users/head/'.Cmd::uid();
			if(!file_exists($path)) mkdir($path, 0777);
			Alp_Upload::init(array(
				'maxsize' => 2000,
				'filename' => array('80'),
				'overwrite' => true,
				'path' => $path.'/'
			));
			if(!Alp_Upload::handle('head'))
			{
				echo "<script>alert('".Alp_Sys::allMsg('',"")."')</script>";
			}
			else // 图像处理
			{
				Alp_Image::init($path.'/80')->output('80', 'gif', null, null, true);
				Alp_Image::init($path.'/80.gif')->resize('80', 80, 80);
				Alp_Image::init($path.'/80.gif')->resize('40', 40, 40);
				echo "<script>parent.ref()</script>";
			}
		}
		
		/**
		 * 文件上传
		 *
		 */
		function fileAction()
		{
			if($_FILES)
			{
				$path = UPLOADROOT.'/share/'.Cmd::uid();
				if(!file_exists($path)) mkdir($path, 0777);
				Alp_Upload::init(array(
					'type' => 'rar|zip|doc|xls|ppt|7z|mp3',
					'maxsize' => 8000,
					'random' => true,
					'path' => $path.'/'
				));
				if(!Alp_Upload::handle('file'))
				{
					echo "<script>alert('".Alp_Sys::allMsg('',"")."');parent.upreload();</script>";
				}
				else
				{
					// 上传后的处理
					echo '<script>parent.create_item('.Cmd::uid().',"'.Alp_Upload::$filename[0].'");parent.upreload();</script>';
				}
			}
		}
		
		/**
		 * 图片上传
		 *
		 */
		function picAction()
		{
			if($_FILES)
			{
				$path = UPLOADROOT.'/users/pic/'.Cmd::uid();
				if(!file_exists($path)) mkdir($path, 0777);
				$newname = strtolower(Alp_Ext_Str2Pinyin::str2pinyin($_FILES['pic']['name'][0]));
				Alp_Upload::init(array(
					'maxsize' => 2000,
					'filename' => array($newname),
					'overwrite' => true,
					'path' => $path.'/'
				));
				if(!Alp_Upload::handle('pic'))
				{
					echo "<script>alert('".Alp_Sys::allMsg('',"")."');parent.upreload();</script>";
				}
				else
				{
					// 图片处理
					$im = Alp_Image::init($path.'/'.$newname);
					$width = $im->width;
					$height = $im->height;
					if($width > 600)
					{	
						$h = $height*(600/$width);
						$im->resize(Alp_String::stripFileExt($newname), 600, $h, Alp_String::stripFile($newname));
					}
					$url = Alp_Url::upload('users/pic/'.Cmd::uid().'/'.$newname);
					echo '<script>parent.insert_html("<img src=\''.$url.'\' />")</script>';
				}
			}
		}
		
		function photoAction()
		{
			if($_FILES)
			{
				$path = UPLOADROOT.'/photo/'.Cmd::uid();
				if(!file_exists($path)) mkdir($path, 0777);
				$newname = strtolower(Alp_Ext_Str2Pinyin::str2pinyin($_FILES['photo']['name'][0]));
				Alp_Upload::init(array(
					'filename' => array($newname),
					'maxsize' => 5000,
					'path' => $path.'/'
				));
				if(!Alp_Upload::handle('photo'))
				{
					echo "<script>alert('".Alp_Sys::allMsg('',"")."');parent.upreload();</script>";
				}
				else
				{
					// 图片处理
					$im = Alp_Image::init($path.'/'.$newname);
					$width = $im->width;
					$height = $im->height;
					if($width > 600)
					{	
						$h = $height*(600/$width);
						$im->resize(Alp_String::stripFileExt($newname).'_resize', 600, $h, Alp_String::stripFile($newname));
					}
					echo '<script>parent.create_item('.Cmd::uid().',"'.Alp_Upload::$filename[0].'");parent.upreload();</script>';
				}
			}
		}
		
		function delshareAction()
		{
			$uid = $this->_getParam('uid', Cmd::uid());
			$path = UPLOADROOT.'/share/'.$uid;
			$file = $path.'/'.$this->_getParam('file');
			if(unlink($file)) echo 'success';
		}
		function delphotoAction()
		{
			$arg = $this->_getParam('file');
			$uid = $this->_getParam('uid', Cmd::uid());
			$path = UPLOADROOT.'/photo/'.$uid;
			$file = $path.'/'.$arg;
			$resize = $path.'/'.Alp_String::appendFileName($arg, '_resize');
			if(file_exists($resize)) @unlink($resize);
			if(unlink($file)) echo 'success';
		}
		
		/**
		 * 上传表单显示
		 *
		 */
		function formAction()
		{
			$tpl = $this->getRequest()->getParam('for');
			$this->render($tpl.'form');
		}
	}

?>