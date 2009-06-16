<?php

	/**
	 * 群组数据过滤类
	 *
	 */
	class Filter_Group
	{
		/**
		 * 群账单数据过滤
		 *
		 * @param unknown_type $params
		 */
		public static function bill($params)
		{
			$params['handler'] = Alp_Valid::of($params['handler'], 'handler', '经手人', 'required');
			$params['sort'] = Alp_Valid::of($params['sort'], 'sort', '归类', 'required');
			$params['item'] = Alp_Valid::of($params['item'], 'item', '物资名称', 'trim|str_between[1,10]');
			$params['num'] = Alp_Valid::of($params['num'], 'num', '数额', 'trim|numeric');
			$params['memo'] = strip_tags(trim($params['memo']));
			$time = strtotime($params['time']);
			if(!$time) Alp_Sys::msg('time', '时间格式错误');
			$params['time'] = (int)$time;
			return $params;
		}
		
		/**
		 * 群信息
		 *
		 * @param unknown_type $params
		 */
		public static function msg($params)
		{
			$params['id'] = Alp_Valid::of($params['id'], 'id', '群组id', 'trim|numeric');
			$params['content'] = Alp_Valid::of($params['content'], 'content', '消息内容', 'trim|required');
			return $params;
		}
		
		/**
		 * 新群组创建数据过滤
		 *
		 */
		public static function create($params)
		{
			$params['name'] = Alp_Valid::of($params['name'], 'name', '群名称', 'trim|strip_tags|str_between[3,30]');
			$params['intro'] = Alp_Valid::of($params['intro'], 'intro', '群介绍', 'trim|strip_tags|required');
			$params['type'] = Alp_Valid::of($params['type'], 'type', '群类型', 'trim|required');
			return $params;
		}
		
		public static function basic($params)
		{
			$params = self::create($params);
			$params['notice'] = strip_tags(trim($params['notice']));
			return $params;
		}
	}

?>