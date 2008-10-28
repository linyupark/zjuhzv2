<?php
	
	/**
	 * 用户数据过滤类
	 *
	 */
	class Filter_User
	{
		/**
		 * 注册信息过滤
		 *
		 * @param array $params
		 * @return array
		 */
		public static function reg($params)
		{
			$params['account'] = Alp_Valid::of($params['account'], 'account', '帐号', 'trim|aldash|str_between[4,16]');
            $params['username'] = Alp_Valid::of($params['username'], 'username', '真实姓名', 'trim|str_between[2,4]');
            $params['password'] = Alp_Valid::of($params['password'], 'password', '登录密码', 'trim|required|matches[确认密码,'.$params['password2'].']');
            $params['email'] = Alp_Valid::of($params['email'], 'email', '邮箱', 'trim|valid_email');
            if(Alp_Sys::getMsg() == null)
            {
            	if(Logic_User_Reg::isRegistered('account', $params['account']))
                Alp_Sys::msg('account','该帐号已被注册');
                if(Logic_User_Reg::isRegistered('email', $params['email']))
                Alp_Sys::msg('email','该邮箱已被注册');
                if(isset($params['ucode']) && isset($params['scode']))
                {
                	$uid = Alp_String::decrypt($params['ucode']);
                	$sid = Alp_String::decrypt($params['scode']);
                }
			}
			return $params;
		}
	}

?>