<?php

	class Logic_Space_Msg extends DbModel 
	{	
		/**
		 * 群信
		 *
		 * @param unknown_type $incept
		 * @param unknown_type $sender
		 * @param unknown_type $gid
		 * @param unknown_type $content
		 */
		public static function group($incept, $sender, $gid, $content)
		{
			parent::Space()->insert('tb_msg', array(
				'gid' => $gid,
				'incept' => $incept,
				'sender' => $sender,
				'type' => 'group',
				'content' => $content,
				'time' => time()
			));
		}
		
		/**
		 * 申请加入群组
		 *
		 * @param unknown_type $incept
		 * @param unknown_type $sender
		 * @param unknown_type $gname
		 * @param unknown_type $gid
		 */
		public static function joinGroup($incept, $sender, $gid)
		{
			$db = parent::Space();
			$db->beginTransaction();
			try {
				foreach ($incept as $r)
				{
					$db->insert('tb_msg', array(
						'gid' => $gid,
						'incept' => $r['uid'],
						'sender' => $sender,
						'type' => 'group',
						'content' => '申请加入群',
						'time' => time()
					));
				}
				$db->commit();
				
			} catch (Exception $e) {
				
				$db->rollback();
				Alp_Sys::msg('exception', $e->getMessage());
			}
		}
		
		/**
		 * 是否有新回复
		 *
		 * @param unknown_type $parent
		 * @return unknown
		 */
		public static function newReply($parent)
		{
			return parent::Space()->fetchRow('SELECT `mid` FROM `tb_msg` 
				WHERE `parent` = ? AND isread = 0 AND sender != '.Cmd::uid(), $parent);
		}
		
		/**
		 * PM包含对话数
		 *
		 * @param unknown_type $mid
		 * @return unknown
		 */
		public static function childnum($parent)
		{
			$row = parent::Space()->fetchRow('SELECT COUNT(`mid`) AS `numrow` 
								FROM `tb_msg` WHERE `parent` = ?', $parent);
			return $row['numrow'];
		}
		
		/**
		 * 相关对话
		 *
		 * @param unknown_type $mid
		 * @return unknown
		 */
		public static function children($parent)
		{
			$select = parent::Space()->select();
			$select->from(array('msg' => 'tb_msg'))->where('msg.parent = ?', $parent);
			$select->joinLeft(array('su' => 'zjuhzv2_user.tb_base'), 'su.uid = msg.sender', 
							array('sname' => 'su.username', 'ssex' => 'su.sex'));
			$select->joinLeft(array('iu' => 'zjuhzv2_user.tb_base'), 'iu.uid = msg.incept', 
							array('iname' => 'iu.username', 'isex' => 'iu.sex'));
			return $select->query()->fetchAll();
		}
		
		/**
		 * 删除双方都已经清除记录的msg
		 *
		 */
		public static function del()
		{
			parent::Space()->delete('tb_msg', 'ibox = 0 AND sbox = 0');
		}
		
		/**
		 * 清除信息记录
		 *
		 * @param unknown_type $mid
		 */
		public static function clear($mid, $box = 'ibox')
		{
			parent::Space()->update('tb_msg', array($box => 0), 'mid = '.(int)$mid);
		}
		
		/**
		 * 发送站内信息
		 *
		 * @param unknown_type $incept
		 * @param unknown_type $content
		 */
		public static function pm($sender, $incept, $content)
		{
			$db = parent::Space();
			$db->beginTransaction();
			try {
				$db->insert('tb_msg', array(
					'type' => 'pm',
					'sender' => $sender,
					'incept' => $incept,
					'content' => $content,
					'time' => time()
				));
				$mid = $db->lastInsertId();
				$db->update('tb_msg', array('parent' => $mid), 'mid = '.$mid);
				$db->commit();

			} catch (Exception $e){
				$db->rollback();
				Alp_Sys::msg('exception', $e->getMessage());
			}
		}
		
		/**
		 * 回复
		 *
		 * @param unknown_type $params
		 */
		public static function reply($params)
		{
			parent::Space()->insert('tb_msg', array(
				'type' => 'pm',
				'sender' => $params['sender'],
				'incept' => $params['incept'],
				'content' => Alp_String::html($params['content']),
				'time' => time(),
				'parent' => $params['parent']
			));
		}
		
		/**
		 * 保证无重复信息
		 *
		 * @param unknown_type $type
		 * @param unknown_type $sender
		 * @param unknown_type $incept
		 */
		public static function unique($type, $sender, $incept)
		{
			return parent::Space()->fetchRow('SELECT `mid` FROM `tb_msg` 
									WHERE `type` = ? AND `sender` = ? AND `incept` = ? ', array($type, $sender, $incept));
		}
		
		/**
		 * 返回新信息数目
		 *
		 * @param unknown_type $uid
		 * @return unknown
		 */
		public static function hasnew($uid, $type = null)
		{
			$where = '';
			if($type != null) $where = ' AND `type` = "'.$type.'"';
			$row = parent::Space()->fetchRow('SELECT COUNT(`mid`) AS `numrow` 
												FROM `tb_msg` WHERE `incept` = ? AND `isread` = 0'.$where, $uid);
			return ($row == false) ? 0 : $row['numrow'];
		}
		
		/**
		 * 变为已读信息
		 *
		 * @param unknown_type $mid
		 */
		public static function reading($mid)
		{
			parent::Space()->update('tb_msg', array('isread' => 1), 'mid = '.$mid);
		}
	}

?>