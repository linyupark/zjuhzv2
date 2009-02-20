<?php

	/**
	 * 帖搜索控制器
	 *
	 */
	class Space_Search_BarController extends Zend_Controller_Action 
	{
		function indexAction()
		{
			$params = $this->getRequest()->getParams();
			$range = $this->_getParam('range', 'all'); // 查找范围
			$key = trim(urldecode($params['key']));
			$page = $this->_getParam('p', 1);
			$pagesize = 20;
			$select = DbModel::Space()->select();
			$select->from(array('b'=>'tb_tbar'), 
					array('numrows' => new Zend_Db_Expr('COUNT(b.tid)')))
				   ->where('b.deny = 0');
			
			if($range != 'all')
			{
				if($range == 'group')
				{
					$select->where('b.group > 0');
				}
				else $select->where('b.type = ?', $range);
			}
			
			// 如果有模糊查询
			if(!empty($key))
			{
				$select->where('b.title LIKE "%'.$key.'%"');
			}
			$row = $select->query()->fetchAll();
			$select->reset(Zend_Db_Select::COLUMNS)->columns('*');
			if($row[0]['numrows'] > $pagesize)
			{
				Alp_Page::$pagesize = $pagesize;
				Alp_Page::create(array(
					'href_open' => '<a href="/space_search/?for=bar&key='.urlencode($key).'&p=%d">',
					'href_close' => '</a>',
					'num_rows' => $row[0]['numrows'],
					'cur_page' => $page
				));
				$select->limit($pagesize, Alp_Page::$offset);
				$this->view->pagination = Alp_Page::$page_str;
			}
			
			$select->joinLeft(array('puser' => 'zjuhzv2_user.tb_base'), 'puser.uid = b.puber', 
							  array('pubname' => 'username', 'pubnick' => 'nickname'));
			$select->joinLeft(array('ruser' => 'zjuhzv2_user.tb_base'), 'ruser.uid = b.replyer', 
							  array('replyname' => 'username', 'replynick' => 'nickname'));
			$select->joinLeft(array('g' => 'tb_group'), 'g.gid = b.group', 
							  array('gname' => 'g.name', 'gtype' => 'g.type'));
				
			$select->order('b.replytime DESC');
			
			$this->view->numrows = $row[0]['numrows'];
			$this->view->icons = Zend_Registry::get('config')->bar_icon->toArray();
			$this->view->range = $range;
			$this->view->rows = $select->query()->fetchAll();
		}
	}

?>