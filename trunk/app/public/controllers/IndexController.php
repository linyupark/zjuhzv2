<?php

	/**
	 * 网站首页控制器
	 *
	 */
	class IndexController extends Zend_Controller_Action 
	{
		function init()
		{
			$this->view->icons = Zend_Registry::get('config')->bar_icon->toArray();
		}
		
		/**
		 * 好友关系数据迁移
		 *
		 */
		function friAction()
		{
			set_time_limit(99999999);
			$this->getHelper('viewRenderer')->setNoRender();
			/*
			$rows = Cmdv1::friends();
			foreach ($rows as $r)
			{
				$uid = $r['uid'];
				$friends = explode(',', $r['friends']);
				if(Logic_User_Base::check($uid) != false)
				foreach ($friends as $f)
				{
					if(Logic_User_Base::check($f) != false)
					Logic_Space_Friends::rel($uid, $f);
				}
			}*/
		}
		
		/**
		 * 群组数据迁移
		 *
		 */
		function mvgroupAction()
		{
			$this->getHelper('viewRenderer')->setNoRender();
			/*
			$gid = $this->_getParam('gid');
			if($gid)
			{
				$data = Cmdv1::group($gid);
				$group = $data['group']; $member = $data['member'];
				$db = DbModel::Space();
				$db->insert('tb_group', array(
					'name' => $group['name'],
					'createtime' => $group['create_time'],
					'intro' => $group['intro'],
					'point' => 0,
					'type' => 'open'
				));
				$n_gid = $db->lastInsertId();
				$db->insert('tb_group_member', array(
					'uid' => $group['creater'],
					'gid' => $n_gid,
					'role' => 'creater',
					'jointime' => $group['create_time']
				));
				foreach ($member as $m)
				{
					if($m['user_id'] != $group['creater'])
					$db->insert('tb_group_member', array(
						'uid' => $m['user_id'],
						'gid' => $n_gid,
						'role' => 'member',
						'jointime' => $m['join_time'],
						'lastvisit' => $m['last_access']
					));
				}
			}*/
		}
		
		function v1tov2Action()
		{
			set_time_limit(99999999);
			$this->getHelper('viewRenderer')->setNoRender();
			/*
			$v2db = DbModel::User();
			$v1users = Cmdv1::alluser();
			
			foreach ($v1users as $u)
			{
				if(Logic_User_Reg::isRegistered('email', $u['eMail'])) continue;
				$role = $u['point'] == 0 ? 'bench' : 'member';
				$regtime = strtotime($u['regTime']);
				$bday = ''; $byear = ''; $bmon = '';
				if($u['point'] == null) $u['point'] = 0;
				if($u['birthday'] != null) // 处理生日
				{
					$time = strtotime($u['birthday']);
					$byear = date('y', $time);
					$bmon = date('m', $time);
					$bday = date('d', $time);
				}
				// 基础数据
				$v2db->insert('tb_base', 
					array(
						'uid' => $u['userid'],
						'account'=>$u['username'],
						'password'=>$u['password'],
						'username'=>$u['realName'],
						'sex' => $u['sex'],
						'birthyear' => $byear,
						'birthmonth' => $bmon,
						'birthday' => $bday,
						'hometown' => $u['hometown_c'],
						'city' => $u['location_c'],
						'regtime' => $regtime,
						'role' => $role,
						'point' => $u['point']
					)
				);
				// 不许空邮箱地址
				if($u['eMail'] == null) $u['eMail'] = $u['username'].'@163.com';
				$v2db->insert('tb_contact', 
					array(
						'uid' => $u['userid'],
						'email' => $u['eMail'],
						'mobile' => $u['mobile'],
						'qq' => $u['qq'],
						'msn' => $u['msn'],
						'address' => $u['address'],
						'zipcode' => $u['postcode'],
						'tel' => $u['phone']
					)
				);
				if($u['point'] > 0)
				$v2db->insert('zjuhzv2_log.tb_point', 
					array(
						'uid' => $u['userid'],
						'memo' => '热心度系统数据迁移累计加分，如需要回溯加分详情可咨询校友会',
						'time' => time(),
						'handler' => 0
					)
				);
			}
			//Zend_Debug::dump($v1users);
			echo 'done';*/
		}
		
		function indexAction()
		{
			
		}
		
		/**
		 * 一般静态文档
		 *
		 */
		function docAction()
		{
			$this->view->of = $this->_getParam('of');
		}
		
		/**
		 * 网站头版新闻编辑
		 *
		 */
		function ftpageAction()
		{
			$this->view->html = file_get_contents(HTMLROOT.'/player/ftpage.html');
		}
		
		/**
		 * 快速登陆显示
		 *
		 */
		function fastloginAction(){}
		
		/**
		 * 群组首页展示
		 *
		 */
		function groupAction()
		{
			$rows = DbModel::Space()->fetchAll('SELECT * FROM `tb_group`
				WHERE `type` != "close" 
				ORDER BY `point` DESC LIMIT 6');
			$this->view->groups = $rows;
		}
		
		/**
		 * 热心度排行
		 *
		 */
		function rankAction()
		{
			$rows = Logic_Api::pointrank(array(2,3,4,5,6,41,493,488,24,35,20,89,7,1075,1130), 3);
			$this->view->rows = $rows;
		}
		
		/**
		 * 近期活动
		 *
		 */
		function eventsAction()
		{
			$now = time();
			$rows = DbModel::Space()->fetchAll('SELECT bar.*,e.`time` AS `starttime` 
				FROM `tb_tbar` AS `bar` 
				LEFT JOIN `tb_group` AS `g` ON g.`gid` = bar.`group` 
				LEFT JOIN `tb_events` AS `e` ON e.`tid` = bar.`tid` 
				WHERE (
					bar.`private` IN(2,3,4) 
					AND bar.`type` = "events" 
					AND e.`time` > '.$now.'  
					AND (g.`type` IS NULL OR g.`type` != "close")
				)  
				ORDER BY e.`time` ASC LIMIT 5');
			$this->view->events = $rows;
		}
		
		/**
		 * 最新投票
		 *
		 */
		function voteAction()
		{
			$rows = DbModel::Space()->fetchAll('SELECT bar.*  
				FROM `tb_tbar` AS `bar` 
				WHERE (
					bar.`private` IN(3,4) 
					AND bar.`group` = 0 
					AND bar.`type` = "vote"
				) 
				ORDER BY bar.`replytime` DESC LIMIT 3');
			$this->view->bars = $rows;
		}
		
		/**
		 * 群组顶帖
		 *
		 */
		function groupbarsAction()
		{
			$rows = DbModel::Space()->fetchAll('SELECT bar.*,g.`name` AS `gname` 
				FROM `tb_tbar` AS `bar` 
				LEFT JOIN `tb_group` AS `g` ON g.`gid` = bar.`group` 
				WHERE (
					bar.`private` IN(3,4) 
					AND bar.`group` != 0 
					AND bar.`deny` != 1 
					AND bar.`type` NOT IN ("events","vote") 
				) 
				ORDER BY bar.`replytime` DESC LIMIT 8');
			$this->view->bars = $rows;
		}
		
		/**
		 * 公开话题
		 *
		 */
		function barsAction()
		{
			$rows = DbModel::Space()->fetchAll('SELECT bar.*  
				FROM `tb_tbar` AS `bar` 
				WHERE (
					bar.`private` IN(2,3,4) 
					AND bar.`group` = 0 
					AND bar.`type` IN ("topic","photo","share","video") 
				) 
				ORDER BY bar.`pubtime` DESC LIMIT 8');
			$this->view->bars = $rows;
		}
		
		/**
		 * 新闻显示
		 *
		 */
		function newsAction()
		{
			$rows = DbModel::Space()->fetchAll('SELECT bar.*,s.`name`,s.`sort` 
				FROM `tb_tbar` AS `bar` 
				LEFT JOIN `tb_news` AS `news` ON news.`tid` = bar.`tid` 
				LEFT JOIN `tb_news_sort` AS `s` ON s.`sort` = news.`sort` 
				WHERE bar.`type` = "news" AND bar.`private` >= 3 
				ORDER BY bar.`ding`DESC,bar.`pubtime` DESC LIMIT 8');
			$this->view->news = $rows;
		}
	}

?>