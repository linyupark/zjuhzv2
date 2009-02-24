<?php

	/**
	 * 记录一些通用数据变动记录
	 *
	 */
	class Logic_Log extends DbModel 
	{
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
				'tid' => $params['tid'],
				'key' => $params['key'],
				'value' => $params['value'],
				'time' => time()
			));
		}
	}

?>