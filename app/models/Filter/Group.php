<?php

	/**
	 * 群组数据过滤类
	 *
	 */
	class Filter_Group
	{
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