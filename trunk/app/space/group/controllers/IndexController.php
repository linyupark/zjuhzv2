<?php

	/**
	 * 群组俱乐部默认控制器
	 *
	 */
	class Space_Group_IndexController extends Zend_Controller_Action 
	{
		function init()
		{
			$this->view->list = $this->_getParam('list', 'my');
			$this->view->icons = Zend_Registry::get('config')->bar_icon->toArray();
		}
		
		/**
		 * 我的群组列表
		 *
		 */
		function indexAction()
		{
			// 边栏
			$this->view->fresh = Logic_Space_Group::fresh(3); // 新群
		}
		
		// 我的群组
		function myAction()
		{
			// 群组信息
			$uid = Cmd::uid();
			$select = DbModel::Space()->select();
			$select->from(array('m' => 'tb_group_member'))
				   ->where('m.uid = ?', $uid)
				   ->where('m.role = ?', 'creater');
			$select->joinLeft(array('g' => 'tb_group'), 'g.gid = m.gid');
			$groups = $select->query()->fetchAll();
			
			// 新话题信息
			if(count($groups) > 0)
			{
				$select->reset();
				$select->from(array('bar' => 'tb_tbar'))->where('bar.group > 0');
				foreach ($groups as $g)
				{
					$select->orWhere('bar.group = ?', $g['gid']);
				}
				$select->order('replytime DESC')->limit(10);
				$this->view->bars = $select->query()->fetchAll();
			}
			$this->view->groups = $groups;
		}
		
		/**
		 * 群组俱乐部功能块标签：我的群组，浏览群组，好友群组
		 *
		 */
		function tabAction()
		{}
	}

?>