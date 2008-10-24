<?php

    class Logic_User_Mod extends UserModel
    {
        # 更改用户角色
        static function role($uid, $role)
        {
            parent::dao()->update('tb_base', array('role'=>$role), 'uid = '.(int)$uid);
            
        }
    }

?>