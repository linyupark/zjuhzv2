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
            	// 注册信息过滤
                $params = Filter_User::reg($params);
                if(Alp_Sys::getMsg() == null)
                {	
                	// 进行注册
                	$uid = Logic_User_Reg::insert($params);
                	if($uid != false)
                	{
                		// 注册成功log - add_user
		            	Logic_Log::user(array(
		            		'uid' => $uid,
		            		'fid' => 0,
		            		'key' => 'add_user'
		            	));
			            // 邀请注册好友关联
			        	Logic_User_Reg::rel($uid, $params['ucode'], $params['scode'], $params['username']);
                	}
                }
            }
            else
            {
                Alp_Sys::msg('form_tip','超时，请刷新页面后重新登录');
            }
            echo Alp_Sys::getMsgJson();
        }
        
        # 注册协议
        function agreementAction()
        {
        	$this->view->headTitle('杭州浙江大学校友会网站注册协议');
        }
        
        # 注册表单 ------------------------------------------------------------
        function indexAction()
        {
        	$this->view->headTitle('新校友注册');
            $this->view->ucode = $this->_getParam('ucode'); // 邀请用户加密id
            $this->view->scode = $this->_getParam('scode'); // 加为好友后的分类id
        }
    }

?>