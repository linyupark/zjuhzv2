<?php
	/**
	 * RSS订阅控制器
	 *
	 */
	class Space_Rss_IndexController extends Zend_Controller_Action 
	{
		function init(){ $this->view->tab = $this->getRequest()->getControllerName(); }
				
		function indexAction()
		{
			$uid = Cmd::uid();
			$news_sort = Logic_Space_Bar_News::getSorts();
			$help_sort = Logic_Space_Bar_Help::getSorts();
			$groups = Logic_Space_Group::my($uid);
			
			$this->view->groups = $groups;
			$this->view->help_sort = $help_sort;
			$this->view->news_sort = $news_sort;
			$this->view->filter = Alp_String::encrypy($uid, 2);// 加密为2
		}
	}
	
?>