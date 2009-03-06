<?php

	/**
	 * 开放新闻控制器
	 *
	 */
	class NewsController extends Zend_Controller_Action 
	{
		function init()
		{
			$tid = $this->_getParam('id');
			$this->tid = $tid;
			// 已经登录的直接到内部查看
			if(Cmd::role() != 'guest') 
			$this->_redirect('/space_bar/news/view?tid='.$tid);
		}
		
		/**
		 * 新闻首页(暂时不做)
		 *
		 */
		function indexAction()
		{
			
		}
		
		/**
		 * 上下文章
		 *
		 */
		function prenextAction()
		{
			$tid = $this->tid;
			$this->view->pre = DbModel::Space()->fetchRow('
				SELECT `title`,`tid` FROM `tb_tbar` 
				WHERE `tid` < '.$tid.' 
				AND `type` = "news" AND `private` = 4 
				ORDER BY `pubtime` DESC');
			$this->view->next = DbModel::Space()->fetchRow('
				SELECT `title`,`tid` FROM `tb_tbar` 
				WHERE `tid` > '.$tid.' 
				AND `type` = "news" AND `private` = 4 
				ORDER BY `pubtime` ASC');
		}
		
		/**
		 * 详细内容
		 *
		 */
		function viewAction()
		{
			$tid = $this->tid;
			$row = Logic_Space_Bar_News::view($tid);
			if(!$row[0]) // 无效帖子
			$this->_forward('error', 'error', 'public');
			if($row[0]['private'] != 4) // 有对应帖子判断阅读权限
			$this->_forward('deny', 'error', 'public');
			Logic_Space_Bar::click($tid);
			$this->view->row = $row[0];
		}
	}

?>