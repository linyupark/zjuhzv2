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
                        Alp_Sys::msg('islogin','yes');
                    }
                    else Alp_Sys::msg('check_msg','账号密码有误');
                }
            }
            else
            {
                Alp_Sys::msg('valid_tip','超时，请刷新页面后重新登录');
            }
            echo Alp_Sys::getMsgJson();
        }
        
        function indexAction()
        {
            $request = $this->getRequest();
            $this->view->account = $request->getParam('account');
        }
    }

?>