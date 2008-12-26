<?php

	/**
	 * 在线状态
	 *
	 */
	class Api_Json_OnlineController extends Zend_Controller_Action 
	{
		function init()
		{
			Zend_Layout::getMvcInstance()->disableLayout();
			$this->getHelper('viewRenderer')->setNoRender();
		}
		
		/**
		 * 检查是否在线
		 *
		 */
		function checkAction()
		{
			$uid = $this->_getParam('uid');
			$alive = time() - 900; // 15分钟等待时间
			$db = DbModel::getSqlite('online.s3db');
			$row = $db->fetchRow('SELECT `uid`,`time` FROM `ol` WHERE `uid` = ?', $uid);
			if($row == false || $row['time'] < $alive) echo Zend_Json::encode(array('state' => 'offline'));
			else echo Zend_Json::encode(array('state' => 'online'));
		}
	}

?>