<?php

	/**
	 * 在线订票数据操作
	 *
	 */
	class Logic_Addon_Booking extends DbModel 
	{
		public static function getStation($pid, $aid)
		{
			return parent::getSqlite('booking.s3db')->fetchRow('SELECT * FROM `station` WHERE `pid` = ? AND `id`= ?', array($pid, $aid));
		}
		
		public static function getStations($id)
		{
			return parent::getSqlite('booking.s3db')->fetchAll('SELECT * FROM `station` WHERE `pid` = ?', $id);
		}
		
		/**
		 * 根据id获取制定活动
		 *
		 * @param unknown_type $id
		 */
		public static function getParty($id)
		{
			return parent::getSqlite('booking.s3db')->fetchRow('SELECT * FROM `party` WHERE `id` = ?', $id);
		}
		
		/**
		 * 所有活动内容
		 *
		 * @return unknown
		 */
		public static function allParty()
		{
			return parent::getSqlite('booking.s3db')->fetchAll('SELECT * FROM `party` ORDER BY `id` DESC');
		}
		
		public static function update($params, $pid)
		{
			$db = parent::getSqlite('booking.s3db');
			$db->beginTransaction();
			try
			{
				$db->update('party', array(
					'member' => serialize($params['member']),
					'left' => $params['left']
				), 'id = '.(int)$pid);
				$db->commit();
				
			} catch (Exception $e) {
				
				$db->rollback();
				Alp_Sys::msg('exception', $e->getMessage());
			}
		}
		
		/**
		 * 新数据
		 *
		 * @param unknown_type $params
		 */
		public static function insert($params)
		{
			$db = parent::getSqlite('booking.s3db');
			$db->beginTransaction();
			try
			{
				$db->insert('party', array(
					'title' => $params['title'],
					'time' => $params['time'],
					'content' => $params['content'],
					'ticket' => $params['ticket'],
					'left' => $params['ticket'],
					'password' => $params['password'],
					'limit' => $params['limit']
				));
				$id = $db->lastInsertId();
				foreach($params['address'] as $address)
				{
					$db->insert('station', array(
						'pid' => $id,
						'address' => $address
					));
				}
				$db->commit();
				return $id;
				
			} catch (Exception $e) {
				
				$db->rollback();
				Alp_Sys::msg('exception', $e->getMessage());
			}
			return false;
		}
		
		/**
		 * 删除
		 *
		 * @param unknown_type $pid
		 */
		public static function del($pid)
		{
			$db = parent::getSqlite('booking.s3db');
			$db->beginTransaction();
			try
			{
				$db->delete('party','id='.$pid);
				$db->delete('station','pid='.$pid);
				$db->commit();
			} catch (Exception $e) {
				
				$db->rollback();
				Alp_Sys::msg('exception', $e->getMessage());
			}
		}
		
		/**
		 * 修改
		 *
		 * @param unknown_type $params
		 * @param unknown_type $pid
		 */
		public static function modify($params, $pid)
		{
			$db = parent::getSqlite('booking.s3db');
			$db->beginTransaction();
			try
			{
				$left = $params['left'];
				if($params['oticket'] > $params['ticket'])
				$left = $params['left'] - ($params['oticket']- $params['ticket']);
				if($params['ticket'] > $params['oticket'])
				$left = $params['left'] + ($params['ticket'] - $params['oticket']);
				$db->update('party', array(
					'title' => $params['title'],
					'time' => $params['time'],
					'content' => $params['content'],
					'ticket' => $params['ticket'],
					'password' => $params['password'],
					'limit' => $params['limit'],
					'left' => $left
				), 'id='.$pid);
				foreach($params['address'] as $aid => $address)
				{
					if(self::getStation($pid, $aid))
					$db->update('station', array(
						'address' => $address
					),'id='.$aid);
					else 
					$db->insert('station', array(
						'pid' => $pid,
						'address' => $address
					));
				}
				$db->commit();
				
			} catch (Exception $e) {
				
				$db->rollback();
				Alp_Sys::msg('exception', $e->getMessage());
			}
		}
	}

?>