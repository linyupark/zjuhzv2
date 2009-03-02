<?php

	/**
	 * 网站首页控制器
	 *
	 */
	class IndexController extends Zend_Controller_Action 
	{
		function init()
		{
			$this->view->icons = Zend_Registry::get('config')->bar_icon->toArray();
		}
		
		function indexAction()
		{
		}
		
		function docAction()
		{
			$this->view->of = $this->_getParam('of');
		}
		
		/**
		 * 近期活动
		 *
		 */
		function eventsAction()
		{
			$now = time();
			$rows = DbModel::Space()->fetchAll('SELECT bar.*,e.`time` AS `starttime` 
				FROM `tb_tbar` AS `bar` 
				LEFT JOIN `tb_group` AS `g` ON g.`gid` = bar.`group` 
				LEFT JOIN `tb_events` AS `e` ON e.`tid` = bar.`tid` 
				WHERE (
					bar.`private` IN(2,3,4) 
					AND bar.`type` = "events" 
					AND e.`time` > '.$now.'  
					AND (g.`type` IS NULL OR g.`type` != "close")
				)  
				ORDER BY e.`time` ASC LIMIT 5');
			$this->view->events = $rows;
		}
		
		/**
		 * 新帖动态,排除公开新闻
		 *
		 */
		function barsAction()
		{
			$rows = DbModel::Space()->fetchAll('SELECT bar.*  
				FROM `tb_tbar` AS `bar` 
				LEFT JOIN `tb_group` AS `g` ON g.`gid` = bar.`group` 
				WHERE (
					bar.`private` IN(2,3,4) 
					AND (g.`type` IS NULL OR g.`type` != "close")
				)  
				AND (bar.`type` != "news" AND bar.`type` != "events")  
				ORDER BY bar.`pubtime` DESC LIMIT 10');
			$this->view->bars = $rows;
		}
		
		/**
		 * 新闻显示
		 *
		 */
		function newsAction()
		{
			$rows = DbModel::Space()->fetchAll('SELECT bar.*,s.`name`,s.`sort` 
				FROM `tb_tbar` AS `bar` 
				LEFT JOIN `tb_news` AS `news` ON news.`tid` = bar.`tid` 
				LEFT JOIN `tb_news_sort` AS `s` ON s.`sort` = news.`sort` 
				WHERE bar.`type` = "news" AND bar.`private` = 4 
				ORDER BY bar.`ding`DESC,bar.`pubtime` DESC LIMIT 7');
			$this->view->news = $rows;
		}
	}

?>