<?php
	
	/**
	 * 加为好友控制器
	 *
	 */
	class Space_Friends_AddController extends Zend_Controller_Action 
	{
		/**
		 * 提交好友请求表单
		 *
		 */
		function submitAction()
		{
			$this->getHelper('viewRenderer')->setNoRender();
			$params = $this->_getAllParams();
			$params['sender'] = Cmd::uid();
			$params = Filter_Friends::add($params);
			if(Alp_Sys::getMsg() == null)
			{
				Logic_Space_Friends::add($params);
				if(Alp_Sys::getMsg() == null)
				{
					echo 'success';
					exit();
				}
			}
			echo Alp_Sys::allMsg('* ', "\n");
		}
		
		/**
		 * 加为好友请求后的反馈
		 *
		 */
		function indexAction()
		{
			$uid = $this->_getParam('uid');
			$myid = Cmd::uid();
			if($myid == $uid)
			{
				$this->render('passed'); // 排除自己加自己
			}
			else
			{
				switch (Logic_Space_Friends::hasFriend($myid, $uid))
				{
					case 'wait' : // 已经申请加为好友，待批准
						$this->render('wait');
					break;
					case 'pass' : // 已经是好友
						$this->render('passed');
					break;
					case 'block' : // 是黑名单人员
						Logic_Space_Friends::unblock($myid, $uid);
						$this->render('unblock');
					break;
					default : // 进行申请成为好友处理
						if(Logic_Space_Friends::hasFriend($uid, $myid) == 'block')
						$this->render('deny');
						else 
						{
							// 请求表单
							$this->view->incept = Logic_User_Base::get($uid);
							$this->render('form');
						}
					break;
				}
			}
		}
	}

?>