<?php

	/**
	 * 访问用户主页
	 *
	 */
	class Space_Home_UserController extends Zend_Controller_Action 
	{	
		function init(){}
		
		function indexAction()
		{
			$uid = $this->_getParam('uid');
			$this->visitor($uid);
			$this->view->profile = Logic_User_Base::get($uid);
			$this->view->home = Logic_User_Privacy::getHome($uid);
			$row = Logic_Space_Home::get('guests', $uid);
			$this->view->guests = unserialize($row['guests']);
			$this->view->uid = $uid;
			$this->view->log = Logic_Log::home($uid);
			$this->view->event = Zend_Registry::get('config')->event_log->toArray();
		}
		
		function visitor($uid)
		{
			// 写入访问记录
			$myid = Cmd::uid();

			$visitor = Logic_Space_Home::get('guests', $uid);
			$visitor = unserialize($visitor['guests']);
			
			// 无数据则初始化
			if($visitor == false) $visitor = array();
			
			$size = 12; // 记录上限
			$flag = 0;
			
			if(count($visitor) > 0)
			foreach ($visitor as $i => $v)
			{
				if($v['uid'] == $myid)
				{
					unset($visitor[$i]);
					$flag = 1;
				}
			}
			
			// 新记录
			if($flag == 0)
			{
				// 超过上限则去掉最后一个数组
				if(count($visitor) == $size)
				array_pop($visitor);
			}
			array_unshift($visitor, 
						array('uid' => $myid, 
							  'username' => Cmd::getSess('profile', 'username'),
							  'time' => time(),
							  'sex' => Cmd::getSess('profile', 'sex')
						));
			
			// 更新数据库
			Logic_Space_Home::guests($visitor, $uid);
		}
	}

?>