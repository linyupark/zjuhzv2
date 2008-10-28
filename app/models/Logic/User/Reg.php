<?php

    class Logic_User_Reg extends DbModel
    {
    	
        /**
         * 是否已经在用户数据库中存在
         *
         * @param string $col
         * @param string $value
         * @return unknown
         */
        public static function isRegistered($col, $value)
        {
        	$User = parent::User();
        	
        	switch ($col)
        	{
        		case 'account' : return $User->fetchRow('SELECT `uid` FROM `tb_base` WHERE `account` = ?', $value); break;
        		case 'email' : return $User->fetchRow('SELECT `uid` FROM `tb_contact` WHERE `email` = ?', $value); break;
        		case 'uid' : return $User->fetchRow('SELECT `uid` FROM `tb_base` WHERE `uid` = ?', $value); break;
        		default : return null; break;
        	}
        }
        
        /**
         * 插入新注册用户数据,以及相关处理
         *
         * @param array $data
         * @return unknown
         */
        public static function insert($data)
        {
            $User = parent::User();
            $User->beginTransaction();
            try
            {
            	$User->insert('tb_base', array(
            		'account' => $data['account'],
            		'password' => md5($data['password']),
            		'username' => $data['username'],
            		'sex' => $data['sex'],
            		
            	));
            }
        }
    }

?>