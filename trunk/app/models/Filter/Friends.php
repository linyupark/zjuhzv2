<?php

	/**
	 * 好友数据过滤
	 *
	 */
	class Filter_Friends
	{
		/**
		 * 请求加为好友
		 *
		 * @param unknown_type $params
		 * @return unknown
		 */
		public static function add($params)
		{
			$params['incept'] = Alp_Valid::of($params['incept'], 'incept', '请求加为好友的ID', 'required|numeric');
			$params['sender'] = Alp_Valid::of($params['sender'], 'sender', '请求人ID', 'required|numeric');
			$params['type'] = 'friend';
			$params['content'] = trim($params['content']);
			$params['time'] = time();
			return $params;
		}
	}

?>