<?php

    class RegController extends Zend_Controller_Action
    {
        function init()
        {
            
        }
        # 注册用户提交 --------------------------------------------------------
        function doregAction()
        {
            $this->getHelper('viewRenderer')->setNoRender();
            $params = $this->getRequest()->getParams();
            if($params['sid'] == Zend_Session::getId())
            {
                $account = Alp_Valid::of($params['account'], 'account', '帐号', 'trim|aldash|str_between[4,16]');
                $username = Alp_Valid::of($params['username'], 'username', '真实姓名', 'trim|str_between[2,4]');
                $password = Alp_Valid::of($params['password'], 'password', '登录密码', 'trim|required|matches[确认密码,'.$params['password2'].']');
                $email = Alp_Valid::of($params['email'], 'email', '邮箱', 'trim|valid_email');
                // 判断注册数据格式
                if(Alp_Sys::getMsg() == null)
                {
                    if(Logic_User_Reg::isRegistered('account', $account))
                    Alp_Sys::msg('account','该帐号已被注册');
                    if(Logic_User_Reg::isRegistered('email', $email))
                    Alp_Sys::msg('email','该邮箱已被注册');
                    // 判断是否有重复数据
                    if(Alp_Sys::getMsg() == null)
                    {
                        // 默认注册数据
                        $data = array(
                            'account' => $account,
                            'username' => $username,
                            'password' => md5($password),
                            'email' => $email,
                            'sex' => $params['sex'],
                            'role' => 'bench',
                            'regtime' => time()
                        );
                        $uid = Logic_User_Reg::insert($data);
                        // 邀请注册更新
                        if(isset($params['ucode']) && isset($params['scode']))
                        {
                            $friend = Alp_String::decrypt($params['ucode']);
                            if(Logic_User_Reg::isRegistered('uid', $friend))
                            {
                                
                                $sort = Alp_String::decrypt($params['scode']);
                                // 
                            }
                        }
                        
                        
                        echo Zend_Json::encode(array('isregistered'=>'yes','account'=>$account));
                        exit();
                    }
                }
            }
            else
            {
                Alp_Sys::msg('form_action','注册有效时间已过，请刷新页面重新注册');
            }
            echo Alp_Sys::getMsgJson();
        }
        
        # 注册表单 ------------------------------------------------------------
        function indexAction()
        {
            $this->view->ucode = $this->_getParam('ucode');
            $this->view->scode = $this->_getParam('scode');
        }
    }

?>