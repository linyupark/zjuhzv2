<?php
	
	/**
	 * 帖子修改
	 *
	 */
	class Space_Bar_ModController extends Zend_Controller_Action
	{
		function init()
		{
			// 是否为群组帖
			$this->view->gp = $this->_getParam('gp', 0);
			$this->view->tid = $this->_getParam('tid');
		}
		
		/**
		 * 是否有权限修改
		 *
		 * @param unknown_type $tid
		 */
		function isAllowed($tid)
		{
			$row = DbModel::Space()->fetchRow('SELECT `puber` FROM `tb_tbar` WHERE `tid` = ?', $tid);
			$puber = $row['puber'];
			if(Cmd::role() == 'master' || $puber == Cmd::uid()) return true;
			else return false;
		}
		
		/**
		 * 话题
		 *
		 */
		function topicAction()
		{
			$this->view->headTitle('修改话题');
			$tid = $this->view->tid;
			$row = Logic_Space_Bar_Topic::view($tid);
			if($this->getRequest()->isXmlHttpRequest() && $this->isAllowed($tid)) // 处理保存
			{
				$this->getHelper('viewRenderer')->setNoRender();
				$params = $this->getRequest()->getParams();
				$params = Filter_Space::modtopic($params);
				if(Alp_Sys::getMsg() == null)
				{
					Logic_Space_Bar_Topic::mod($params, $tid);
					if(Alp_Sys::getMsg() == null)
					{
						echo Zend_Json::encode(array('result'=>'success', 'tid' => $tid));
						exit();
					}
				}
				echo Zend_Json::encode(array('result'=>Alp_Sys::allMsg('* ',"\n")));
			}
			$this->view->row = $row[0];
		}
	}

?>