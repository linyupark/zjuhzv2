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
                $account = Alp_Valid::of($params['account'], 'account', '帐号', 'trim|aldash|str_between[4,16]');
                $password = Alp_Valid::of($params['password'], 'password', '登录密码', 'trim|required');
                if(Alp_Sys::getMsg() == null)
                {
                    $row = Logic_User_Login::check($account, md5($password));
                    if($row != false)
                    {
                        // 成功登录处理
                        if(Logic_User_Login::treat($row['uid']) == true)
                        {
	                        // cookie 保存帐号密码
	                        if($params['rememberme'] == 1)
	                        setcookie(
	                        	'zjuhz_remember', 
	                        	Alp_String::encrypy(serialize(array($account, $password))), 
	                        	(time()+3600*24*7), '/'
	                        );
	                        Alp_Sys::msg('form_tip','success');
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
        	unset($_COOKIE['zjuhz_remember']);
        	setcookie('zjuhz_remember', '', -100, '/');
        	$this->_redirect('/');
        }
        
        /**
         * 登录表单
         *
         */
        function indexAction()
        {
        	$request = $this->getRequest();
        	$this->view->remember = unserialize(stripslashes(Alp_String::decrypt($_COOKIE['zjuhz_remember'])));
            $this->view->account = $request->getParam('account');
        }
    }

?>