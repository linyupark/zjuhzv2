<?php

	/**
	 * 用户管理
	 *
	 */
	class Console_UserController extends Zend_Controller_Action
	{
		/**
		 * 数据导出
		 *
		 */
		function exportAction()
		{
			$flag = $this->_getParam('flag');
			$select = DbModel::User()->select();
			$select->from('tb_base', array('uid'));
			switch ($flag)
			{
				case 1 : // 非冻结
					$select->where('role != "black"');
				break;
				case 2 : // 已审核校友
					$select->where('role != "bench" AND role != "black"');
				break;
				case 3 : // 成员
					$select->where('role = "power"');
				break;
			}
			$uids = $select->query()->fetchAll();
			$this->_forward('user', 'excel', 'api_export', array('uids' => $uids));
		}
		
		/**
		 * 初始用户密码
		 *
		 */
		function initpswAction()
		{
			$params = $this->getRequest()->getParams();
			if($params['psw'])
			{
				$this->getHelper('viewRenderer')->setNoRender();
				$params['password'] = trim($params['psw']);
				Logic_User_Password::update($params, $params['uid']);
				
				// v1版本初始化(附加)
				DbModel::getAdapter('zjuhz_user')->update('tbl_user', array(
					'password' => md5($params['password'])
				), 'uid = '.$params['uid']);
				
				if(Alp_Sys::getMsg() == null)
				echo 'success';
				else echo Alp_Sys::msg('exception');
			}
			else
			{
				$this->view->uid = $params['uid'];
			}
		}
		
		/**
		 * 改变用户身份
		 *
		 */
		function croleAction()
		{
			$this->getHelper('viewRenderer')->setNoRender();
			if($this->getRequest()->isXmlHttpRequest())
			{
				$params = $this->getRequest()->getParams();
				Logic_User_Base::crole($params['uid'], $params['role']);
			}
		}
		
		function chonorAction()
		{
			$this->getHelper('viewRenderer')->setNoRender();
			if($this->getRequest()->isXmlHttpRequest())
			{
				$params = $this->getRequest()->getParams();
				Logic_User_Base::chonor($params['uid'], trim($params['honor']));
			}
		}
		
		/**
		 * 用户详细信息
		 *
		 */
		function detailAction()
		{
			if($this->getRequest()->isXmlHttpRequest())
			{
				$uid = $this->getRequest()->getPost('uid');
				$this->view->data = Logic_Api::user($uid);
			}
		}
		
		function indexAction()
		{
			$params = $this->getRequest()->getParams();
			$range = $params['range'] ? $params['range'] : 'bench';
			$key = trim(urldecode($params['key']));
			$pagesize = 10;
			$page = $params['p'] ? $params['p'] : 1;
			
			$select = DbModel::User()->select();
			$select->from(array('ub'=>'tb_base'), 
					          array('numrows' => new Zend_Db_Expr('COUNT(ub.uid)')));
					          
			// 被冻结用户
			if($range == 'black') $select->where('ub.role = ?', 'black');
			
			// 等待审核
			if($range == 'bench') $select->where('ub.role = ?', 'bench');
			
			// 审核通过的校友
			if($range == 'member') $select->where('ub.role = ?', 'member');
			
			// 校友会成员组power用户
			if($range == 'power') $select->where('ub.role = ?', 'power');
			
			// 管理员
			if($range == 'master') $select->where('ub.role = ?', 'master');
			
			// 搜索姓名
			if(!empty($key)) $select->where('ub.username LIKE "%'.$key.'%" 
				OR ub.account LIKE "%'.$key.'%" 
				OR ub.nickname LIKE "%'.$key.'%"');
			
			$row = $select->query()->fetchAll();
			$numrows = $row[0]['numrows'] ? $row[0]['numrows'] : 0;
			
			$select->reset(Zend_Db_Select::COLUMNS)->columns('*');
			
			// 分页
			if($numrows > $pagesize)
			{
				Alp_Page::$pagesize = $pagesize;
				Alp_Page::create(array(
					'href_open' => '<a href="/console/user/?range='.$range.'&key='.urlencode($key).'&p=%d">',
					'href_close' => '</a>',
					'num_rows' => $numrows,
					'cur_page' => $page
				));
				$select->limit($pagesize, Alp_Page::$offset);
				$this->view->pagination = Alp_Page::$page_str;
			}
			$select->order('lastlogin DESC');
			$this->view->rows = $select->query()->fetchAll();
			$this->view->numrows = $numrows;
			$this->view->range = $range;
			$this->view->key = $key;
		}
		
		function toolbarAction()
		{
			$row = $this->_getParam('row');
			$this->view->row = $row;
			$this->view->roles = Zend_Registry::get('config')->user_role->toArray();
			$this->render('crole');
		}
	}
?>