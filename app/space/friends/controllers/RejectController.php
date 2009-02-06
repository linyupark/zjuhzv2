<?php

	/**
	 * 好友请求拒绝控制
	 *
	 */
	class Space_Friends_RejectController extends Zend_Controller_Action 
	{
		/**
		 * 删除好友请求
		 *
		 */
		function indexAction()
		{
			$this->getHelper('viewRenderer')->setNoRender();
			if($this->getRequest()->isXmlHttpRequest())
			{
				$params = $this->_getAllParams();
				$params = Filter_Friends::reject($params);
				if(Alp_Sys::getMsg() == null)
				{
					Logic_Space_Friends::reject($params);
					if(Alp_Sys::getMsg() == null)
					{
						echo 'success';
						exit();
					}
				}
				echo Alp_Sys::allMsg('* ', "\n");
			}
		}
		
		/**
		 * 删除好友
		 *
		 */
		function delAction()
		{
			$this->getHelper('viewRenderer')->setNoRender();
			if($this->getRequest()->isXmlHttpRequest())
			{
				$fid = $this->_getParam('fid');
				$uid = Cmd::uid();
				DbModel::Space()->delete('tb_friends', 'uid = '.$uid.' AND friend = '.$fid);
				echo 'success';
			}
		}
	}

?>