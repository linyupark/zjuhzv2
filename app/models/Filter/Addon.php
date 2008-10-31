<?php
	
	/**
	 * 扩展组件数据过滤类
	 *
	 */
	class Filter_Addon
	{
		/**
		 * 赞助伙伴账号数据过滤
		 *
		 * @param unknown_type $params
		 * @return unknown
		 */
		public static function partUser($params)
		{
			$params['username'] = Alp_Valid::of($params['username'], 'username', '账号', 'trim|aldash|str_between[4,16]');
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