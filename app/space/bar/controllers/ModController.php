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
			$gid = $this->view->gp;
			$uid = Cmd::uid(); $role = Cmd::role();
			$row = DbModel::Space()->fetchRow('SELECT `puber` FROM `tb_tbar` WHERE `tid` = ?', $tid);
			if($role == 'master' || $row['puber'] == $uid) return true;
			if($gid > 0)
			{
				$grole = Logic_Space_Group_Member::role($gid, $uid);
				if($grole == 'creater' || $grole == 'manager')
				return true;
			}
			$this->_forward('deny', 'error', 'public', array('position' => 'space_bar_mod'));
			return false;
		}
		
		/**
		 * 删除共享单元
		 *
		 */
		function delshareAction()
		{
			$this->getHelper('viewRenderer')->setNoRender();
			$id = $this->_getParam('id');
			Logic_Space_Bar_Share::del($id);
		}
		
		/**
		 * 修改共享帖
		 *
		 */
		function shareAction()
		{
			$tid = $this->view->tid;
			if($this->getRequest()->isXmlHttpRequest()) // 处理保存
			{
				$this->getHelper('viewRenderer')->setNoRender();
				$params = $this->getRequest()->getParams();
				$params = Filter_Space::modshare($params);
				if(Alp_Sys::getMsg() == null)
				{
					Logic_Space_Bar_Share::mod($params, $tid);
					if(Alp_Sys::getMsg() == null)
					{
						echo Zend_Json::encode(array('result'=>'success', 'tid' => $tid));
						exit();
					}
				}
				echo Zend_Json::encode(array('result'=>Alp_Sys::allMsg('* ',"\n")));
			}
			else 
			{
				$this->view->headTitle('修改共享帖');
				$this->isAllowed($tid);
				$row = Logic_Space_Bar_Share::view($tid);
				$this->view->row = $row[0];
				$this->view->items = DbModel::Space()->fetchAll('SELECT * FROM `tb_share` WHERE `tid` = ? ORDER BY `id` ASC', $tid);
			}
		}
		
		/**
		 * 删除图片帖单元数据
		 *
		 */
		function delphotoAction()
		{
			$this->getHelper('viewRenderer')->setNoRender();
			$id = $this->_getParam('id');
			Logic_Space_Bar_Photo::del($id);
		}
		
		/**
		 * 修改图片讨论帖
		 *
		 */
		function photoAction()
		{
			$tid = $this->view->tid;
			if($this->getRequest()->isXmlHttpRequest()) // 处理保存
			{
				$this->getHelper('viewRenderer')->setNoRender();
				$params = $this->getRequest()->getParams();
				$params = Filter_Space::modphoto($params);
				if(Alp_Sys::getMsg() == null)
				{
					Logic_Space_Bar_Photo::mod($params, $tid);
					if(Alp_Sys::getMsg() == null)
					{
						echo Zend_Json::encode(array('result'=>'success', 'tid' => $tid));
						exit();
					}
				}
				echo Zend_Json::encode(array('result'=>Alp_Sys::allMsg('* ',"\n")));
			}
			else 
			{
				$this->view->headTitle('修改图片帖');
				$this->isAllowed($tid);
				$row = Logic_Space_Bar_Photo::view($tid);
				$this->view->row = $row[0];
				$this->view->photos = DbModel::Space()->fetchAll('SELECT * FROM `tb_photo` WHERE `tid` = ? ORDER BY `id` ASC', $tid);
			}
		}
		
		/**
		 * 修改投票调查
		 *
		 */
		function voteAction()
		{
			$tid = $this->view->tid;
			if($this->getRequest()->isXmlHttpRequest()) // 处理保存
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
			else 
			{
				$this->view->headTitle('修改投票');
				$this->isAllowed($tid);
				$row = Logic_Space_Bar_Vote::view($tid);
				$this->view->row = $row[0];
				$this->view->options = unserialize($row[0]['options']);
			}
		}
		
		/**
		 * 活动
		 *
		 */
		function eventsAction()
		{
			$tid = $this->view->tid;
			if($this->getRequest()->isXmlHttpRequest()) // 处理保存
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
			else 
			{
				$this->view->headTitle('修改活动');
				$this->isAllowed($tid);
				$row = Logic_Space_Bar_Events::view($tid);
				$this->view->row = $row[0];
			}
		}
		
		/**
		 * 新闻
		 *
		 */
		function newsAction()
		{
			$tid = $this->view->tid;
			if($this->getRequest()->isXmlHttpRequest()) // 处理保存
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
			else
			{
				$this->view->headTitle('修改新闻');
				$this->isAllowed($tid);
				$row = Logic_Space_Bar_News::view($tid);
				$this->view->row = $row[0];
			}
		}
		
		/**
		 * 视频
		 *
		 */
		function videoAction()
		{
			$tid = $this->view->tid;
			if($this->getRequest()->isXmlHttpRequest()) // 处理保存
			{
				$this->getHelper('viewRenderer')->setNoRender();
				$params = $this->getRequest()->getParams();
				$params = Filter_Space::modtopic($params);
				if(Alp_Sys::getMsg() == null)
				{
					Logic_Space_Bar_Video::mod($params, $tid);
					if(Alp_Sys::getMsg() == null)
					{
						echo Zend_Json::encode(array('result'=>'success', 'tid' => $tid));
						exit();
					}
				}
				echo Zend_Json::encode(array('result'=>Alp_Sys::allMsg('* ',"\n")));
			}
			else
			{
				$this->view->headTitle('修改视频');
				$this->isAllowed($tid);
				$row = Logic_Space_Bar_Video::view($tid);
				$this->view->row = $row[0];
			}
		}
		
		/**
		 * 话题
		 *
		 */
		function topicAction()
		{
			$tid = $this->view->tid;
			if($this->getRequest()->isXmlHttpRequest()) // 处理保存
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
			else
			{
				$this->view->headTitle('修改话题');
				$this->isAllowed($tid);
				$row = Logic_Space_Bar_Topic::view($tid);
				$this->view->row = $row[0];
			}
		}
	}

?>