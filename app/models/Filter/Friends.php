<?php

	/**
	 * 好友数据过滤
	 *
	 */
	class Filter_Friends
	{
		/**
		 * 好友分组数据过滤
		 *
		 */
		public static function sort($params)
		{
			$params['uid'] = Cmd::uid();
			$params['val'] = Alp_Valid::of($params['val'], 'val', '分组内容', 'trim|str_between[1,6]');
			return $params;
		}
		
		/**
		 * 拒绝好友请求
		 *
		 * @param unknown_type $params
		 */
		public static function reject($params)
		{
			$params['sender'] = Alp_Valid::of($params['sender'], 'sender', '请求人ID', 'trim|numeric');
			$params['uid'] = Alp_Valid::of(Cmd::uid(), 'uid', '我的ID', 'trim|numeric');
			return $params;
		}
		
		/**
		 * 通过好友请求
		 *
		 * @param unknown_type $params
		 */
		public static function pass($params)
		{
			$params['sender'] = Alp_Valid::of($params['sender'], 'sender', '请求人ID', 'trim|numeric');
			$params['uid'] = Alp_Valid::of(Cmd::uid(), 'uid', '我的ID', 'trim|numeric');
			$params['sort'] = Alp_Valid::of($params['sort'], 'sort', '好友归类', 'trim|numeric');
			$params['time'] = time();
			return $params;
		}
		
		/**
		 * 请求加为好友
		 *
		 * @param unknown_type $params
		 * @return unknown
		 */
		public static function add($params)
		{
			$params['incept'] = Alp_Valid::of($params['incept'], 'incept', '请求加为好友的ID', 'trim|numeric');
			$params['sender'] = Alp_Valid::of($params['sender'], 'sender', '请求人ID', 'trim|numeric');
			$params['type'] = 'friend';
			$params['content'] = Alp_Valid::of($params['content'], 'content', '附加信息', 'trim|required');
			$params['time'] = time();
			return $params;
		}
	}

?>