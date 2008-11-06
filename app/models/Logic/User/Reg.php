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
        		default : return false; break;
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
        	// 自行注册
        	$data['role'] = 'bench';
        	
        	// 邀请注册
        	if(isset($data['ucode']) && isset($data['scode']))
        	{
        		// 是否为有效邀请
        		if(self::isRegistered('uid', $data['ucode']) != false 
        		&& Logic_Space_Friends::hasSort($data['ucode'], $data['scode']) == true)
        		{
        			$data['role'] = 'member';
        		}
        	}
        	
        	// 注册事务处理
            $User = parent::User()->beginTransaction();
            try
            {
            	// 用户基础数据插入
            	$User->insert('tb_base', array(
            		'account' => $data['account'],
            		'password' => md5($data['password']),
            		'username' => $data['username'],
            		'sex' => $data['sex'],
            		'role' => $data['role'],
            		'regtime' => time()
            	));
            	
            	// 获取注册后的个人id
            	$self_uid = $User->lastInsertId();
            	
            	// 用户email数据插入
            	$User->insert('tb_contact', array(
            		'uid' => $self_uid, 
            		'email' => $data['email']
            	));
            	
            	// log数据
            	
            	
            	// 好友关联
            } catch (Exception $e) {
            	
            }
        }
    }

?>