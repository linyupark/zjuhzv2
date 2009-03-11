<?php
	
	/**
	 * 用户数据过滤类
	 *
	 */
	class Filter_User
	{
		/**
		 * 密码确认
		 *
		 * @param unknown_type $params
		 * @return unknown
		 */
		public static function password($params)
		{
			$params['opassword'] = Alp_Valid::of($params['opassword'], 'opassword', '原密码', 'trim|required');
			$params['password'] = Alp_Valid::of($params['password'], 'password', '新密码', 'trim|required|matches[确认密码,'.$params['password2'].']');
			if(Alp_Sys::getMsg() == null)
			{
				if(false == Logic_User_Login::check(Cmd::getSess('profile','account'), md5($params['opassword'])))
				Alp_Sys::msg('opassword_error', '原密码错误');
			}
			return $params;
		}
		
		/**
		 * 工作情况
		 *
		 * @param unknown_type $params
		 * @return unknown
		 */
		public static function career($params)
		{
			$params['company'] = Alp_Valid::of($params['company'], 'company', '公司或机构', 'trim|strip_tags|required');
			$params['department'] = Alp_Valid::of($params['department'], 'department', '部门', 'trim|strip_tags|required');
			$params['job'] = Alp_Valid::of($params['job'], 'job', '职业', 'trim|strip_tags|required');
			$params['start'][0] = Alp_Valid::of($params['start'][0], 'start_year', '入职年份', 'trim|strip_tags|required');
			$params['start'][1] = Alp_Valid::of($params['start'][1], 'start_month', '入职月份', 'trim|strip_tags|required');
			if($params['end'] != 0)
			{
				$params['end'][0] = Alp_Valid::of($params['end'][0], 'end_year', '离职年份', 'trim|strip_tags|required');
				$params['end'][1] = Alp_Valid::of($params['end'][1], 'end_month', '离职月份', 'trim|strip_tags|required');
			}
			return $params;
		}
		
		/**
		 * 教育情况
		 *
		 * @param unknown_type $params
		 * @return unknown
		 */
		public static function edu($params)
		{
			$params['campus'] = Alp_Valid::of($params['campus'], 'campus', '院系名称', 'trim|strip_tags|required');
			$params['class'] = Alp_Valid::of($params['class'], 'class', '班级名称', 'trim|strip_tags|required');
			$params['year'] = Alp_Valid::of($params['year'], 'year', '入学年份', 'trim|strip_tags|numeric');
			return $params;
		}
		
		/**
		 * 个人介绍信息
		 *
		 * @param unknown_type $params
		 * @return unknown
		 */
		public static function intro($params)
		{
			$params['intro'] = Alp_Valid::of($params['intro'], 'intro', '我的简介', 'trim|strip_tags|str_between[0,255]');
			$params['motto'] = Alp_Valid::of($params['motto'], 'motto', '座右铭', 'trim|strip_tags|str_between[0,255]');
			$params['wish'] = Alp_Valid::of($params['wish'], 'wish', '最近心愿', 'trim|strip_tags|str_between[0,255]');
			$params['interest'] = Alp_Valid::of($params['interest'], 'interest', '兴趣爱好', 'trim|strip_tags|str_between[0,255]');
			$params['book'] = Alp_Valid::of($params['book'], 'book', '喜欢的书', 'trim|strip_tags|str_between[0,255]');
			$params['movie'] = Alp_Valid::of($params['movie'], 'movie', '喜欢的电影', 'trim|strip_tags|str_between[0,255]');
			$params['idol'] = Alp_Valid::of($params['idol'], 'idol', '偶像', 'trim|strip_tags|str_between[0,255]');
			$params['tv'] = Alp_Valid::of($params['tv'], 'tv', '喜欢的电视', 'trim|strip_tags|str_between[0,255]');
			return $params;
		}
		
		/**
		 * 个人联系方式信息
		 *
		 * @param unknown_type $params
		 * @return unknown
		 */
		public static function contact($params)
		{
			$params['email'] = Alp_Valid::of($params['email'], 'email', '邮箱', 'trim|valid_email');
			if(!empty($params['address']))
			$params['address'] = Alp_Valid::of($params['address'], 'address', '地址', 'trim');
			if(!empty($params['zipcode']))
			$params['zipcode'] = Alp_Valid::of($params['zipcode'], 'zipcode', '邮编', 'trim|numeric');
			if(!empty($params['tel']))
			$params['tel'] = Alp_Valid::of($params['tel'], 'tel', '电话', 'trim|numeric');
			if(!empty($params['mobile']))
			$params['mobile'] = Alp_Valid::of($params['mobile'], 'mobile', '手机', 'trim|numeric');
			if(!empty($params['qq']))
			$params['qq'] = Alp_Valid::of($params['qq'], 'qq', 'QQ', 'trim|numeric');
			if(!empty($params['msn']))
			$params['msn'] = Alp_Valid::of($params['msn'], 'msn', 'MSN', 'trim|valid_email');
			return $params;
		}
		
		/**
		 * 设置个人基本信息
		 *
		 * @param unknown_type $params
		 */
		public static function base($params)
		{
			$params['username'] = Alp_Valid::of($params['username'], 'username', '真实姓名', 'trim|strip_tags|str_between[2,4]');
			$params['nickname'] = Alp_Valid::of($params['nickname'], 'nickname', '昵称', 'trim|strip_tags|str_between[0,10]');
			$params['hometown'] = Alp_Valid::of($params['hometown'], 'hometown', '家乡', 'trim|strip_tags|str_between[0,10]');
			$params['city'] = Alp_Valid::of($params['city'], 'city', '现居住地', 'trim|strip_tags|str_between[0,10]');
			return $params;
		}
		
		/**
		 * 注册信息过滤
		 *
		 * @param array $params
		 * @return array
		 */
		public static function reg($params)
		{
			$params['account'] = Alp_Valid::of($params['account'], 'account', '帐号', 'trim|aldash|str_between[3,16]');
            $params['username'] = Alp_Valid::of($params['username'], 'username', '真实姓名', 'trim|str_between[2,4]');
            $params['password'] = Alp_Valid::of($params['password'], 'password', '登录密码', 'trim|required|matches[确认密码,'.$params['password2'].']');
            $params['email'] = Alp_Valid::of($params['email'], 'email', '邮箱', 'trim|valid_email');
            $params['mobile'] = Alp_Valid::of($params['mobile'], 'mobile', '手机号码', 'trim|numeric|str_exact[11]');

            if(Alp_Sys::getMsg() == null)
            {
            	if(Logic_User_Reg::isRegistered('account', $params['account']))
                Alp_Sys::msg('account','该帐号已被注册');
                if(Logic_User_Reg::isRegistered('email', $params['email']))
                Alp_Sys::msg('email','该邮箱已被注册');
                if(Logic_User_Reg::isRegistered('mobile', $params['mobile']))
                Alp_Sys::msg('mobile','该手机号码已被注册');
                
                // 邀请是否为注册判断
                if(!empty($params['ucode']) && !empty($params['scode']))
                {
                	$params['ucode'] = Alp_String::decrypt($params['ucode']);
                	$params['scode'] = Alp_String::decrypt($params['scode']);
                }
			}
			return $params;
		}
	}

?>