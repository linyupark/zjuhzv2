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
		 * 批准加入
		 *
		 */
		function passAction()
		{
			$this->getHelper('viewRenderer')->setNoRender();
			if($this->getRequest()->isXmlHttpRequest())
			{
				$myid = Cmd::uid();
				$uid = $this->_getParam('uid');
				$gid = $this->_getParam('gid');
				if(Logic_Space_Group_Member::isMemeber($gid, $uid) == false)
				Logic_Space_Group_Member::crole($gid, $uid, 'member', time());
				Logic_Space_Msg::group($uid, $myid, $gid, '已经批准你的申请，赶快去群组看看吧~');
				echo 'success';
			}
		}
		
		/**
		 * 加入群组
		 *
		 */
		function joinAction()
		{
			$this->getHelper('viewRenderer')->setNoRender();
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
					echo Alp_Sys::jump('/space_group/home/?id='.$gid, 1);
				}
				else 
				{
					$role = Logic_Space_Group_Member::role($gid, $uid);
					if(!$role) // 可申请
					{
						$incept = Logic_Space_Group_Member::all($gid, 'creater');
						$incept = $incept + Logic_Space_Group_Member::all($gid, 'manager');
						
						Logic_Space_Group_Member::join($gid, $uid);
						Logic_Space_Msg::joinGroup($incept, $uid, $gid);
						if(Alp_Sys::getMsg() == null)
						echo '加入申请已发送，等待回应';
						else echo Alp_Sys::allMsg();
					}
					if($role == 'invit')
					{
						echo '你已被邀请，请到<a href="/space_msg/?type=group">群组信息</a>查看';
					}
					if($role == 'join')
					{
						echo '已经发出申请，请耐心等待处理';
					}
				}
			}
		}
	}
?>