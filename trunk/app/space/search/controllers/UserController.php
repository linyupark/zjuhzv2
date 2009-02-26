<?php

	/**
	 * 用户搜索控制器
	 *
	 */
	class Space_Search_UserController extends Zend_Controller_Action 
	{	
		function init()
		{
			// 成员，权力成员，管理员才能搜索用户
			$role = Cmd::role();
			if($role != 'member' && $role != 'power' && $role != 'master')
			{
				$this->_redirect('/public/error/deny/?position=deny');
			}
		}
		
		function indexAction()
		{
			$params = $this->getRequest()->getParams();
			$attr = $this->_getParam('attr', 'username');
			$range = $this->_getParam('range', 'all'); // 查找范围
			$key = trim(urldecode($params['key']));
			$page = $this->_getParam('p', 1);
			$pagesize = 20;
			$uid = Cmd::uid();
			
			$select = DbModel::User()->select();
			$select->from(array('ub'=>'tb_base'), 
					          array('numrows' => new Zend_Db_Expr('COUNT(ub.uid)')));
			
			// 好友范围
			if($range == 'friend')
			{
				 $friends = Logic_Space_Friends::ids($uid);
				 $in = '';
				 if(count($friends) > 0)
				 {
				 	foreach ($friends as $f)
					 {
					 	$in .= $f['friend'].',';
					 }
					 $select->where('ub.uid IN ('.substr($in,0,-1).')');
				 }
				 else $select->where('ub.uid = 0');
			}
			
			// 在线范围
			if($range == 'online')
			{
				 $users = Logic_Api::online();
				 $in = '';
				 foreach ($users as $u)
				 {
				 	$in .= $u['uid'].',';
				 }
				 $select->where('ub.uid IN ('.substr($in,0,-1).')');
			}
			
			// 搜索属性
			switch ($attr)
			{
				case 'campus' : // 院系
					$select->joinLeft(array('ue' => 'tb_edu'), 'ue.uid = ub.uid');
					if(!empty($key))
					$select->where('ue.campus LIKE "%'.$key.'%"');
					$select->group('ue.uid');
				break;
				case 'interest' : // 兴趣
					$select->joinLeft(array('ui' => 'tb_intro'), 'ui.uid = ub.uid', array('intro' => 'ui.intro'));
					if(!empty($key))
					$select->where('ui.interest LIKE "%'.$key.'%"');
					$select->group('ui.uid');
				break;
				default : // 姓名
					if(!empty($key))
					$select->where('ub.username LIKE "%'.$key.'%"');
				break;
			}
			
			$row = $select->query()->fetchAll();
			$numrows = $row[0]['numrows'] ? $row[0]['numrows'] : 0;
			
			$select->reset(Zend_Db_Select::COLUMNS)->columns('*');
			
			// 分页
			if($numrows > $pagesize)
			{
				Alp_Page::$pagesize = $pagesize;
				Alp_Page::create(array(
					'href_open' => '<a href="">',
					'href_close' => '</a>',
					'num_rows' => $numrows,
					'cur_page' => $page
				));
				$select->limit($pagesize, Alp_Page::$offset);
				$this->view->pagination = Alp_Page::$page_str;
			}
			
			$this->view->roles = Zend_Registry::get('config')->user_role->toArray();
			$this->view->rows = $select->query()->fetchAll();
			$this->view->numrows = $numrows;
			$this->view->attr = $attr;
			$this->view->range = $range;
		}
	}

?>