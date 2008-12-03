<?php

	/**
	 * 互动空间数据库的数据过滤
	 *
	 */
	class Filter_Space
	{
		public static function bar($params)
		{
			$params['title'] = Alp_Valid::of($params['title'], 'title', '标题', 'trim|strip_tags|str_between[3,90]');
			$params['type'] = Alp_Valid::of($params['type'], 'type', '帖子类型', 'trim|required');
			$params['private'] = Alp_Valid::of($params['private'], 'private', '帖子开放权限', 'trim|required');
			return $params;
		}
		
		/**
		 * 新闻帖过滤
		 *
		 * @param unknown_type $params
		 * @return unknown
		 */
		public static function news($params)
		{
			$params = self::bar($params);
			$params['content'] = Alp_Valid::of($params['content'], 'content', '内容', 'trim|required');
			$params['sort'] = Alp_Valid::of($params['sort'], 'sort', '归类', 'trim|required');
			$tags = array();
			foreach($params['tags'] as $v)
			{
				$temp = strip_tags(trim($v));
				if(!empty($temp)) $tags[] = $temp;
			}
			if(count($tags) == 0) Alp_Sys::msg('tags', '关键字不能为空，请填写新闻相关的关键字');
			$params['tags'] = $tags;
			return $params;
		}
		
		/**
		 * 新闻分类过滤
		 *
		 * @param unknown_type $sort
		 * @return unknown
		 */
		public static function newsSort($sortname)
		{
			return Alp_Valid::of($sortname, 'sortname', '新闻分类', 'trim|strip_tags|str_between[2,30]');
		}
		
		/**
		 * 话题帖过滤
		 *
		 * @param unknown_type $params
		 */
		public static function topic($params)
		{
			$params = self::bar($params);
			$params['content'] = Alp_Valid::of($params['content'], 'content', '内容', 'trim|required');
			return $params;
		}
	}

?>