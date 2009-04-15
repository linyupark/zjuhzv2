<?php
	
	/**
	 * 扩展组件数据过滤类
	 *
	 */
	class Filter_Addon
	{	
		/**
		 * 联盟企业信息
		 *
		 * @param unknown_type $params
		 */
		public static function corp($params)
		{
			$params['name'] = Alp_Valid::of($params['name'], 'name', '企业名称', 'trim|strip_tags|required');
			$params['contacter'] = Alp_Valid::of($params['contacter'], 'contacter', '联系人帐号', 'trim|required');
			$params['region'] = Alp_Valid::of($params['region'], 'region', '地区', 'trim|strip_tags|required');
			$params['trade'] = Alp_Valid::of($params['trade'], 'trade', '行业', 'trim|strip_tags|required');
			$params['intro'] = Alp_Valid::of($params['intro'], 'intro', '介绍', 'trim|required');
			if($params['web']!=''&&$params['web']!='http://' )
			$params['web'] = Alp_Valid::of($params['web'], 'web', '企业网站', 'trim|valid_url');
			$params['url'] = Alp_Valid::of($params['url'], 'url', '报名地址', 'trim|valid_url');
			$params['job'] = trim($params['job']);
			return  $params;
		}
		
		/**
		 * 过滤在线订票的各项数据
		 *
		 * @param unknown_type $params
		 * @return unknown
		 */
		public static function booking($params)
		{
			$params['title'] = Alp_Valid::of($params['title'], 'title', '活动主题', 'trim|required');
			$params['year'] = Alp_Valid::of($params['year'], 'year', '年份', 'trim|numeric');
			$params['month'] = Alp_Valid::of($params['month'], 'month', '月份', 'trim|numeric');
			$params['day'] = Alp_Valid::of($params['day'], 'day', '日', 'trim|numeric');
			$params['hour'] = Alp_Valid::of($params['hour'], 'hour', '小时', 'trim|numeric');
			$params['min'] = Alp_Valid::of($params['min'], 'min', '分钟', 'trim|numeric');
			$time = strtotime($params['year'].'-'.$params['month'].'-'.$params['day'].' '.$params['hour'].':'.$params['min']);
			if(!$time) Alp_Sys::msg('time', '活动时间无效，请检查格式');
			$params['time'] = $time;
			$params['content'] = Alp_Valid::of($params['content'], 'content', '活动内容', 'trim|required');
			$params['ticket'] = Alp_Valid::of($params['ticket'], 'ticket', '订票总数', 'trim|numeric');
			$params['limit'] = Alp_Valid::of($params['limit'], 'limit', '领票上限', 'trim|numeric');
			foreach($params['address'] as $id => $v)
			{
				$temp = strip_tags(trim($v));
				if(empty($temp)) unset($params['address'][$id]);
			}
			if(count($params['address']) == 0) Alp_Sys::msg('address', '取票点不能为空');
			$params['password'] = Alp_Valid::of($params['password'], 'password', '密码', 'trim|required');
			return $params;
		}
		
		/**
		 * 赞助伙伴账号数据过滤
		 *
		 * @param unknown_type $params
		 * @return unknown
		 */
		public static function partUser($params)
		{
			$params['username'] = Alp_Valid::of($params['username'], 'username', '账号', 'trim|aldash|str_between[3,16]');
			$params['password'] = Alp_Valid::of($params['password'], 'password', '密码', 'trim|required');
			return $params;
		}
		
		/**
		 * 投票数据过滤
		 *
		 * @param unknown_type $params
		 * @return unknown
		 */
		public static function vote($params)
		{
			if(count($params['oid']) == 0)
			Alp_Sys::msg('oid', '没有选择投票选项');
			else
			{
				$oids = array();
				foreach($params['oid'] as $oid)
				{
					if($oid != '') $oids[] = (int)$oid;
				}
				$params['oid'] = $oids;
			}
			return $params;
		}
		
		/**
		 * 创建投票的数据过滤
		 *
		 * @param unknown_type $params
		 * @return unknown
		 */
		public static function createVote($params)
		{
			$params['title'] = Alp_Valid::of($params['title'], 'title', '投票主题', 'trim|required');
			
			$options = array();
			foreach($params['options'] as $opt)
			{
				if($opt != '') $options[] = trim($opt);
			}
			$params['options'] = $options;
			if(count($params['options']) < 2) 
			Alp_Sys::msg('options', '选项起码填写2个');
			
			$params['mulit'] = Alp_Valid::of($params['mulit'], 'mulit', '可投选项', 'trim|numeric|num_larger[0]');
			return $params;
		}
	}

?>