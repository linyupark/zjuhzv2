<?php

    class Logic_User_Login extends DbModel
    {
        # 检查登录数据 ---------------------------------------------
        static function check($account, $password)
        {
            return parent::User()->fetchRow('SELECT `uid` FROM `tb_base` WHERE `account` = ? AND `password` = ?', array($account, $password));
        }
        
        # 登录成功后的处理
        static function treat($uid)
        {
            Cmd::setSess('profile', array('uid' => $uid));
            // 获取用户信息必要的存储在session
            $User = parent::User();
            $base = $User->fetchRow('SELECT * FROM `tb_base` WHERE `uid` = ?', $uid);
            $contact = $User->fetchRow('SELECT * FROM `tb_contact` WHERE `uid` = ?', $uid);
            if($base != false && $contact != false)
            {
            	Cmd::setSess('profile', $base);
            	Cmd::setSess('profile', $contact);
            	// 更新最后登陆时间
            	$User->update('tb_base', array('lastlogin' => time()), 'uid = '.$uid);      	
            	return true;
            }
            else return false;
        }
    }

?>