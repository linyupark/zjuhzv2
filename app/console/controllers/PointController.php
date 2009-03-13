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
		 * 热心度增长排行
		 *
		 */
		function rankAction()
		{
			$range = $this->_getParam('range', 'week');
			$now = time();
			$this_year = date('Y', $now);
			$set_year = $this->_getParam('year', $this_year);
			if($set_year > $this_year) $set_year = $this_year;
			$db = DbModel::Log();
			
			$row = $db->fetchRow('SELECT MIN(`time`) AS `starttime` FROM `tb_point`');
			$point_start_time = $row['starttime']; // 开始有加分记录的时间为统计开始时间
			$start_year = date('Y', $point_start_time); // 开始计算年份

			switch ($range)
			{
				case 'year' : // 年
					$years = $set_year - $start_year;
					for ($i = 0; $i < $years; $i ++)
					{
						$start_count_time = strtotime($start_year+$i.'-1-1 00:00');
						if($set_year > $start_year) $span = $start_count_time + 31536000;
						else $span = $now;
						$row = $db->fetchRow('SELECT SUM(`point`) AS `points` FROM `tb_point` 
							WHERE `time` > '.$start_count_time.' AND `time` < '.$span);
						$data['points'][] = $row['points'];
						$rows = $db->fetchAll('SELECT u.username,pt.uid,SUM(pt.point) AS `pts` 
							FROM `tb_point` AS `pt`
							LEFT JOIN `zjuhzv2_user`.`tb_base` AS `u` ON u.uid = pt.uid 
							WHERE pt.time > '.$start_count_time.' AND pt.time < '.$span.' 
							GROUP BY pt.uid ORDER BY SUM(pt.point) DESC LIMIT 3');
						$data['rank'][$i] = $rows;
					}
				break;
				
				case 'month' : //月
					$div = strtotime($set_year.'-1-1 00:00');
					$months = floor(($now - $div)/2628000); // 总月数
					for ($i = 0; $i < $months; $i ++)
					{
						$span = $now - ($i+1)*2628000;
						$row = $db->fetchRow('SELECT SUM(`point`) AS `points` FROM `tb_point` 
							WHERE `time` > '.$span.' AND `time` < '.($span+2628000));
						$data['points'][] = $row['points'];
						$rows = $db->fetchAll('SELECT u.username,pt.uid,SUM(pt.point) AS `pts` 
							FROM `tb_point` AS `pt`
							LEFT JOIN `zjuhzv2_user`.`tb_base` AS `u` ON u.uid = pt.uid 
							WHERE pt.time > '.$span.' AND pt.time < '.($span+2628000).' 
							GROUP BY pt.uid ORDER BY SUM(pt.point) DESC LIMIT 3');
						$data['rank'][$i] = $rows;
					}
				break;
				
				case 'week' : // 周
					$div = strtotime($set_year.'-1-1 00:00');
					$weeks = floor(($now - $div)/604800); // 总星期数
					for ($i = 0; $i < $weeks; $i ++)
					{
						$span = $now - ($i+1)*604800;
						$row = $db->fetchRow('SELECT SUM(`point`) AS `points` FROM `tb_point` 
							WHERE `time` > '.$span.' AND `time` < '.($span+604800));
						$data['points'][] = $row['points'];
						$rows = $db->fetchAll('SELECT u.username,pt.uid,SUM(pt.point) AS `pts` 
							FROM `tb_point` AS `pt`
							LEFT JOIN `zjuhzv2_user`.`tb_base` AS `u` ON u.uid = pt.uid 
							WHERE pt.time > '.$span.' AND pt.time < '.($span+604800).' 
							GROUP BY pt.uid ORDER BY SUM(pt.point) DESC LIMIT 3');
						$data['rank'][$i] = $rows;
					}
				break;
				
				case 'day' : // 日
					$days = 10;
					$this->view->days = $days;
					for ($i = 0; $i < $days; $i ++)
					{
						$span = $now - ($i+1)*86400;
						$row = $db->fetchRow('SELECT SUM(`point`) AS `points` FROM `tb_point` 
							WHERE `time` > '.$span.' AND `time` < '.($span+86400));
						$data['points'][] = $row['points'];
						$rows = $db->fetchAll('SELECT u.username,pt.uid,SUM(pt.point) AS `pts` 
							FROM `tb_point` AS `pt`
							LEFT JOIN `zjuhzv2_user`.`tb_base` AS `u` ON u.uid = pt.uid 
							WHERE pt.time > '.$span.' AND pt.time < '.($span+86400).' 
							GROUP BY pt.uid ORDER BY SUM(pt.point) DESC LIMIT 3');
						$data['rank'][$i] = $rows;
					}
				break;
			}
			$this->view->data = $data;
			$this->view->range = $range;
		}
		
		/**
		 * 全站热心度记录，带回撤功能
		 *
		 */
		function logAction()
		{
			$page = $this->_getParam('p', 1);
			$data = Logic_Api::pointlog($page);
			if($data['numrows'] > $data['pagesize'])
			{
				Alp_Page::$pagesize = $data['pagesize'];
				Alp_Page::create(array(
					'href_open' => '<a href="/console/point/?tab=log&p=%d">',
					'href_close' => '</a>',
					'num_rows' => $data['numrows'],
					'cur_page' => $page
				));
				$this->view->pagination = Alp_Page::$page_str;
			}
			$this->view->rows = $data['rows'];
		}
		
		/**
		 * 删除记录以及加分
		 *
		 */
		function delAction()
		{
			if($this->getRequest()->isXmlHttpRequest())
			{
				$this->getHelper('viewRenderer')->setNoRender();
				$pid = $this->_getParam('pid');
				$log = DbModel::Log()->fetchRow('SELECT `point`,`gid`,`uid` 
					FROM `tb_point` WHERE `pid` = '.(int)$pid);
				if($log != false)
				{
					$point = $log['point'];
					if($log['uid'] != 0) // 用户分
					{
						DbModel::User()->update('tb_base', 
							array('point' => new Zend_Db_Expr('point - '.$point)), 'uid = '.$log['uid']);
					}
					if($log['gid'] != 0) // 群组分
					{
						DbModel::Space()->update('tb_group',
							array('point' => new Zend_Db_Expr('point - '.$point)), 'gid = '.$log['gid']);
					}
					DbModel::Log()->delete('tb_point', 'pid = '.$pid);
					echo 'success';
				}
			}
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
			$key_arr = explode(':', $key);
			$range = $key_arr[0]; $keys = explode(' ', $key_arr[1]);
			$select = DbModel::Space()->select();
			
			if($range == 'group') // 群内搜索
			{
				$select->from(array('g' => 'tb_group'),array('g.gid'))->where('g.name LIKE "%'.$keys[0].'%"');
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
					if($keys[1])
					{
						$user_like = '';
						foreach ($keys as $i => $u)
						{
							if($i != 0)
							{
								$or = count($keys) != ($i + 1) ? ' OR ' : '';
								$user_like .= 'u.username LIKE "%'.$u.'%"'.$or;
							}
						}
						$select->where($user_like);
					}
					$this->view->rows = $select->query()->fetchAll();
				}
			}
			
			if($range == 'name') // 只找人名
			{
				$user_like = '';
				foreach ($keys as $i => $u)
				{
					$or = count($keys) != ($i + 1) ? ' OR ' : '';
					$user_like .= 'username LIKE "%'.$u.'%"'.$or;
				}
				$this->view->rows = DbModel::User()->fetchAll('SELECT `uid`,`username` AS `uname`,`point` FROM `tb_base` 
						WHERE ('.$user_like.')');
			}
			
			if($range == 'events') // 活动
			{
				$select->from('tb_tbar', array('tid'))->where('title LIKE "%'.$keys[0].'%"')->order('pubtime DESC');
				$events = $select->query()->fetchAll();
				if(count($events)  > 0)
				{
					$tid = $events[0]['tid']; // 只选取第一个被选到的活动
					$select->reset();
					$select->from('tb_events', array('member'))->where('tid = '.$tid);
					$members = $select->query()->fetchAll();
					$members = unserialize($members[0]['member']);
					if(count($members) > 0)
					$in_uids = '';
					foreach ($members as $uid => $m) $in_uids .= $uid.',';
					$in_uids = substr($in_uids, 0, -1);
					$select->reset()->from(array('u' => 'zjuhzv2_user.tb_base'), 
						array('point', 'uname' => 'u.username'))
						->where('u.uid IN ('.$in_uids.')');
				}
				// 有名字模糊查询
				if($keys[1])
				{
					$user_like = '';
					foreach ($keys as $i => $u)
					{
						if($i != 0)
						{
							$or = count($keys) != ($i + 1) ? ' OR ' : '';
							$user_like .= 'u.username LIKE "%'.$u.'%"'.$or;
						}
					}
					$select->where($user_like);
				}
				$this->view->rows = $select->query()->fetchAll();
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