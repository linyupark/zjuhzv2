<?php

	/**
	 * 群搜索控制器
	 *
	 */
	class Space_Search_GroupController extends Zend_Controller_Action 
	{
		function indexAction()
		{
			$params = $this->getRequest()->getParams();
			$key = trim(urldecode($params['key']));
			$page = $this->_getParam('p', 1);
			$pagesize = 12;
			// 隐藏私密群，除了自己加入的
			$my = Logic_Space_Group::my(Cmd::uid());
			$sql = '';
			if(count($my) > 0)
			foreach ($my as $g)
			{
				$sql .= ' OR g.gid = '.$g['gid'];
			}
			$select = DbModel::Space()->select();
			$select->from(array('g'=>'tb_group'), 
					array('numrows' => new Zend_Db_Expr('COUNT(g.gid)')))
				   ->where('g.type != "close"'.$sql);
			// 如果有模糊查询
			if(!empty($key))
			{
				$select->where('g.name LIKE "%'.$key.'%" OR 
								g.intro LIKE "%'.$key.'%"');
			}
			$row = $select->query()->fetchAll();
			$select->reset(Zend_Db_Select::COLUMNS)->columns('*');
			if($row[0]['numrows'] > $pagesize)
			{
				Alp_Page::$pagesize = $pagesize;
				Alp_Page::create(array(
					'href_open' => '<a href="/space_search/?for=group&key='.urlencode($key).'&p=%d">',
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
	}

?>