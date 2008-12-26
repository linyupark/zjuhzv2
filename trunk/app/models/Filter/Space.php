<?php

	/**
	 * 互动空间数据库的数据过滤
	 *
	 */
	class Filter_Space
	{
		/**
		 * 评论
		 *
		 * @param unknown_type $params
		 * @return unknown
		 */
		public static function comment($params)
		{
			$params['tid'] = Alp_Valid::of($params['tid'], 'tid', '指定帖子', 'trim|required');
			$params['content'] = Alp_Valid::of($params['content'], 'content', '内容', 'trim|required');
			return $params;
		}
		
		public static function bar($params)
		{
			$params['title'] = Alp_Valid::of($params['title'], 'title', '标题主题', 'trim|strip_tags|str_between[3,90]');
			if(Logic_Space_Bar::unique($params['title']) != false)
			Alp_Sys::msg('title', '您使用的标题或主题已经存在，请更换');
			$params['type'] = Alp_Valid::of($params['type'], 'type', '帖子类型', 'trim|required');
			$params['private'] = Alp_Valid::of($params['private'], 'private', '帖子开放权限', 'trim|required');
			return $params;
		}
		
		/**
		 * 图片帖过滤
		 *
		 * @param unknown_type $params
		 * @return unknown
		 */
		public static function photo($params)
		{
			$params['title'] = Alp_Valid::of($params['title'], 'title', '标题', 'trim|strip_tags|str_between[3,90]');
			if(Logic_Space_Bar::unique($params['title']) != false)
			Alp_Sys::msg('title', '您使用的标题已经存在，请更换');
			if(count($params['photos']) == 0) Alp_Sys::msg('files', '请上传要展示的图片文件');
			return $params;
		}
		
		/**
		 * 文件共享帖过滤
		 *
		 * @param unknown_type $params
		 * @return unknown
		 */
		public static function share($params)
		{
			$params['title'] = Alp_Valid::of($params['title'], 'title', '主题', 'trim|strip_tags|str_between[3,90]');
			if(Logic_Space_Bar::unique($params['title']) != false)
			Alp_Sys::msg('title', '您使用的标题已经存在，请更换');
			if(count($params['files']) == 0) Alp_Sys::msg('files', '请上传想要共享的文件');
			return $params;
		}
		
		/**
		 * 投票帖过滤
		 *
		 * @param unknown_type $params
		 * @return unknown
		 */
		public static function vote($params)
		{
			if(Logic_Space_Bar::unique($params['title']) != false)
			Alp_Sys::msg('title', '您使用的主题已经存在，请更换');
			$params['title'] = Alp_Valid::of($params['title'], 'title', '主题', 'trim|strip_tags|str_between[3,90]');
			$options = array();
			$rates = array();
			foreach ($params['options'] as $opt)
			{
				$value = strip_tags(trim($opt));
				if(!empty($value))
				{
					$options[] = $value;
					$rates[] = 0;
				}
			}
			if(count($options) < 2)
				Alp_Sys::msg('options', '可投选项内容不得少于2项');
			if($params['maxselect'] >= count($options))
				Alp_Sys::msg('maxselect', '可选数不得大于等于选项总数');
			$params['memo'] = trim($params['memo']);
			$params['options'] = $options;
			$params['rates'] = $rates;
			return $params;
		}
		
		/**
		 * 活动贴过滤
		 *
		 * @param unknown_type $params
		 */
		public static function events($params)
		{
			$params = self::bar($params);
			$params['content'] = Alp_Valid::of($params['content'], 'content', '内容', 'trim|required');
			if(!empty($params['limit']))
			$params['limit'] = Alp_Valid::of($params['limit'], 'limit', '人数限制', 'trim|numeric');
			else $params['limit'] = null;
			$params['year'] = Alp_Valid::of($params['year'], 'year', '年份', 'trim|numeric');
			$params['month'] = Alp_Valid::of($params['month'], 'month', '月份', 'trim|numeric');
			$params['day'] = Alp_Valid::of($params['day'], 'day', '日', 'trim|numeric');
			$params['hour'] = Alp_Valid::of($params['hour'], 'hour', '小时', 'trim|numeric');
			$params['min'] = Alp_Valid::of($params['min'], 'min', '分钟', 'trim|numeric');
			$time = strtotime($params['year'].'-'.$params['month'].'-'.$params['day'].' '.$params['hour'].':'.$params['min']);
			if(!$time) Alp_Sys::msg('time', '活动时间无效，请检查格式');
			else $params['time'] = $time;
			$params['address'] = Alp_Valid::of($params['address'], 'address', '活动地址', 'trim|required');
			return $params;
		}
		
		/**
		 * 求助帖过滤
		 *
		 * @param unknown_type $params
		 * @return unknown
		 */
		public static function help($params)
		{
			$params = self::bar($params);
			$params['content'] = Alp_Valid::of($params['content'], 'content', '内容', 'trim|required');
			$params['sort'] = Alp_Valid::of($params['sort'], 'sort', '归类', 'trim|required');
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
		
		/**
		 * 修改相关过滤
		 *
		 * @param unknown_type $params
		 * @return unknown
		 */
		public static function modtopic($params)
		{
			$params['title'] = Alp_Valid::of($params['title'], 'title', '标题主题', 'trim|strip_tags|str_between[3,90]');
			$params['content'] = Alp_Valid::of($params['content'], 'content', '内容', 'trim|required');
			return $params;
		}
		
		public static function modevents($params)
		{
			$params['title'] = Alp_Valid::of($params['title'], 'title', '标题主题', 'trim|strip_tags|str_between[3,90]');
			$params['content'] = Alp_Valid::of($params['content'], 'content', '内容', 'trim|required');
			if(!empty($params['limit']))
			$params['limit'] = Alp_Valid::of($params['limit'], 'limit', '人数限制', 'trim|numeric');
			else $params['limit'] = null;
			$params['year'] = Alp_Valid::of($params['year'], 'year', '年份', 'trim|numeric');
			$params['month'] = Alp_Valid::of($params['month'], 'month', '月份', 'trim|numeric');
			$params['day'] = Alp_Valid::of($params['day'], 'day', '日', 'trim|numeric');
			$params['hour'] = Alp_Valid::of($params['hour'], 'hour', '小时', 'trim|numeric');
			$params['min'] = Alp_Valid::of($params['min'], 'min', '分钟', 'trim|numeric');
			$time = strtotime($params['year'].'-'.$params['month'].'-'.$params['day'].' '.$params['hour'].':'.$params['min']);
			if(!$time) Alp_Sys::msg('time', '活动时间无效，请检查格式');
			else $params['time'] = $time;
			$params['address'] = Alp_Valid::of($params['address'], 'address', '活动地址', 'trim|required');
			return $params;
		}
		
		public static function modnews($params)
		{
			$params['title'] = Alp_Valid::of($params['title'], 'title', '标题主题', 'trim|strip_tags|str_between[3,90]');
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
		
		//----------------------------------------------------------------------------------------------
		
		/**
		 * 新闻分类过滤
		 *
		 * @param unknown_type $sort
		 * @return unknown
		 */
		public static function newsSort($sortname)
		{
			return Alp_Valid::of($sortname, 'sortname', '新闻分类', 'trim|strip_tags|str_between[2,10]');
		}
		
		public static function helpSort($sortname)
		{
			return Alp_Valid::of($sortname, 'sortname', '求助分类', 'trim|strip_tags|str_between[2,10]');
		}
	}

?>