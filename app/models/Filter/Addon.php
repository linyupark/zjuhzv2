<?php
	
	/**
	 * 扩展组件数据过滤类
	 *
	 */
	class Filter_Addon
	{
		public static function createVote($params)
		{
			$params['title'] = Alp_Valid::of($params['title'], 'title', '投票主题', 'trim|required');
			$options = array();
			foreach($params['options'] as $opt)
			{
				if($opt != '') $options[] = trim($opt);
			}
			$params['mulit'] = Alp_Valid::of($params['mulit'], 'mulit', '可投选项', 'trim|numeric');
			$params['options'] = $options;
			if(count($params['options']) < 2) 
			Alp_Sys::msg('options', '选项起码填写2个');
			return $params;
		}
	}

?>