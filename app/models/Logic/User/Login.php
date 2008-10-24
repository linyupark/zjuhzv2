<?php

    class Logic_User_Login extends UserModel
    {
        # 检查登录数据 ---------------------------------------------
        static function check($account, $password)
        {
            return parent::dao()->fetchRow('SELECT `uid` FROM `tb_base` WHERE `account` = ? AND `password` = ?', array($account, $password));
        }
        
        # 登录成功后的处理
        static function treat($uid)
        {
            // 将必要数据保存到session
            $sess_data = parent::dao()->fetchRow('SELECT `uid`,``');
            
        }
    }

?>