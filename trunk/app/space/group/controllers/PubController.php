<?php

	/**
	 * 群组创建控制器
	 *
	 */
	class Space_Group_PubController extends Zend_Controller_Action 
	{
		/**
		 * 创建导航页
		 *
		 */
		function indexAction()
		{
			$type = $this->_getParam('type');
			if($type)
			{
				$this->view->type= $this->_getParam('type');
				$this->render('form');
			}
			else $this->render('index');
		}
		
		/**
		 * 创建群组俱乐部
		 *
		 */
		function createAction()
		{
			$params = $this->_getAllParams();
			if($this->getRequest()->isXmlHttpRequest())
			{
				$this->getHelper('viewRenderer')->setNoRender();
				$params = Filter_Group::create($params);
				if(Alp_Sys::getMsg() == null)
				{
					$gid = Logic_Space_Group::create($params, Cmd::uid());
					if(Alp_Sys::getMsg() == null)
					{
						echo Zend_Json::encode(array('result'=>'success','gid'=>$gid));
						exit();
					}
				}
				echo Zend_Json::encode(array('result'=>Alp_Sys::allMsg('* ',"\n")));
			}
		}
	}

?>