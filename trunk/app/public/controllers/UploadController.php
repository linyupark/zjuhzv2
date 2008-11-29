<?php

	class UploadController extends Zend_Controller_Action 
	{
		function init()
		{
			Zend_Layout::getMvcInstance()->disableLayout();
			$this->getHelper('viewRenderer')->setNoRender();
		}
		
		/**
		 * 头像上传
		 *
		 */
		function indexAction()
		{
			$name = $this->getRequest()->getParam('name');
			$path = UPLOADROOT.'/users/head/'.Cmd::uid();
			if(!file_exists($path)) mkdir($path, 0777);
			Alp_Upload::init(array(
				'maxsize' => 2000,
				'filename' => array('80'),
				'overwrite' => true,
				'path' => $path.'/'
			));
			if(!Alp_Upload::handle($name))
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
	}

?>