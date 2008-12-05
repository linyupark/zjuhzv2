<?php

	/**
	 * 求助帖子
	 *
	 */
	class Space_Bar_HelpController extends Zend_Controller_Action
	{
		function indexAction()
		{
			
		}
		
		/**
		 * 求助分类模块调用
		 *
		 */
		function sortsAction()
		{
			$this->getHelper('viewRenderer')->setNoRender();
			if($this->getRequest()->isXmlHttpRequest())
			{
				$this->view->sorts = Logic_Space_Bar_Help::getSorts(); // 获取新闻分类
				$this->render('sorts');
			}
		}
		
		/**
		 * 创建新求助分类
		 *
		 */
		function createsortAction()
		{
			$this->getHelper('viewRenderer')->setNoRender();
			if($this->getRequest()->isXmlHttpRequest())
			{
				$sortname = Filter_Space::helpSort($this->_getParam('sortname'));
				if(Alp_Sys::getMsg() == null)
				{
					Logic_Space_Bar_Help::createSort($sortname);
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