<?php
	
	/**
	 * 群成员邀请控制器
	 *
	 */
	class Space_Group_InviteController extends Zend_Controller_Action 
	{
		function init()
		{
			$gid = $this->_getParam('gid');
			$this->view->gid = $gid;
			// 群组类型获取
			$this->group = Logic_Space_Group::info($gid);
		}
		
		function error()
		{
			$this->_forward('deny', 'error', 'public', array('position' => 'space_group_home'));
		}
		
		/**
		 * 邀请发出页
		 *
		 */
		function indexAction()
		{
			if($this->group == false)
				$this->error();
			else 
			{
				// 如是私密群则只有管理员跟创建者可以发邀请
				$uid = Cmd::uid();
				$role = Logic_Space_Group_Member::role($this->group['gid'], $uid);
				if($this->group['type'] == 'close' && $role != 'creater' && $role != 'manager')
				$this->error();
				
				$this->view->group = $this->group;
				$this->view->sorts = Logic_Space_Friends::getSort(Cmd::uid());
			}
		}
	}

?>