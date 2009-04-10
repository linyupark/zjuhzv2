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
			// 一些短信息提示操作
		}
		
		/**
		 * 一般静态文档
		 *
		 */
		function docAction()
		{
			$this->view->of = $this->_getParam('of');
		}
		
		/**
		 * 首页赞助友情链接
		 *
		 */
		function linksAction()
		{
			$this->view->links = DbModel::getSqlite('mix.s3db')->fetchAll('SELECT * FROM `tb_links` WHERE `home` = 1 ORDER BY `serid` ASC');
		}
		
		/**
		 * 网站头版新闻编辑
		 *
		 */
		function ftpageAction()
		{
			$this->view->html = file_get_contents(HTMLROOT.'/player/ftpage.html');
		}
		
		/**
		 * 快速登陆显示
		 *
		 */
		function fastloginAction(){}
		
		/**
		 * 群组首页展示
		 *
		 */
		function groupAction()
		{
			$limit = Cmd::role() == 'guest' ? 40 : 20;
			$rows = DbModel::Space()->fetchAll('SELECT * FROM `tb_group`
				WHERE `type` != "close" 
				ORDER BY `point` DESC LIMIT '.$limit);
			$this->view->groups = $rows;
		}
		
		/**
		 * 热心度排行
		 *
		 */
		function rankAction()
		{
			$rows = Logic_Api::pointrank(array(1), 6);
			$this->view->rows = $rows;
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
					AND bar.`deny` = 0  
					AND e.`time` > '.$now.'  
					AND (g.`type` IS NULL OR g.`type` != "close")
				)  
				ORDER BY e.`time` ASC LIMIT 8');
			$this->view->events = $rows;
		}
		
		/**
		 * 最新投票
		 *
		 */
		function voteAction()
		{
			$rows = DbModel::Space()->fetchAll('SELECT bar.*  
				FROM `tb_tbar` AS `bar` 
				WHERE (
					bar.`private` IN(3,4) 
					AND bar.`deny` = 0 
					AND bar.`type` = "vote" 
				) 
				ORDER BY bar.`replytime` DESC LIMIT 3');
			$this->view->bars = $rows;
		}
		
		/**
		 * 群组顶帖
		 *
		 */
		function groupbarsAction()
		{
			$rows = DbModel::Space()->fetchAll('SELECT bar.*,g.`name` AS `gname` 
				FROM `tb_tbar` AS `bar` 
				LEFT JOIN `tb_group` AS `g` ON g.`gid` = bar.`group` 
				WHERE (
					bar.`private` IN(3,4) 
					AND bar.`group` != 0 
					AND bar.`deny` != 1 
					AND bar.`type` NOT IN ("events","vote") 
				) 
				ORDER BY bar.`replytime` DESC LIMIT 10');
			$this->view->bars = $rows;
		}
		
		/**
		 * 公开话题
		 *
		 */
		function barsAction()
		{
			$limit = Cmd::role() == 'guest' ? 15 : 8;
			$rows = DbModel::Space()->fetchAll('SELECT bar.*  
				FROM `tb_tbar` AS `bar` 
				WHERE (
					bar.`private` IN(2,3,4) 
					AND bar.`group` = 0 
					AND bar.`deny` = 0 
					AND bar.`type` IN ("topic","photo","share","video","help") 
				) 
				ORDER BY bar.`replytime` DESC LIMIT '.$limit);
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
				WHERE bar.`type` = "news" AND bar.`private` >= 3 
				AND bar.`deny` = 0 
				ORDER BY bar.`ding`DESC,bar.`pubtime` DESC LIMIT 8');
			$this->view->news = $rows;
		}
	}

?>