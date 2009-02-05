<?php

	/**
	 * 短信息消息过滤
	 *
	 */
	class Filter_Msg
	{
		/**
		 * pm数据过滤
		 *
		 * @param unknown_type $params
		 */
		public static function pm($params)
		{
			$params['content'] = Alp_Valid::of($params['content'], 'content', '信息内容', 'trim|required');
			$params['uname'] = Alp_Valid::of($params['uname'], 'uname', '收件人', 'trim|required');
			return $params;
		}
		
		public static function reply($params)
		{
			$params['content'] = Alp_Valid::of($params['content'], 'content', '信息内容', 'trim|required');
			$params['incept'] = Alp_Valid::of($params['incept'], 'incept', '收件人', 'trim|numeric');
			$params['parent'] = Alp_Valid::of($params['parent'], 'parent', '对话id', 'trim|numeric');
			return $params;
		}
	}
?>