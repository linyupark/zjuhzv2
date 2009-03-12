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
        		case 'mobile' : return $User->fetchRow('SELECT `uid` FROM `tb_contact` WHERE `mobile` = ?', $value); break;
        		case 'uid' : return $User->fetchRow('SELECT `uid` FROM `tb_base` WHERE `uid` = ?', $value); break;
        		default : return false; break;
        	}
        }
        
        /**
         * 邀请注册
         *
         * @param unknown_type $uid
         * @param unknown_type $fid
         */
        public static function rel($uid, $fid, $sid)
        {
        	// 是否为有效邀请
			if(self::isRegistered('uid', $fid) != false 
			   && Logic_Space_Friends::hasSort($fid, $sid) == true)
			{
				// 关联
			    Logic_Space_Friends::rel($uid, $fid);
			    Logic_Space_Friends::rel($fid, $uid);
			    Logic_Space_Friends::sort($fid, $uid, $sid);
			
			    // 通知双方
				if(Logic_Space_Msg::unique('friend', $uid, $fid) == false)	
				DbModel::Space()->insert('tb_msg', array(
					'type' => 'friend',
					'content' => '系统提示：该用户已经通过邀请注册成功并将你加为好友',
					'sender' => $uid,
					'incept' => $fid,
					'time' => time()
				));
				if(Logic_Space_Msg::unique('friend', $fid, $uid) == false)
				DbModel::Space()->insert('tb_msg', array(
					'type' => 'friend',
					'content' => '系统提示：该用户已经将你加为好友',
					'sender' => $fid,
					'incept' => $uid,
					'time' => time()
				));
			        			
			    // log记录 - add_friend
			    Logic_Log::user(array(
			        'uid' => $uid,
			        'fid' => $fid,
				    'key' => 'add_friend'
				));
				Logic_Log::user(array(
				    'uid' => $fid,
			        'fid' => $uid,
				    'key' => 'add_friend'
				));
				// 邀请人加分
				Logic_Api::apoint('user', $fid, 5, '邀请校友注册本站', time(), 0);
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
        	// 默认为待审核
        	$data['role'] = 'bench';
        	
        	// 注册事务处理
            $User = parent::User();
            $User->beginTransaction();
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
            	
            	// 用户基础联系方式数据插入
            	$User->insert('tb_contact', array(
            		'uid' => $self_uid, 
            		'email' => $data['email'],
            		'mobile' => $data['mobile']
            	));
            	
            	$User->commit();
            	Alp_Sys::msg('form_tip', 'success');
            	Alp_Sys::msg('account', $data['account']);
            	return $self_uid;
            	
            } catch (Exception $e) {
            	$User->rollback();
            	Alp_Sys::msg('form_tip', $e->getMessage());
            }
            
            return false;
        }
    }

?>