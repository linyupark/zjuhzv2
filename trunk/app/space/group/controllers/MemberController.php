<?php

	/**
	 * 群组成员控制器
	 *
	 */
	class Space_Group_MemberController extends Zend_Controller_Action 
	{		
		function init()
		{
			$this->view->gid = $this->_getParam('id');
			$this->view->tab = $this->getRequest()->getControllerName();
		}
		
		function indexAction()
		{
			$uid = Cmd::uid();
			$gid = $this->view->gid;
			$group = Logic_Space_Group::info($gid);
			if(Logic_Space_Group::isAllowedVisit($gid, $uid))
			{	
				$this->view->group = $group;
			}
			else $this->_forward('deny', 'error', 'public', 
				array('position'=>'space_group_home','group'=>$group));
		}
		
		/**
		 * 加入群组
		 *
		 */
		function joinAction()
		{
			if($this->getRequest()->isXmlHttpRequest())
			{
				$uid = Cmd::uid();
				$gid = $this->_getParam('id');
				$group = Logic_Space_Group::info($gid);
				// 群组过滤
				if($group == false || $group['type'] == 'close')
				{ echo '你无法加入该群'; exit(); }
				// 成员过滤
				if(Logic_Space_Group_Member::isMemeber($gid, $uid) != false)
				{ echo '你已经是该群成员了'; exit(); }
				// 公开群直接进入
				if($group['type'] == 'open')
				{
					Logic_Space_Group_Member::join($gid, $uid);
					Logic_Space_Group_Member::crole($gid, $uid, 'member', time());
					echo '你成功加入该群';
				}
				else 
				{
					$role = Logic_Space_Group_Member::role($gid, $uid);
					if(!$role) // 可申请
					{
						Logic_Space_Group_Member::join($gid, $uid);
						
						echo '加入申请已发送，等待回应';
					}
				}
			}
		}
	}
?>