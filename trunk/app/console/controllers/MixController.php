<?php

	/**
	 * 杂项管理
	 *
	 */
	class Console_MixController extends Zend_Controller_Action
	{	
		function indexAction()
		{
			$this->view->tab = $this->_getParam('tab', 'player');
		}
		
		/**
		 * 内部文件上传、浏览
		 *
		 */
		function sysfileAction()
		{
			$params = $this->getRequest()->getParams();
			if($this->getRequest()->isXmlHttpRequest())
			{
				if($params['rmdir'] != null) // 删除目录下所有文件包括目录
				{
					$rmdir = UPLOADROOT.'system/'.$params['rmdir'];
					$files = Alp_Sys::lsfile($rmdir);
					if(count($files) > 0)
					{
						foreach ($files as $f)
						{
							@unlink($rmdir.'/'.$f);
						}
					}
					rmdir($rmdir);
				}
				elseif($params['rm'] != null) // 删除指定文件
				{
					$rm = UPLOADROOT.'system'.$params['rm'];
					@unlink($rm);
				}
				else 
				{
					$rows = Alp_Sys::lsfile(UPLOADROOT.'system'.$params['dir'], true);
					$this->view->rows = $rows;
					$this->view->dir = $params['dir'];
					$this->view->path = UPLOADROOT.'system'.$params['dir'];
					$this->render('sysfiletb');
				}
			}
		}
		
		/**
		 * 头版新闻编辑
		 *
		 */
		function ftpageAction()
		{
			$file = HTMLROOT.'/player/ftpage.html';
			if($this->getRequest()->isPost()) // 保存处理
			{
				$data = $this->getRequest()->getPost('html');
				file_put_contents($file, stripslashes($data));
			}
			$html = file_get_contents($file);
			$this->view->html = $html;
		}
		
		/**
		 * 首页播放管理
		 *
		 */
		function playerAction()
		{
			$file = HTMLROOT.'/player/bcastr.xml';
			if($this->getRequest()->isPost()) // 保存处理
			{
				$data = $this->getRequest()->getPost('xml');
				file_put_contents($file, stripslashes($data));
			}
			$xml = file_get_contents($file);
			$this->view->xml = $xml;
		}
	}
	
?>