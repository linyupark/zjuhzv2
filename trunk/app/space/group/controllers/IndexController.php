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
		
		/**
		 * 群热TOP10
		 *
		 */
		function hotAction()
		{
			$rows = DbModel::Space()->fetchAll('
				SELECT `point`,`name`,`gid` 
				FROM `tb_group` 
				ORDER BY `point` DESC 
				LIMIT 15');
			$this->view->rows = $rows;
		}
		
		/**
		 * 所有
		 *
		 */
		function allAction()
		{
			$page = $this->_getParam('p', 1);
			$select = DbModel::Space()->select();
			$select->from(array('g' => 'tb_group'), array('numrows' => new Zend_Db_Expr('COUNT(g.gid)')))
				   ->where('g.type != ?', 'close');
			$row = $select->query()->fetchAll();
			$select->reset(Zend_Db_Select::COLUMNS)->columns('*');
			$pagesize = 15;
			if($row[0]['numrows'] > $pagesize)
			{
				Alp_Page::$pagesize = $pagesize;
				Alp_page::create(array(
					'href_open' => '<a href="/space_group/?list=all&p=%d">',
					'href_close' => '</a>',
					'num_rows' => $row[0]['numrows'],
					'cur_page' => $page
				));
				$select->limit($pagesize, Alp_Page::$offset);
				$this->view->pagination = Alp_Page::$page_str;
			}
			$select->order('createtime DESC');
			$this->view->numrows = $row[0]['numrows'];
			$this->view->rows = $select->query()->fetchAll();
		}
		
		/**
		 * 好友的群
		 *
		 */
		function friendAction()
		{
			// 群组信息
			$uid = Cmd::uid();
			$friends = Logic_Space_Friends::ids($uid);
			$where = '';
			if(count($friends) != 0)
			{
				$mygroup = Logic_Space_Group::my($uid);
				$select = DbModel::Space()->select();
				$select->from(array('m' => 'tb_group_member'))
					   ->where('m.role = "member" OR m.role="manager" OR m.role = "creater"');
				
				if(count($friends) > 0)
				{
					foreach ($friends as $f)
					{
						$where .= $f['friend'].',';
					}
					$select->where('m.uid IN ('.substr($where,0,-1).')');
					
					if(count($mygroup) > 0)
					{
						$where = '';
						foreach ($mygroup as $g)
						{
							$where .= ' m.gid != '.$g['gid'].' AND';
						}
						$select->where(substr($where, 0, -3));
					}
					
					$select->order(new Zend_Db_Expr('RAND()'))
					       ->joinLeft(array('g' => 'tb_group'), 'g.gid = m.gid')
					       ->joinLeft(array('u' => 'zjuhzv2_user.tb_base'), 'u.uid = m.uid', 
					       			  array('uname' => 'u.username'))
					       ->where('g.type != ?', 'close')->group('g.gid')
					       ->limit(10);
				}
				else $select->where('m.uid = 0');
				       
				$groups = $select->query()->fetchAll();
				
				// 新话题信息
				if(count($groups) > 0)
				{
					$select->reset();
					$select->from(array('bar' => 'tb_tbar'))->where('bar.deny = 0');
					foreach ($groups as $i => $g)
					{
						if($i == 0) $select->where('bar.group = ?', $g['gid']);
						else $select->orWhere('bar.group = ?', $g['gid']);
					}
					$select->order('replytime DESC')->limit(20);
					$this->view->bars = $select->query()->fetchAll();
				}
			}
			$this->view->groups = $groups;
		}
		
		/**
		 * 我的群组
		 *
		 */
		function myAction()
		{
			// 群组信息
			$uid = Cmd::uid();
			$groups = Logic_Space_Group::my($uid);
			
			// 新话题信息
			if(count($groups) > 0)
			{
				$select = DbModel::Space()->select();
				$select->from(array('bar' => 'tb_tbar'))->where('bar.deny = 0');
				foreach ($groups as $i => $g)
				{
					if($i == 0) $select->where('bar.group = ?', $g['gid']);
					else $select->orWhere('bar.group = ?', $g['gid']);
				}
				$select->order('replytime DESC')->limit(20);
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