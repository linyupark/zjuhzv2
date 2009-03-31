<?php

	/**
	 * 站内短消息控制器
	 *
	 */
	class Space_Msg_PmController extends Zend_Controller_Action 
	{	
		function init(){  }
		
		/**
		 * 清除收件箱记录
		 *
		 */
		function clearAction()
		{
			$this->getHelper('viewRenderer')->setNoRender();
			if($this->getRequest()->isXmlHttpRequest())
			{
				$pid = $this->_getParam('pid');
				$mids = DbModel::Space()->fetchAll('SELECT `mid` 
					FROM `tb_msg` WHERE `parent` = ? AND `type` = "pm"', $pid);
				if(count($mids) > 0)
				{
					foreach ($mids as $r)
					{
						Logic_Space_Msg::clear($r['mid'], 'ibox');
					}
					Logic_Space_Msg::del();
				}
				echo 'success';
			}
		}
		
		/**
		 * 发送站内信表单
		 *
		 */
		function indexAction()
		{
			$uid = $this->_getParam('uid');
			$incept = Logic_User_Base::get($uid);
			$this->view->incept = $incept;
			$this->view->type = 'send';
		}
		
		/**
		 * 相关对话
		 *
		 */
		function viewAction()
		{
			$uid = Cmd::uid();
			$params = $this->getRequest()->getParams();
			$children = Logic_Space_Msg::children($params['rel']);
			
			// 找出对方的uid
			if($children[0]['sender'] != $uid)
			$oid = $children[0]['sender'];
			else $oid = $children[0]['incept'];
			$pm_user = Logic_User_Base::get($oid);
			
			// 将属于接受的信息改为已读
			foreach ($children as $r)
			{
				if($r['incept'] == $uid)
				Logic_Space_Msg::reading($r['mid']);
			}
			
			$this->view->pm_user = $pm_user;
			$this->view->rows = $children;
		}
		
		function replyAction()
		{
			$this->getHelper('viewRenderer')->setNoRender();
			$uid = Cmd::uid();
			$params = $this->_getAllParams();
			$params = Filter_Msg::reply($params);
			if(Alp_Sys::getMsg() == null)
			{	
				$params['sender'] = $uid;
				Logic_Space_Msg::reply($params);
				echo 'success';
			}
			echo Alp_Sys::allMsg('* ', "\n");
		}
		
		/**
		 * 短消息发送
		 *
		 */
		function postAction()
		{
			$this->getHelper('viewRenderer')->setNoRender();
			$uid = Cmd::uid();
			$params = $this->_getAllParams();
			$params = Filter_Msg::pm($params);
			if(Alp_Sys::getMsg() == null)
			{
				if($params['incept'] == '') // 从好友名字中获取id
				{
					$unames = explode(' ', $params['uname']);
					$rows = Logic_User_Base::uid($unames);
					$incept = array();
					foreach ($rows as $r)
					{
						$incept[] = $r['uid'];
					}
				}
				else $incept = explode(',', $params['incept']);
				if(count($incept) > 0)
				{
					// 逐个发信
					foreach ($incept as $u)
					{
						Logic_Space_Msg::pm($uid, $u, Alp_String::html($params['content']));
					}
					echo 'success';
				}
				else Alp_Sys::msg('incept', '无效的发送对象，请重新选择');
			}
			echo Alp_Sys::allMsg('* ', "\n");
		}
		
		/**
		 * 好友列表
		 *
		 */
		function friendsAction()
		{
			$this->view->sorts = Logic_Space_Friends::getSort(Cmd::uid());
		}
	}

?>