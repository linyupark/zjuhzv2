<?php

	/**
	 *  附件组件 - 活动在线订票系统
	 *
	 */
	class Addon_Console_BookingController extends Zend_Controller_Action 
	{	
		function init()
		{
			$this->view->tab = $this->_getParam('tab', 'create');
		}
		
		function indexAction()
		{
			$this->view->headTitle('校友会活动在线订票系统');
			$pid = $this->_getParam('pid');
			if($this->view->tab == 'modify' && !$pid)
			{
				// 显示活动列表
				$this->view->all_party = Logic_Addon_Booking::allParty();
			}
			if($pid)
			{
				$this->view->party = Logic_Addon_Booking::getParty($pid);
				$this->view->stations = Logic_Addon_Booking::getStations($pid);
			}
			$this->view->pid = $pid;
		}
		
		function modifyAction()
		{
			if($this->getRequest()->isXmlHttpRequest())
			{
				$this->getHelper('viewRenderer')->setNoRender();
				$params = $this->_getAllParams();
				$params = Filter_Addon::booking($params);
				if(Alp_Sys::getMsg() == null)
				{
					Logic_Addon_Booking::modify($params, $this->_getParam('id'));
					if(Alp_Sys::getMsg() == null)
					{
						echo 'success';
						exit();
					}
				}
				echo Alp_Sys::allMsg('* ', "\n");
			}
		}
		
		/**
		 * 执行建立活动
		 *
		 */
		function createAction()
		{
			if($this->getRequest()->isXmlHttpRequest())
			{
				$this->getHelper('viewRenderer')->setNoRender();
				$params = $this->_getAllParams();
				$params = Filter_Addon::booking($params);
				if(Alp_Sys::getMsg() == null)
				{
					Logic_Addon_Booking::insert($params);
					if(Alp_Sys::getMsg() == null)
					{
						echo 'success';
						exit();
					}
				}
				echo  Alp_Sys::allMsg('* ', "\n");
			}
		}
		
		/**
		 * 删除活动记录
		 *
		 */
		function delAction()
		{
			if($this->getRequest()->isXmlHttpRequest())
			{
				$this->getHelper('viewRenderer')->setNoRender();
				Logic_Addon_Booking::del($this->_getParam('id'));
				if(Alp_Sys::getMsg() == null)
				echo 'success';
				else echo Alp_Sys::allMsg('* ',"\n");
			}
		}
		
		function uploadformAction()
		{
		}
		
		function uploadAction()
		{
			$this->getHelper('viewRenderer')->setNoRender();
			if($_FILES)
			{
				$path = UPLOADROOT.'/addon/booking';
				Alp_Upload::init(array(
					'maxsize' => 2000,
					'random' => true,
					'path' => $path.'/'
				));
				if(!Alp_Upload::handle('pic'))
				{
					echo "<script>alert('".Alp_Sys::allMsg('',"")."');parent.insert_html(' ')</script>";
				}
				else
				{
					$file = Alp_Upload::$filename[0];
					// 图片处理
					$im = Alp_Image::init($path.'/'.$file);
					$width = $im->width;
					$height = $im->height;
					if($width > 800)
					{	
						$h = $height*(800/$width);
						$im->resize(Alp_String::stripFileExt($file), 800, $h, Alp_String::stripFile($file));
					}
					$url = Alp_Url::upload('/addon/booking/'.$file);
					echo '<script>parent.insert_html("<img src=\''.$url.'\' />")</script>';
				}
			}
		}
	}

?>