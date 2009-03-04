<?php

	/**
	 * 帖子管理
	 *
	 */
	class Console_BarController extends Zend_Controller_Action
	{
		/**
		 * 帖子归类管理
		 *
		 */
		function newssortAction()
		{
			$sorts = Logic_Space_Bar_News::getSorts();
			$this->view->sorts = $sorts;
		}
		
		/**
		 * 修改帖子属性
		 *
		 */
		function updateAction()
		{
			if($this->getRequest()->isXmlHttpRequest()):
			$this->getHelper('viewRenderer')->setNoRender();
			$type = $this->_getParam('t');
			$tid = $this->_getParam('tid');
			try {
				switch ($type)
				{
					case 'ding' : 
						DbModel::Space()->update('tb_tbar', array('ding' => 1), 'tid = '.$tid);
					break;
					case 'unding' : 
						DbModel::Space()->update('tb_tbar', array('ding' => 0), 'tid = '.$tid);
					break;
					case 'deny' : 
						DbModel::Space()->update('tb_tbar', array('deny' => 1), 'tid = '.$tid);
					break;
					case 'undeny' : 
						DbModel::Space()->update('tb_tbar', array('deny' => 0), 'tid = '.$tid);
					break;
				}
				echo 'success';
				
			} catch (Exception $e) {
				echo $e->getMessage();
			}
			endif;
		}
		
		/**
		 * 帖子
		 *
		 */
		function barAction()
		{
			$params = $this->getRequest()->getParams();
			$range = $params['range'] ? $params['range'] : 'news';
			$key = trim($params['key']);
			$page = $this->_getParam('p', 1);
			$pagesize = 10;
			$select = DbModel::Space()->select();
			$select->from(array('b'=>'tb_tbar'), array('numrows' => new Zend_Db_Expr('COUNT(b.tid)')));
			
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
					'href_open' => '<a href="/console/bar/?tab=bar&range='.$range.'&key='.urlencode($key).'&p=%d">',
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
		
		function indexAction()
		{
			$this->view->tab = $this->_getParam('tab', 'bar');
		}
	}
	
?>