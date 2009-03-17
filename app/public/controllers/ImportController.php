<?php

	/**
	 * 网站数据导入控制器(临时性)
	 *
	 */
	class ImportController extends Zend_Controller_Action 
	{
		function init(){ $this->getHelper('viewRenderer')->setNoRender(); set_time_limit(99999999); }
		
		function mvgbarAction()
		{
			$from_gid = (int)$this->_getParam('fgid');
			$to_gid = (int)$this->_getParam('tgid');
			if($from_gid > 0 && $to_gid > 0)
			{
				$gbars = Cmdv1::gbar($from_gid);
				if(count($gbars) > 0)
				{
					$v2db = DbModel::Space();
					foreach ($gbars as $b)
					{
						$v2db->insert('tb_tbar', array(
							'type' => 'topic',
							'title' => $b['title'],
							'puber' => $b['pub_user'],
							'pubtime' => $b['pub_time'],
							'click' => $b['click_num'],
							'reply' => $b['reply_num'],
							'replytime' => $b['reply_time'],
							'group' => $to_gid,
							'private' => 3
						));
						$tid = $v2db->lastInsertId();
						$v2db->insert('tb_topic', array(
							'tid' => $tid,
							'content' => $b['content']
						));
						// 回复导入
						$replys = Cmdv1::greply($b['topic_id']);
						foreach ($replys as $r)
						{
							$v2db->insert('tb_comment', array(
								'tid' => $tid,
								'uid' => $r['user_id'],
								'content' => $r['content'],
								'time' => $r['reply_time']
							));
						}
					}
				}
			}
		}
		
		/**
		 * 群组数据迁移
		 *
		 */
		function mvgroupAction()
		{
			$gid = $this->_getParam('gid');
			if($gid)
			{
				$data = Cmdv1::group($gid);
				$group = $data['group']; $member = $data['member'];
				$db = DbModel::Space();
				$db->insert('tb_group', array(
					'name' => $group['name'],
					'createtime' => $group['create_time'],
					'intro' => $group['intro'],
					'point' => 0,
					'type' => 'open'
				));
				$n_gid = $db->lastInsertId();
				$db->insert('tb_group_member', array(
					'uid' => $group['creater'],
					'gid' => $n_gid,
					'role' => 'creater',
					'jointime' => $group['create_time']
				));
				foreach ($member as $m)
				{
					if($m['user_id'] != $group['creater'])
					$db->insert('tb_group_member', array(
						'uid' => $m['user_id'],
						'gid' => $n_gid,
						'role' => 'member',
						'jointime' => $m['join_time'],
						'lastvisit' => $m['last_access']
					));
				}
			}
		}
		
		/**
		 * 好友关系数据迁移
		 *
		 */
		function friAction()
		{
			/*
			$rows = Cmdv1::friends();
			foreach ($rows as $r)
			{
				$uid = $r['uid'];
				$friends = explode(',', $r['friends']);
				if(Logic_User_Base::check($uid) != false)
				foreach ($friends as $f)
				{
					if(Logic_User_Base::check($f) != false)
					Logic_Space_Friends::rel($uid, $f);
				}
			}*/
		}
		
		function v1tov2Action()
		{
			/*
			$v2db = DbModel::User();
			$v1users = Cmdv1::alluser();
			
			foreach ($v1users as $u)
			{
				if(Logic_User_Reg::isRegistered('email', $u['eMail'])) continue;
				$role = $u['point'] == 0 ? 'bench' : 'member';
				$regtime = strtotime($u['regTime']);
				$bday = ''; $byear = ''; $bmon = '';
				if($u['point'] == null) $u['point'] = 0;
				if($u['birthday'] != null) // 处理生日
				{
					$time = strtotime($u['birthday']);
					$byear = date('y', $time);
					$bmon = date('m', $time);
					$bday = date('d', $time);
				}
				// 基础数据
				$v2db->insert('tb_base', 
					array(
						'uid' => $u['userid'],
						'account'=>$u['username'],
						'password'=>$u['password'],
						'username'=>$u['realName'],
						'sex' => $u['sex'],
						'birthyear' => $byear,
						'birthmonth' => $bmon,
						'birthday' => $bday,
						'hometown' => $u['hometown_c'],
						'city' => $u['location_c'],
						'regtime' => $regtime,
						'role' => $role,
						'point' => $u['point']
					)
				);
				// 不许空邮箱地址
				if($u['eMail'] == null) $u['eMail'] = $u['username'].'@163.com';
				$v2db->insert('tb_contact', 
					array(
						'uid' => $u['userid'],
						'email' => $u['eMail'],
						'mobile' => $u['mobile'],
						'qq' => $u['qq'],
						'msn' => $u['msn'],
						'address' => $u['address'],
						'zipcode' => $u['postcode'],
						'tel' => $u['phone']
					)
				);
				if($u['point'] > 0)
				$v2db->insert('zjuhzv2_log.tb_point', 
					array(
						'uid' => $u['userid'],
						'memo' => '热心度系统数据迁移累计加分，如需要回溯加分详情可咨询校友会',
						'time' => time(),
						'handler' => 0
					)
				);
			}
			//Zend_Debug::dump($v1users);
			echo 'done';*/
		}
	}
	
?>