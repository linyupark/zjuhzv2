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
			echo Zend_Json::encode(array('result'=>Logic_Space_Msg::hasnew(Cmd::uid())));
		}
	}

?>