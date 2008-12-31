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
		 * 修改投票调查
		 *
		 */
		function voteAction()
		{
			$this->view->headTitle('修改投票');
			$tid = $this->view->tid;
			$row = Logic_Space_Bar_Vote::view($tid);
			if($this->getRequest()->isXmlHttpRequest() && $this->isAllowed($tid)) // 处理保存
			{
				$this->getHelper('viewRenderer')->setNoRender();
				$params = $this->getRequest()->getParams();
				$params = Filter_Space::modvote($params);
				if(Alp_Sys::getMsg() == null)
				{
					Logic_Space_Bar_Vote::mod($params, $tid);
					if(Alp_Sys::getMsg() == null)
					{
						echo Zend_Json::encode(array('result'=>'success', 'tid' => $tid));
						exit();
					}
				}
				echo Zend_Json::encode(array('result'=>Alp_Sys::allMsg('* ',"\n")));
			}
			$this->view->row = $row[0];
			$this->view->options = unserialize($row[0]['options']);
		}
		
		/**
		 * 活动
		 *
		 */
		function eventsAction()
		{
			$this->view->headTitle('修改活动');
			$tid = $this->view->tid;
			$row = Logic_Space_Bar_Events::view($tid);
			if($this->getRequest()->isXmlHttpRequest() && $this->isAllowed($tid)) // 处理保存
			{
				$this->getHelper('viewRenderer')->setNoRender();
				$params = $this->getRequest()->getParams();
				$params = Filter_Space::modevents($params);
				if(Alp_Sys::getMsg() == null)
				{
					Logic_Space_Bar_Events::mod($params, $tid);
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
		
		/**
		 * 新闻
		 *
		 */
		function newsAction()
		{
			$this->view->headTitle('修改新闻');
			$tid = $this->view->tid;
			$row = Logic_Space_Bar_News::view($tid);
			if($this->getRequest()->isXmlHttpRequest() && $this->isAllowed($tid)) // 处理保存
			{
				$this->getHelper('viewRenderer')->setNoRender();
				$params = $this->getRequest()->getParams();
				$params = Filter_Space::modnews($params);
				if(Alp_Sys::getMsg() == null)
				{
					Logic_Space_Bar_News::mod($params, $tid);
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