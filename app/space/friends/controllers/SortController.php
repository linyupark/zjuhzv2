<?php

	/**
	 * 好友分组管理
	 *
	 */
	class Space_Friends_SortController extends Zend_Controller_Action 
	{
		function init(){}
		
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
	}

?>