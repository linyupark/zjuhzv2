<?php

	/**
	 * 用户个人主页
	 *
	 */
	class Space_Home_IndexController extends Zend_Controller_Action 
	{	
		function init()
		{
			Zend_Layout::getMvcInstance()->disableLayout();
			$this->getHelper('viewRenderer')->setNoRender();
		}
		
		/**
		 * 获取某人的ing记录
		 *
		 */
		function getdoingAction()
		{
			$uid = $this->_getParam('uid', Cmd::uid());
			$row = Logic_Space_Home::get('ing', $uid);
			if($row['ing'] == null) echo '';
			else echo $row['ing'];
		}
		
		/**
		 * 根据身份分配控制器
		 *
		 */
		function indexAction()
		{
			$params = $this->getRequest()->getParams();
			if($params['uid'] == Cmd::uid() || !isset($params['uid']))
			{
				// 访问自己的主页
				Logic_Space_Home::init(Cmd::uid());
				$this->_forward('index', 'my');
			}
			else // 其他人访问
			{
				// 是否为有效uid
				if(!Logic_User_Base::check($params['uid']))
				{
					$this->_forward('error', 'error', 'public');
				}
				else 
				{
					Logic_Space_Home::init($params['uid']);
					// 获取该用户权限
					$access = Logic_User_Privacy::getAccess($params['uid']);
					switch ($access['vhome'])
					{
						case 0 : 
							$this->_forward('deny', 'error', 'public', 
											array(
												'position'=>'space_home',
												'uid'=>$params['uid']
											));
						break;
						case 1 : 
							// 判断访问用户是否为好友
							if(Logic_Space_Friends::hasFriend($params['uid'], Cmd::uid()) == 'pass')
							$this->_forward('index', 'user');
							else 
							$this->_forward('deny', 'error', 'public', 
											array(
												'position'=>'space_home',
												'uid'=>$params['uid']
											));
						break;
						case 2 : $this->_forward('index', 'user'); break;
					}
				}
			}
		}
	}

?>