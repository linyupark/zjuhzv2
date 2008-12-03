<?php

	class UploadController extends Zend_Controller_Action 
	{
		function init()
		{
			if(Cmd::role() == 'guest') exit();
			Zend_Layout::getMvcInstance()->disableLayout();
			$this->getHelper('viewRenderer')->setNoRender();
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
		 * 图片上传
		 *
		 */
		function picAction()
		{
			if($_FILES)
			{
				$path = UPLOADROOT.'/users/pic/'.Cmd::uid();
				if(!file_exists($path)) mkdir($path, 0777);
				$newname = Alp_Ext_Str2Pinyin::str2pinyin($_FILES['pic']['name'][0]);
				Alp_Upload::init(array(
					'maxsize' => 2000,
					'filename' => array($newname),
					'overwrite' => true,
					'path' => $path.'/'
				));
				if(!Alp_Upload::handle('pic'))
				{
					echo "<script>alert('".Alp_Sys::allMsg('',"")."')</script>";
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