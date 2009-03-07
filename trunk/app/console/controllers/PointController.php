<?php

	/**
	 * 热度管理控制器⑦
	 *
	 */
	class Console_PointController extends Zend_Controller_Action
	{
		/**
		 * tab切换控制
		 *
		 */
		function indexAction()
		{
			$this->view->tab = $this->_getParam('tab', 'user');
		}
		
		/**
		 * 群组/用户加分
		 *
		 */
		function addAction()
		{
			if($this->getRequest()->isXmlHttpRequest())
			{
				$for = $this->_getParam('for');
				$uids = $this->_getParam('uid');
				$point = (int)$this->_getParam('point');
				$memo = $this->_getParam('memo');
				$time = strtotime($this->_getParam('time'));
				
				if($point && $memo)
				{
					$this->getHelper('viewRenderer')->setNoRender();
					if($point == 0 || $time == false)
					{
						echo '分数/时间格式有错误数据，请检查表单';
						exit();
					}
					if($for == 'user')
					foreach ($uids as $uid)
					{
						Logic_Api::apoint('user', $uid, $point, $memo, $time);
					}
					
					echo 'success';
				}
				else
				{
					$this->view->for = $for;
					$this->view->uids = $uids;
					$this->render('addpointform');
				}
			}
		}
		
		/**
		 * 用户热度操作
		 *
		 */
		function userAction()
		{
			$key = trim(urldecode($this->_getParam('key')));
			$matches = array(); // 正则匹配
			preg_match("/\[(.*)\](.*)/", $key, $matches);
			
			if($matches[1]) // 有群关键字
			{
				$select = DbModel::Space()->select();
				$select->from(array('g' => 'tb_group'),array('g.gid'))->where('g.name LIKE "%'.$matches[1].'%"');
				$groups = $select->query()->fetchAll();
				
				if(count($groups) > 0)
				{
					$gids = '';
					foreach ($groups as $g) $gids = $g['gid'].',';
					$in_gids = substr($gids, 0, -1);
					
					$select->reset();
					$select->from(array('gm' => 'tb_group_member'))
						   ->joinLeft(array('g' => 'tb_group'), 'g.gid = gm.gid', array('gname' => 'g.name'))
						   ->joinLeft(array('u' => 'zjuhzv2_user.tb_base'), 'u.uid = gm.uid', 
						   		array('uname' => 'u.username', 'point' => 'u.point'))
						   ->where('gm.gid IN ('.$in_gids.')')->where('gm.role IN ("member","manager","creater")');
					
					// 有名字模糊查询
					$user_like = '';
					if($matches[2])
					{
						$users = explode('@', $matches[2]);
						foreach ($users as $i => $u)
						{
							$or = count($users) != ($i + 1) ? ' OR ' : '';
							$user_like .= 'u.username LIKE "%'.$u.'%"'.$or;
						}
						$select->where($user_like);
					}
					$this->view->rows = $select->query()->fetchAll();
				}
			}
			else // 只找人
			{
				if(!empty($key))
				{
					$users = explode('@', $key);
					foreach ($users as $i => $u)
					{
						$or = count($users) != ($i + 1) ? ' OR ' : '';
						$user_like .= 'username LIKE "%'.$u.'%"'.$or;
					}
					$this->view->rows = DbModel::User()->fetchAll('SELECT `uid`,`username` AS `uname`,`point` FROM `tb_base` 
						WHERE ('.$user_like.')');
				}
			}
			$this->view->key = $key;
		}
		
		/**
		 * 群组热度操作
		 *
		 */
		function groupAction()
		{
			
		}
	}
?>