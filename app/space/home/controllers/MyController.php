<?php

	/**
	 * 个人主页
	 *
	 */
	class Space_Home_MyController extends Zend_Controller_Action 
	{
		function init()
		{
			$this->view->headTitle('我的个人主页');
		}
		
		function doingAction()
		{
			$uid = Cmd::uid();
			$this->getHelper('viewRenderer')->setNoRender();
			$ing = strip_tags(trim($this->_getParam('ing')));
			Logic_Space_Home::ing($ing, $uid);
			if(Alp_Sys::getMsg() == null)
			{
				Logic_Log::user(array(
					'uid' => $uid,
					'fid' => 0,
					'key' => 'mod_ing'
				));
			}
		}
		
		function indexAction()
		{
			$uid = Cmd::uid();
			$this->view->profile = Cmd::getSess('profile');
			$row = Logic_Space_Home::get('guests', $uid);
			$this->view->guests = unserialize($row['guests']);
			$this->view->home = Logic_User_Privacy::getHome($uid);
			$this->view->log = Logic_Log::home($uid);
			$this->view->event = Zend_Registry::get('config')->event_log->toArray();
		}
	}

?>