<?php

	/**
	 * 群组数据过滤类
	 *
	 */
	class Filter_Group
	{
		/**
		 * 新群组创建数据过滤
		 *
		 */
		public static function create($params)
		{
			$params['name'] = Alp_Valid::of($params['name'], 'name', '群名称', 'trim|strip_tags|str_between[3,30]');
			$params['intro'] = Alp_Valid::of($params['intro'], 'content', '群介绍', 'trim|required');
			$params['type'] = Alp_Valid::of($params['type'], 'content', '群类型', 'trim|required');
			return $params;
		}
	}

?>