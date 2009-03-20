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
				// 成员显示
				$name = $this->_getParam('uname');
				$select = DbModel::Space()->select();
				$page = $this->_getParam('p', 1);
				$query = '';
				
				$select->from(array('gm' => 'tb_group_member'))
					   ->joinLeft(array('u' => 'zjuhzv2_user.tb_base'), 'u.uid = gm.uid', 
					   			  array('uname'=>'u.username', 'sex'=>'u.sex'));
				
				$select->where('gm.gid = ?', $gid)
					   ->where('gm.role IN ("manager","member","creater")');

				if($name != null)
				{
					$query = '&uname='.urlencode($name);
					$select->where('u.username LIKE "%'.$name.'%"');
					$this->view->uname = urldecode($name);
				}
				
				$row = $select->query()->fetchAll();
								
				$pagesize = 10;
				if(count($row) > $pagesize)
				{
					Alp_Page::$pagesize = $pagesize;
					Alp_Page::create(array(
						'href_open' => '<a href="/space_group/member/?id='.$gid.$query.'&p=%d">',
						'href_close' => '</a>',
						'num_rows' => count($row),
						'cur_page' => $page
					));
					$select->limit($pagesize, Alp_Page::$offset);
					$this->view->pagination = Alp_Page::$page_str;
				}
				$select->order('gm.jointime DESC');
				$this->view->rows = $select->query()->fetchAll();
			}
			else $this->_forward('deny', 'error', 'public', 
				array('position'=>'space_group_home','group'=>$group));
		}
		
		/**
		 * 转换角色
		 *
		 */
		function croleAction()
		{
			$this->getHelper('viewRenderer')->setNoRender();
			if($this->getRequest()->isXmlHttpRequest())
			{
				$uid = $this->_getParam('uid');
				$gid = $this->_getParam('gid');
				$role = $this->_getParam('role');
				Logic_Space_Group_Member::crole($gid, $uid, $role);
				echo 'success';
			}
		}
		
		/**
		 * 脱离群
		 *
		 */
		function leaveAction()
		{
			$this->getHelper('viewRenderer')->setNoRender();
			if($this->getRequest()->isXmlHttpRequest())
			{
				$uid = $this->_getParam('uid');
				$gid = $this->_getParam('id');
				Logic_Space_Group_Member::leave($gid, $uid);
				echo 'success';
			}
		}
		
		/**
		 * 批准加入
		 *
		 */
		function jpassAction()
		{
			$this->getHelper('viewRenderer')->setNoRender();
			if($this->getRequest()->isXmlHttpRequest())
			{
				$myid = Cmd::uid();
				$uid = $this->_getParam('uid');
				$gid = $this->_getParam('gid');
				if(Logic_Space_Group_Member::isMemeber($gid, $uid) == false)
				Logic_Space_Group_Member::crole($gid, $uid, 'member', time());
				// 记录
				Logic_Log::group(array(
					'uid' => $uid,
					'gid' => $gid,
					'key' => 'join_group',
				));
				Logic_Space_Msg::group($uid, $myid, $gid, '已经批准你的申请，赶快去群组看看吧~');
				echo 'success';
			}
		}
		
		function ipassAction()
		{
			$this->getHelper('viewRenderer')->setNoRender();
			if($this->getRequest()->isXmlHttpRequest())
			{
				$myid = Cmd::uid();
				$uid = $this->_getParam('uid');
				$gid = $this->_getParam('gid');
				if(Logic_Space_Group_Member::isMemeber($gid, $myid) == false)
				Logic_Space_Group_Member::crole($gid, $myid, 'member', time());
				Logic_Space_Msg::group($uid, $myid, $gid, '已经同意你的邀请，赶快去群组看看吧~');
				echo 'success';
			}
		}
		
		/**
		 * 邀请加入群
		 *
		 */
		function inviteAction()
		{
			$this->getHelper('viewRenderer')->setNoRender();
			$uid = Cmd::uid();
			$params = $this->_getAllParams();
			$params = Filter_Msg::group($params);
			if(Alp_Sys::getMsg() == null)
			{
				if($params['incept'] == '') // 从名字中获取id
				{
					$unames = explode(' ', $params['uname']);
					$incept = Logic_User_Base::uid($unames);
					
					$friends = Logic_Space_Friends::ids($uid);
					$fid = array();
					foreach ($friends as $f)
					{
						$fid[] = $f['friend'];
					}
					// 过滤非好友id
					foreach ($incept as $i => $u)
					{
						if(!in_array($u['uid'], $fid))
						unset($incept[$i]);
					}
				}
				else $incept = explode(',', $params['incept']);
				
				if(count($incept) > 0)
				{
					// 逐个发信
					foreach ($incept as $u)
					{
						$role = Logic_Space_Group_Member::role($params['gid'], $u['uid']);
						// 插入新角色数据
						if(!$role) Logic_Space_Group_Member::invite($params['gid'], $u['uid']);
						// 过滤群组成员
						if($role == 'creater' || $role == 'member' || $role == 'manager') continue;
						else
						{
							// 变换角色
							if($role == 'join') 
							Logic_Space_Group_Member::crole($params['gid'], $u['uid'], 'invite');
							// 没有重复邀请
							if(Logic_Space_Msg::unique('group', $uid, $u['uid']) == false)
							Logic_Space_Msg::group($u['uid'], $uid, $params['gid'], '邀请加入群');
						}
					}
					echo 'success';
				}
				else Alp_Sys::msg('incept', '无效的发送对象，请重新选择');
			}
			echo Alp_Sys::allMsg('* ', "\n");
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
					// 记录
					Logic_Log::group(array(
						'uid' => $uid,
						'gid' => $gid,
						'key' => 'join_group',
					));
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