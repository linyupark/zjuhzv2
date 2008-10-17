<?php

    class Logic_User_Reg extends UserModel
    {
        # 指定的字段是否已经注册 ---------------------------------------------
        static function isRegistered($col, $value)
        {
            if($col == 'account')
            return parent::dao()->fetchRow('SELECT `uid` FROM `tb_base` WHERE `account` = ?', $value);
            if($col == 'email')
            return parent::dao()->fetchRow('SELECT `uid` FROM `tb_contact` WHERE `email` = ?', $value);
            if($col == 'uid')
            return parent::dao()->fetchRow('SELECT `uid` FROM `tb_contact` WHERE `uid` = ?', $value);
        }
        # 注册用户数据 --------------------------------------------------------
        static function insert($data)
        {
            $db = parent::dao();
            $db->beginTransaction();
            try
            {
                $email = $data['email'];
                unset($data['email']);
                $db->insert('tb_base', $data);
                $uid = $db->lastInsertId();
                $db->insert('tb_contact', array('uid'=>$uid,'email'=>$email));
                $db->commit();
                return $uid;
            }
            catch(Exception $e)
            {
                $db->rollBack();
                return $e->getMessage();
            }
        }
    }

?>