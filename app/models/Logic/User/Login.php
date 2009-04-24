<?php

    class Logic_User_Login extends DbModel
    {
        # 检查登录数据 ---------------------------------------------
        static function check($account, $password)
        {
            return parent::User()->fetchRow('SELECT `uid`,`lastlogin` FROM `tb_base` WHERE `account` = ? AND `password` = ?', array($account, $password));
        }
        
        /**
         * 活动事件结束后的提示，加分
         *
         * @param unknown_type $uid
         */
        static function eventsnotice($uid)
        {
        	$now = time();
        	$db = DbModel::Space();
        	$rows = $db->fetchAll('SELECT e.`tid`,b.`title` 
        		FROM `tb_events` AS `e` 
        		LEFT JOIN `tb_tbar` AS `b` ON b.tid = e.tid 
        		WHERE (e.`end` <'.$now.' AND LENGTH(e.`member`) > 6 AND b.`deny` = 0 AND e.`apted` = 0 AND b.`puber` = '.$uid.')'
        	);
        	if(count($rows) > 0)
        	{
        		$str = '系统消息：<br />';
        		foreach ($rows as $r)
        		{
        			$str .= '您所发布的活动"'.$r['title'].'"已经结束，
        				现在可以为参加这次活动的成员<a target="_blank" href="/space_bar/events/view?tid='.$r['tid'].'#apt_link">增加热心度</a><br />';
        		}
        		$str .= '活动中有什么感触么想跟大家分享么？
        		<a href="/space_bar/?pub=topic" target="_blank">发帖总结</a>下吧';
        		Logic_Space_Msg::pm(1, $uid, $str);
        	}
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