<?php

	class Addon_Partners_UserController extends Zend_Controller_Action 
	{
		private $params;
		
		function init()
		{
			$this->params = $this->getRequest()->getParams();
		}
		
		/**
		 * 用户登陆界面
		 *
		 */
		function indexAction()
		{
			$this->view->goto = $this->params['goto'];
		}
		
		/**
		 * 登陆操作
		 *
		 */
		function dologinAction()
		{
			$this->getHelper('viewRenderer')->setNoRender();
			$params = Filter_Addon::partUser($this->params);
			if(Alp_Sys::getMsg() == null)
			{
				$row = Logic_Addon_Partners::checkUser($params);
				if($row != false)
				{
					Cmd::setSess('addon_partner', $row['uid']); // 保存账号id
					echo '<div class="success">登陆成功，系统即将跳转至您所管理的<a href="/addon_partners/manage/corplist?uid='.$row['uid'].'">企业列表</a></div>';
					echo Alp_Sys::jump('/addon_partners/manage/corplist?uid='.$row['uid'], 2);
					exit();
				}
				else Alp_Sys::msg('error', '账号密码错误');
			}
			echo '<script>alert("'.Alp_Sys::allMsg('*','\n').'");</script>';
		}
		
		/**
		 * 添加新赞助合作伙伴账号地址格式：
		 * /addon_partners/user/add?username=xxxx&password=xxxxx
		 *
		 */
		function docreateAction()
		{
			$this->getHelper('viewRenderer')->setNoRender();
			$params = Filter_Addon::partUser($this->params);
			if(Alp_Sys::getMsg() == null)
			{
				$uid = Logic_Addon_Partners::insertUser($params);
				if($uid != 0) 
				{
					echo '<div class="success">创建成功, 账号:'.$params['username'].' 密码:'.$params['password'].'</div>';
					exit();
				}
			}
			echo '<script>alert("'.Alp_Sys::allMsg('*','\n').'");</script>';
		}
	}

?>