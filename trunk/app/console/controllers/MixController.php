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
		
		function emailAction()
		{
			$this->view->addresses = Logic_Mail::address();
		}
		
		function sendmailAction()
		{
			set_time_limit(0);
			$this->getHelper('viewRenderer')->setNoRender(true);
	
			$params = $this->getRequest()->getParams();
			if(!$address = trim($params['address']))
			Alp_Sys::msg('mail_address', '请输入邮件地址');
			if(!$body = trim($params['content']))
			Alp_Sys::msg('mail_body', '请输入邮件内容');
			$subject = $params['subject'] ? $params['subject'] : '杭州浙江大学校友会邮件信息';
			if(Alp_Sys::getMsg() == null)
			{
				$address = explode(';', $address);
				$result = Logic_Mail::batch($subject, stripslashes($body), $address);
				if($result == true) echo '邮件发送成功';
				else foreach ($result as $mail) echo '* '.$mail.' 发送失败<br />';
			}
			else echo Alp_Sys::allMsg();
		}
		
		/**
		 * 友情链接，赞助企业
		 *
		 */
		function linksAction()
		{
			if($this->getRequest()->isXmlHttpRequest())
			{
				$this->getHelper('viewRenderer')->setNoRender();
				$params = $this->_getAllParams();
				switch ($params['t'])
				{
					case 'update' :
						$params = Filter_Mix::links($params);
						if(Alp_Sys::getMsg() == null)
						{
							Logic_Mix::updatelink($params);
							echo 'success';
							return ;
						}
						else echo Alp_Sys::allMsg('* ', "\n");
					break;
						
					case 'delete' :
						Logic_Mix::dellink($params['id']);
						echo 'success';
					break;
						
					default : 
						$params = Filter_Mix::links($params);
						if(Alp_Sys::getMsg() == null)
						{
							Logic_Mix::addlink($params);
							$params['result'] = 'success';
							echo Zend_Json::encode($params);
							return ;
						}
						else echo Zend_Json::encode(array('result' => Alp_Sys::allMsg('* ', "\n")));
					break;
				}
			}
			else 
			{
				$links = DbModel::getSqlite('mix.s3db')->fetchAll('SELECT * FROM `tb_links`');
				$this->view->links = $links;
			}
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