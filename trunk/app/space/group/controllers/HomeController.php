<?php

	/**
	 * 群组主页控制器
	 *
	 */
	class Space_Group_HomeController extends Zend_Controller_Action 
	{		
		function init()
		{
			$this->view->gid = $this->_getParam('id');
			$this->view->tab = $this->getRequest()->getControllerName();
		}
		
		/**
		 * 群组工具条
		 *
		 */
		function toolbarAction()
		{
			$this->view->role = Logic_Space_Group_Member::role($this->view->gid, Cmd::uid());
			$this->view->type = $this->_getParam('type');
		}
		
		/**
		 * 群组基本信息栏
		 *
		 */
		function infoAction()
		{
			$this->view->group = $this->_getParam('group');
		}
		
		/**
		 * 帖子一览
		 *
		 */
		function topicAction()
		{
			$this->view->icons = Zend_Registry::get('config')->bar_icon->toArray();
			
			$gid = $this->view->gid;
			$select = DbModel::Space()->select()->from(array('bar' => 'zjuhzv2_space.tb_tbar'))
												->where('`group` = ?', $gid)
												->order('ding DESC')->order('bar.replytime DESC');
			$select->joinLeft(array('puser' => 'zjuhzv2_user.tb_base'), 'puser.uid = bar.puber', 
							  array('pubname' => 'username', 'pubnick' => 'nickname'));
			$select->joinLeft(array('ruser' => 'zjuhzv2_user.tb_base'), 'ruser.uid = bar.replyer', 
							  array('replyname' => 'username', 'replynick' => 'nickname'));
			$select->limit(20);
			$rows = $select->query()->fetchAll();
			$this->view->rows = $rows;
		}
		
		/**
		 * 指定id的群组首页
		 *
		 */
		function indexAction()
		{
			$uid = Cmd::uid();
			$gid = $this->view->gid;
			$group = Logic_Space_Group::info($gid);
			if(Logic_Space_Group::isAllowedVisit($gid, $uid))
			{
				Logic_Space_Group::visit($gid, $uid); // 更新最后访问时间
				$select = DbModel::Space()->select()->from(array('gm' => 'tb_group_member'), array('uid','role'));
				$select->joinLeft(array('u' => 'zjuhzv2_user.tb_base'), 'gm.uid = u.uid', array('username'))
					   ->where('gm.role != "join" AND gm.role != "invite"')
					   ->order('gm.jointime DESC')->limit(12);
				$rows = $select->query()->fetchAll();
				$manager = array(); $fresh = array();
				foreach ($rows as $user)
				{
					if($user['role'] == 'creater')
					$this->view->creater = $user;
					if($user['role'] == 'manager')
					$manager[] = $user;
					if($user['role'] == 'member')
					$fresh[] = $user;
				}
				$this->view->manager = $manager;
				$this->view->fresh = $fresh;
				
				$select->reset(Zend_Db_Select::ORDER)->order('gm.lastvisit DESC'); // 重置排序
				$this->view->visitor = $select->query()->fetchAll();
				
				$this->view->group = $group;
			}
			else $this->_forward('deny', 'error', 'public', 
				array('position'=>'space_group_home','group'=>$group));
		}
	}

?>