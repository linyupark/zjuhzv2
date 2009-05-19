<?php

	/**
	 * 全局获取信箱状态(保持session活动状态)
	 *
	 */
	class Api_Json_MsgController extends Zend_Controller_Action 
	{
		function init()
		{
			Zend_Layout::getMvcInstance()->disableLayout();
			$this->getHelper('viewRenderer')->setNoRender();
		}
		
		/**
		 * 检查是否有新信息
		 *
		 */
		function checkAction()
		{
			// 在线成员更新
			$uid = Cmd::uid();
			$db = DbModel::getSqlite('online.s3db');
			$row = $db->fetchRow('SELECT `uid` FROM `ol` WHERE `uid` = ?', $uid);
			if($row == false) $db->insert('ol', array('uid' => $uid, 'time' => time()));
			else $db->update('ol', array('time' => time()), 'uid = '.$uid);
			// 返回新信息数
			echo Zend_Json::encode(array('result'=>Logic_Space_Msg::hasnew($uid)));
		}
		
		/**
		 * 自己在线触发的系统自动加分提示
		 *
		 */
		function aptAction()
		{
			$this->getHelper('viewRenderer')->setNoRender(false);
			$params = $this->getRequest()->getParams();
			$this->view->params = $params;
		}
	}

?>