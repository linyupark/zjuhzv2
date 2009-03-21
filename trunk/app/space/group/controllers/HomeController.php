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
		 * 群热度显示(当前热度,前后2群排行，总排行)
		 *
		 */
		function pointAction()
		{
			
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
												->where('bar.group = ?', $gid)->where('bar.deny = 0')
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
		 * 消灭群组
		 *
		 */
		function delAction()
		{
			$this->getHelper('viewRenderer')->setNoRender();
			$gid = (int)$this->_getParam('gid');
			$uid = Cmd::uid();
			
			if(Logic_Space_Group_Member::isCreater($gid, $uid) != false || Cmd::role() == 'master')
			{
				$db = DbModel::Space();
				$db->delete('tb_group', 'gid = '.$gid); // 群组本体信息
				$db->delete('tb_group_member', 'gid = '.$gid); // 群组成员信息
				$db->delete('tb_tbar', '`group` = '.$gid); // 群组帖子信息
				echo 'success';
			}
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
				$select = DbModel::Space()->select()->from(array('gm' => 'tb_group_member'));
				$select->joinLeft(array('u' => 'zjuhzv2_user.tb_base'), 'gm.uid = u.uid', array('username','sex'));
				
				$select->where('gm.gid = '.$gid.' AND gm.role = "creater"');
				$rows = $select->query()->fetchAll();
				$this->view->creater = $rows[0]; $select->reset(Zend_Db_Select::WHERE);
				
				$select->where('gm.gid = '.$gid.' AND gm.role = "manager"');
				$rows = $select->query()->fetchAll();
				$this->view->manager = $rows; $select->reset(Zend_Db_Select::WHERE);
				
				$select->where('gm.gid = '.$gid.' AND gm.role = "member"')->order('gm.jointime DESC')->limit(3);
				$rows = $select->query()->fetchAll();
				$this->view->fresh = $rows; 
				
				$select->reset(Zend_Db_Select::ORDER)->order('gm.lastvisit DESC')->limit(12);; // 重置排序
				$this->view->visitor = $select->query()->fetchAll();
				
				$this->view->group = $group;
			}
			else $this->_forward('deny', 'error', 'public', 
				array('position'=>'space_group_home','group'=>$group));
		}
	}

?>