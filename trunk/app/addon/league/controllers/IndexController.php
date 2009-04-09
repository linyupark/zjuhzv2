<?php

	/**
	 * 大学生实习联盟搜索页面
	 *
	 */
	class Addon_League_IndexController extends Zend_Controller_Action 
	{
		function introAction()
		{
			$row = DbModel::getSqlite('league.s3db')->fetchRow('
				SELECT `name`,`intro` FROM `tb_corp` WHERE `corp_id` = ?', $this->_getParam('id'));
			$this->view->row = $row;
		}
		
		/**
		 * 报名情况获取
		 *
		 */
		function signboxAction()
		{
			$this->getHelper('viewRenderer')->setNoRender();
			$tid = $this->_getParam('tid');
			$e = Logic_Space_Bar_Events::view($tid);
			$m = Logic_Space_Bar_Events::members($tid);
			$sign_num = ($m == false) ? 0 : count($m);
			echo $e[0]['limit'].' / '.$sign_num;
		}
		
		/**
		 * 列表
		 *
		 */
		function indexAction()
		{
			$params = $this->getRequest()->getParams();
			$page = (int)$params['p'] == 0 ? 1 : $params['p'];
			$pagesize = 20;
			$select = DbModel::getSqlite('league.s3db')->select();			
			$select->from('tb_corp');
			
			if($params['region']) // 筛选
			$select->where('region LIKE "%'.$params['region'].'%"');
			if($params['trade'])
			$select->where('trade LIKE "%'.$params['trade'].'%"');
			if($params['name'])
			$select->where('name LIKE "%'.$params['name'].'%"');
				
			$rows = $select->query()->fetchAll();
			$numrows = count($rows);
			if($numrows > $pagesize)
			{
				Alp_Page::$pagesize = $pagesize;
				Alp_Page::create(array(
					'href_open' => '<a href="/addon_league/?region='.urlencode($params['region']).'&trade='.urlencode($params['trade']).'&name='.urlencode($params['name']).'&p=%d">',
					'href_close' => '</a>',
					'cur_page' => $page,
					'num_rows' => $numrows
				));
				$select->limit($pagesize, Alp_Page::$offset);
				$this->view->pagination = Alp_Page::$page_str;
			}
			
			$this->view->numrows = $numrows;
			$this->view->rows = $select->query()->fetchAll();
			$this->view->regions = Logic_Addon_League::opt('region');
			$this->view->trades = Logic_Addon_League::opt('trade');
			$this->view->params = $params;
		}
	}

?>