<?php

	/**
	 * 好友分组管理
	 *
	 */
	class Space_Friends_SortController extends Zend_Controller_Action 
	{
		function init(){ $this->view->headTitle('我的好友'); }
		
		/**
		 * ajax调用好友分组内容
		 *
		 */
		function loadAction()
		{
			$sid = $this->_getParam('id', 0);
			$uid = $this->_getParam('uid', Cmd::uid());
			$this->view->rows = Logic_Space_Friends::fetch($uid, $sid);
		}
		
		/**
		 * 好友分组添加
		 *
		 */
		function createAction()
		{
			$this->getHelper('viewRenderer')->setNoRender();
			if($this->getRequest()->isXmlHttpRequest())
			{
				$uid = Cmd::uid();
				$params = Filter_Friends::sort($this->_getAllParams());
				if(Alp_Sys::getMsg() == null)
				{
					$sorts = Logic_Space_Friends::getSort($uid);
					if(in_array($params['val'], $sorts))
					Alp_Sys::msg('error', '该分组已经存在');
					if(Alp_Sys::getMsg() == null)
					{
						Logic_Space_Friends::addSort($params);
						echo 'success';
						exit();
					}
				}
				echo Alp_Sys::allMsg('* ', "\n");
			}
		}
		
		/**
		 * 好友分组改变
		 *
		 */
		function changeAction()
		{
			$this->getHelper('viewRenderer')->setNoRender();
			if($this->getRequest()->isXmlHttpRequest())
			{
				$params = $this->getRequest()->getParams();
				$uid = Cmd::uid();
				Logic_Space_Friends::sort($uid, $params['fid'], $params['sort']);
				echo 'success';
			}
		}
	}

?>