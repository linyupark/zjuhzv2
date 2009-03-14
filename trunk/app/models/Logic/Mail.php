<?php

	/**
	 * 邮件控制器
	 *
	 */
	class Logic_Mail
	{
		/**
		 * 返回邮箱设置
		 *
		 * @param unknown_type $set
		 * @return unknown
		 */
		public static function config($set = null)
		{
			if($set == null)
			return array(
				'name' => 'smtp.gmail.com',
				'username' => 'service@zjuhz.com',
				'password' => '89988185-service_zjuhz_com',
				'auth' => 'login',
				'ssl' => 'ssl'
			);
			else return $set;
		}
	}

?>