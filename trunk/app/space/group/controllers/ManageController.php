<?php

	/**
	 * 群组管理控制器
	 *
	 */
	class Space_Group_ManageController extends Zend_Controller_Action 
	{
		function init()
		{ 
			$gid = $this->_getParam('id');
			$group = Logic_Space_Group::info($gid);
			$this->view->group = $group;
			$this->view->gid = $gid; 
		}
		
		function indexAction()
		{
			$role = Logic_Space_Group_Member::role($this->view->gid, Cmd::uid());
			if($role != 'creater' && $role != 'manager')
			$this->_forward('deny', 'error', 'public', 
				array('position'=>'space_group_home','group' => $this->view->group));
			$this->view->type = $this->_getParam('type', 'basic');
		}
		
		function barcmdAction()
		{
			if($this->getRequest()->isXmlHttpRequest()):
			$this->getHelper('viewRenderer')->setNoRender();
			$cmd = $this->_getParam('cmd');
			$tid = $this->_getParam('tid');
			Logic_Api::barcmd($tid, $cmd);
			if(Alp_Sys::getMsg() == null){ echo 'success'; }
			endif;
		}
		
		/**
		 * 奖励热心度(从群point中扣除)
		 *
		 */
		function pointAction()
		{
			
		}
		
		/**
		 * 帖子管理
		 *
		 */
		function barAction()
		{
			$params = $this->getRequest()->getParams();
			$gid = $params['id'];
			$range = $params['range'] ? $params['range'] : 'all';
			$key = trim(urldecode($params['key']));
			$page = $this->_getParam('p', 1);
			$pagesize = 10;
			$select = DbModel::Space()->select();
			$select->from(array('b'=>'tb_tbar'), array('numrows' => new Zend_Db_Expr('COUNT(b.tid)')))
				   ->where('b.group = ?', $gid);
			
			// 帖子显示范围
			if($range != 'all' && $range != 'deny' && $range != 'ding')
			$select->where('b.type = ?', $range);
			if($range == 'ding') $select->where('b.ding = 1');
			if($range == 'deny') $select->where('b.deny = 1');
			if($range != 'deny') $select->where('b.deny = 0');
			
			// 如果有模糊查询
			if(!empty($key))
			{
				$select->where('b.title LIKE "%'.$key.'%"');
			}
			
			$row = $select->query()->fetchAll();
			$numrows = $row[0]['numrows'];
			$select->reset(Zend_Db_Select::COLUMNS)->columns('*');
			
			if($numrows > $pagesize)
			{
				Alp_Page::$pagesize = $pagesize;
				Alp_Page::create(array(
					'href_open' => '<a href="/space_group/manage/?type=bar&id='.$gid.'&range='.$range.'&key='.urlencode($key).'&p=%d">',
					'href_close' => '</a>',
					'num_rows' => $numrows,
					'cur_page' => $page
				));
				$select->limit($pagesize, Alp_Page::$offset);
				$this->view->pagination = Alp_Page::$page_str;
			}
			
			$select->joinLeft(array('g' => 'tb_group'), 'g.gid = b.group', 
							  array('gname' => 'g.name', 'gtype' => 'g.type'));
							  
			$select->order('b.ding DESC')->order('b.pubtime DESC');
			
			$this->view->numrows = $numrows;
			$this->view->range = $range;
			$this->view->key = $key;
			$this->view->rows = $select->query()->fetchAll();
			$this->view->icons = Zend_Registry::get('config')->bar_icon->toArray();
		}
		
		/**
		 * 群成员管理
		 *
		 */
		function memberAction()
		{
			$gid = $this->_getParam('id');
			$name = $this->_getParam('uname');
			$select = DbModel::Space()->select();
			$page = $this->_getParam('p', 1);
			$query = '';
			
			$select->from(array('gm' => 'tb_group_member'));
			
			$select->where('gm.gid = ?', $gid)
				   ->where('gm.role = "manager" OR gm.role = "member" OR gm.role = "join"');
				   
			if($name != null)
			{
				$query = '&uname='.urlencode($name);
				$select->where('u.username LIKE "%'.$name.'%"');
				$this->view->uname = urldecode($name);
			}
			
			$row = $select->query()->fetchAll();
			$select->joinLeft(array('u' => 'zjuhzv2_user.tb_base'), 'u.uid = gm.uid', 
				   			  array('uname'=>'u.username', 'sex'=>'u.sex', 'u.lastlogin'));
			$pagesize = 10;
			if(count($row) > $pagesize)
			{
				Alp_Page::$pagesize = $pagesize;
				Alp_Page::create(array(
					'href_open' => '<a href="/space_group/manage/?id='.$gid.'&type=member'.$query.'&p=%d">',
					'href_close' => '</a>',
					'num_rows' => count($row),
					'cur_page' => $page
				));
				$select->limit($pagesize, Alp_Page::$offset);
				$this->view->pagination = Alp_Page::$page_str;
			}
			$select->order('u.lastlogin DESC');
			$this->view->rows = $select->query()->fetchAll();
			$this->view->role = Logic_Space_Group_Member::role($gid, Cmd::uid());
		}
		
		/**
		 * 群消息
		 *
		 */
		function msgAction()
		{
			if($this->getRequest()->isXmlHttpRequest())
			{
				$this->getHelper('viewRenderer')->setNoRender();
				$params = $this->getRequest()->getParams();
				$params = Filter_Group::msg($params);
				if(Alp_Sys::getMsg() == null)
				{
					$params['uid'] = Cmd::uid();
					Logic_Space_Group_Manage::msg($params);
					if(Alp_Sys::getMsg() == null){ echo 'success'; exit(); }
				}
				echo Alp_Sys::allMsg('* ', "\n");
			}
		}
		
		/**
		 * 群标
		 *
		 */
		function logoAction()
		{
			
		}
		
		/**
		 * 基本信息
		 *
		 */
		function basicAction()
		{
			if($this->getRequest()->isXmlHttpRequest())
			{
				$this->getHelper('viewRenderer')->setNoRender();
				$params = $this->getRequest()->getParams();
				$params = Filter_Group::basic($params);
				if(Alp_Sys::getMsg() == null)
				{
					Logic_Space_Group_Manage::basic($params);
					if(Alp_Sys::getMsg() == null){ echo 'success'; exit(); }
				}
				echo Alp_Sys::allMsg('* ', "\n");
			}
		}
		
		function tabAction()
		{
			$this->view->type = $this->_getParam('type', 'basic');
		}
	}

?>