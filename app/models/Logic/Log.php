<?php

	/**
	 * 记录一些通用数据变动记录
	 *
	 */
	class Logic_Log extends DbModel 
	{
		/**
		 * 用户申请加分
		 * */
		public static function apoint($params)
		{
			parent::Log()->insert('tb_apoint',
			array(
				'uid' => $params['uid'],
				'auid' => $params['auid'],
				'memo' => $params['memo'],
				'time' => time(),
				'point' => $params['point'],
			));
		}
		
		/**
		 * 用户首页显示动态信息
		 *
		 */
		public static function home($uid, $limit = 20)
		{
			$select = parent::Log()->select();
			$select->from(array('e' => 'tb_event'));
			
			// 在好友uid里选择
			$friends = Logic_Space_Friends::ids($uid);
			$friend = '0,';
			if(count($friends) > 0)
			{
				foreach ($friends as $f)
				{
					$friend .= $f['friend'].',';
				}
			}
			// 在uid加入的群组里选择
			$groups = Logic_Space_Group::ids($uid);
			$group = '';
			if(count($groups) > 0)
			{
				foreach ($groups as $g)
				{
					$group .= $g['gid'].',';
				}
			}
			// 在我发布过的tid中选择
			/*
			$bars = Logic_Space_Bar::ids($uid);
			$bar = '';
			if(count($bars) > 0)
			{
				foreach ($bars as $b)
				{
					$bar .= $b['tid'].',';
				}
			}*/
			// 开始筛选
			$select->where('e.uid IN ('.substr($friend,0,-1).') AND e.fid != '.$uid);
			if($group != '') $select->orWhere('e.gid IN ('.substr($group,0,-1).') AND e.uid != '.$uid);
			
			$select->joinLeft(array('u' => 'zjuhzv2_user.tb_base'), 'u.uid = e.uid', 
							  array('uname'=>'u.username'));
			$select->joinLeft(array('u2' => 'zjuhzv2_user.tb_base'), 'u2.uid = e.fid', 
							  array('fname'=>'u2.username'));
			$select->joinLeft(array('bar' => 'zjuhzv2_space.tb_tbar'), 'bar.tid = e.tid', 
							  array('title'=>'bar.title','type'=>'bar.type','reply'=>'bar.reply'));
			$select->joinLeft(array('g' => 'zjuhzv2_space.tb_group'), 'g.gid = e.gid', 
							  array('gname'=>'g.name'));
							  
			$select->order('time DESC')->limit($limit);
			
			return $select->query()->fetchAll();
		}
		
		/**
		 * 跟用户数据库有关的事件记录
		 *
		 * @param unknown_type $params
		 */
		public static function user($params)
		{
			parent::Log()->insert('tb_event', array(
				'uid' => $params['uid'],
				'fid' => $params['fid'],
				'key' => $params['key'],
				'value' => $params['value'],
				'time' => time()
			));
		}
		
		/**
		 * 跟帖子有关的事件记录
		 *
		 * @param unknown_type $params
		 */
		public static function bar($params)
		{
			parent::Log()->insert('tb_event', array(
				'uid' => $params['uid'],
				'gid' => $params['gid'],
				'tid' => $params['tid'],
				'key' => $params['key'],
				'value' => $params['value'],
				'time' => time()
			));
		}
		
		/**
		 * 跟群组有关的事件记录
		 *
		 * @param unknown_type $params
		 */
		public static function group($params)
		{
			parent::Log()->insert('tb_event', array(
				'uid' => $params['uid'],
				'gid' => $params['gid'],
				'key' => $params['key'],
				'time' => time()
			));
		}
	}

?>