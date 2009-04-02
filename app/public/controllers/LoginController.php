<?php

    class LoginController extends Zend_Controller_Action
    {
        function init(){}
        
        function dologinAction()
        {
            $this->getHelper('viewRenderer')->setNoRender();
            $params = $this->getRequest()->getParams();
            if($params['sid'] == Zend_Session::getId())
            {
                $account = Alp_Valid::of($params['account'], 'account', '帐号', 'trim|aldash|str_between[2,16]');
                $password = Alp_Valid::of($params['password'], 'password', '登录密码', 'trim|required');
                if(Alp_Sys::getMsg() == null)
                {
                    $row = Logic_User_Login::check($account, md5($password));
                    if($row != false)
                    {
                        // 成功登录处理
                        if(Logic_User_Login::treat($row['uid']) == true)
                        {
                        	// 检测其教育资料数据
                        	if(!Logic_User_Edu::get($row['uid']))
                        	{
                        		Alp_Sys::msg('goto', '/space_set/profile/edu?tip=1');
                        	}
                        	
                        	// 检测其联系方式资料
                        	if(!Cmd::getSess('profile', 'mobile'))
                        	{
                        		Alp_Sys::msg('goto', '/space_set/profile/contact?tip=1');
                        	}
                        	
	                        // cookie 保存帐号密码
	                        if($params['rememberme'] == 1)
	                        setcookie(
	                        	'zjuhzv2_remember', 
	                        	Alp_String::encrypy(serialize(array($account, $password))), 
	                        	(time()+3600*24*7), '/'
	                        );
	                        
	                        // 活动结束提示
	                        Logic_User_Login::eventsnotice($row['uid']);
	                        
	                        // 系统自动给新一天登陆的成员加分1pt
	                        $point = 1;
	                        $pre = date('Y-m-d', $row['lastlogin']);
	                        $now = date('Y-m-d', time());
	                        if($pre != $now) // 不是同一天
	                        {
	                        	DbModel::User()->update('tb_base', 
								array('point' => new Zend_Db_Expr('point + '.$point)), 'uid = '.$row['uid']);
		                        Cmd::setSess('apt_tip', array('login' => 1));
	                        }
	                        Alp_Sys::msg('form_tip', 'success');
                        }
                    }
                    else Alp_Sys::msg('form_tip','账号密码有误');
                }
            }
            else
            {
                Alp_Sys::msg('valid_tip','超时，请刷新页面后重新登录');
            }
            echo Alp_Sys::getMsgJson();
        }
        
        /**
         * 退出登录
         *
         */
        function outAction()
        {
        	Zend_Session::destroy();
        	unset($_COOKIE['zjuhzv2_remember']);
        	setcookie('zjuhzv2_remember', null, time() - 3600, '/');
        	$this->_redirect('/');
        }
        
        /**
         * 登录表单
         *
         */
        function indexAction()
        {
        	$request = $this->getRequest();
        	$this->view->remember = unserialize(stripslashes(Alp_String::decrypt($_COOKIE['zjuhzv2_remember'])));
            $this->view->account = $request->getParam('account');
            $this->view->relogin = $request->getParam('relogin');
        }
    }

?>