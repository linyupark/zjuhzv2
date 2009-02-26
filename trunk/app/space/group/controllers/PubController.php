<?php

	/**
	 * 群组创建控制器
	 *
	 */
	class Space_Group_PubController extends Zend_Controller_Action 
	{
		function init()
		{
			// 成员，权力成员，管理员才能建立群组
			$role = Cmd::role();
			if($role != 'member' && $role != 'power' && $role != 'master')
			{
				$this->_redirect('/public/error/deny/?position=deny');
			}
		}
		
		/**
		 * 创建导航页
		 *
		 */
		function indexAction()
		{
			$type = $this->_getParam('type');
			if($type)
			{
				$this->view->type= $this->_getParam('type');
				$this->render('form');
			}
			else $this->render('index');
		}
		
		/**
		 * 创建群组俱乐部
		 *
		 */
		function createAction()
		{
			$uid = Cmd::uid();
			$params = $this->_getAllParams();
			if($this->getRequest()->isXmlHttpRequest())
			{
				$this->getHelper('viewRenderer')->setNoRender();
				$params = Filter_Group::create($params);
				if(Alp_Sys::getMsg() == null)
				{
					$gid = Logic_Space_Group::create($params, $uid);
					if(Alp_Sys::getMsg() == null)
					{
						// 记录
						Logic_Log::group(array(
							'uid' => $uid,
							'gid' => $gid,
							'tid' => 0,
							'key' => 'add_group',
						));
						echo Zend_Json::encode(array('result'=>'success','gid'=>$gid));
						exit();
					}
				}
				echo Zend_Json::encode(array('result'=>Alp_Sys::allMsg('* ',"\n")));
			}
		}
	}

?>