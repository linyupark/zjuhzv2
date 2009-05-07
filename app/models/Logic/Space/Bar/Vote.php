<?php

	class Logic_Space_Bar_Vote extends DbModel
	{
		public static function mod($params, $tid)
		{
			$db = parent::Space();
			$db->beginTransaction();
			try {
				$row = $db->fetchRow('SELECT `options`,`rates` FROM `tb_vote` WHERE `tid` = ?', $tid);
				$options = unserialize($row['options']);
				$rates = unserialize($row['rates']);
				$options = array_merge($options, $params['options']);
				$rates = array_merge($rates, $params['rates']);
				$db->update('tb_tbar', array(
					'title' => $params['title'],
					'private' => $params['private'],
					'nicky' => $params['nicky']
				), 'tid = '.$tid);
				
				$db->update('tb_vote', array(
					'memo' => $params['memo'],
					'rates' => serialize($rates),
					'maxselect' => $params['maxselect'],
					'options' => serialize($options)
				), 'tid = '.$tid);
				
				$db->commit();
			} catch (Exception $e) {
				
				$db->rollback();
				Alp_Sys::msg('exception', $e->getMessage());
			}
		}
		
		/**
		 * 发送本人的投票信息
		 *
		 * @param unknown_type $tid
		 * @param unknown_type $opt
		 */
		public static function send($tid, $opt)
		{
			$db = parent::Space();
			$db->beginTransaction();
			try {
				$row = $db->fetchRow('SELECT `rates`,`voters` FROM `tb_vote` WHERE `tid` = ?', $tid);
				$rates = unserialize($row['rates']);
				$voters = unserialize($row['voters']);
				$uid = Cmd::uid();
				foreach ($opt as $v)
				{
					$rates[$v] += 1;
				}
				$voters[$uid] = $opt;
				$db->update('tb_vote', array(
					'rates' => serialize($rates),
					'votenum' => new Zend_Db_Expr('votenum + '.count($opt)),
					'voters' => serialize($voters)
				), 'tid = '.$tid);
				$db->commit();
			} catch (Exception $e) {
				
				$db->rollback();
				Alp_Sys::msg('exception', $e->getMessage());
			}
		}
		
		/**
		 * 调查帖信息
		 *
		 * @param unknown_type $tid
		 * @return unknown
		 */
		public static function view($tid)
		{
			$select = parent::Space()->select();
			$select->from(array('bar' => 'tb_tbar'))->where('bar.tid = ?', $tid);
			$select->joinLeft(array('v' => 'zjuhzv2_space.tb_vote'), 'bar.tid = v.tid');
			$select->joinLeft(array('u'=>'zjuhzv2_user.tb_base'), 'bar.puber = u.uid', 
							  array('uname'=>'u.username','unick'=>'u.nickname','u.sex','sign'));
			return $select->query()->fetchAll();
		}
		
		/**
		 * 发布调查贴
		 *
		 * @param unknown_type $params
		 * @return unknown
		 */
		public static function insert($params)
		{
			$db = parent::Space();
			$db->beginTransaction();
			try {
				$db->insert('tb_tbar', array(
					'type' => $params['type'],
					'title' => $params['title'],
					'puber' => Cmd::uid(),
					'pubtime' => time(),
					'replytime' => time(),
					'group' => $params['group'],
					'private' => $params['private'],
					'nicky' => $params['nicky']
				));
				$tid = $db->lastInsertId();
				$db->insert('tb_vote', array(
					'tid' => $tid,
					'options' => serialize($params['options']),
					'rates' => serialize($params['rates']),
					'votenum' => 0,
					'maxselect' => $params['maxselect'],
					'memo' => $params['memo']
				));
				$db->commit();
				return $tid;
				
			} catch (Exception $e) {
				
				$db->rollback();
				Alp_Sys::msg('exception', $e->getMessage());
			}
			return false;
		}
	} 

?>