<?php

	/**
	 * 新闻帖子
	 *
	 */
	class Space_Bar_NewsController extends Zend_Controller_Action
	{
		function indexAction()
		{
			
		}
		
		/**
		 * 新闻分类模块调用
		 *
		 */
		function sortsAction()
		{
			$this->getHelper('viewRenderer')->setNoRender();
			if($this->getRequest()->isXmlHttpRequest())
			{
				$this->view->sorts = Logic_Space_Bar_News::getSorts(); // 获取新闻分类
				$this->render('sorts');
			}
		}
		
		/**
		 * 创建新新闻分类
		 *
		 */
		function createsortAction()
		{
			$this->getHelper('viewRenderer')->setNoRender();
			if($this->getRequest()->isXmlHttpRequest())
			{
				$sortname = Filter_Space::newsSort($this->_getParam('sortname'));
				if(Alp_Sys::getMsg() == null)
				{
					Logic_Space_Bar_News::createSort($sortname);
					if(Alp_Sys::getMsg() == null)
					{
						echo 'success';
						exit();
					}
				}
				echo Alp_Sys::allMsg('* ', "\n");
			}
		}
	}

?>