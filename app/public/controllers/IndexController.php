<?php

	class IndexController extends Zend_Controller_Action 
	{
		function indexAction()
		{
		}
		
		function docAction()
		{
			$this->view->of = $this->_getParam('of');
		}
		
		/**
		 * 新闻显示
		 *
		 */
		function newsAction()
		{
			$rows = DbModel::Space()->fetchAll('SELECT bar.*,s.`name` FROM `tb_tbar` AS `bar` 
				LEFT JOIN `tb_news` AS `news` ON news.`tid` = bar.`tid` 
				LEFT JOIN `tb_news_sort` AS `s` ON s.`sort` = news.`sort` 
				WHERE bar.`type` = "news" AND bar.`private` = 4 
				ORDER BY bar.`ding`,bar.`pubtime` DESC');
			$this->view->news = $rows;
		}
	}

?>