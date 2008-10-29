<?php

	class Logic_Addon_Vote extends DbModel 
	{
		/**
		 * 返回指定vid投票的总票数
		 *
		 * @param int $vid
		 * @return int
		 */
		public static function totalRate($vid)
		{
			$options = self::options($vid);
			if(count($options) > 0)
			{
				$total = 0;
				foreach($options as $v)
				{
					$total += $v['rate'];
				}
				return $total;
			}
			return 0;
		}
		
		/**
		 * 获取某oid的票数
		 *
		 * @param int $oid
		 * @return int
		 */
		public static function rate($oid)
		{
			$row = parent::getSqlite('vote.s3db')->fetchRow('SELECT `rate` FROM `options` WHERE `oid` = ?', $oid);
			if($row != false)
			return $row['rate'];
			else return 0;
		}
		
		/**
		 * 获取指定vid的选项信息
		 *
		 * @param int $vid
		 * @return array
		 */
		public static function options($vid)
		{
			return parent::getSqlite('vote.s3db')->fetchAll('SELECT * FROM `options` WHERE `vid` = ?', $vid);
		}
		
		
		/**
		 * 获取指定vid的投票基本信息
		 *
		 * @param int $vid
		 * @return array
		 */
		public static function base($vid)
		{
			return parent::getSqlite('vote.s3db')->fetchRow('SELECT * FROM `base` WHERE `vid` = ?', $vid);
		}
		
		/**
		 * 插入新投票数据
		 *
		 * @param array $params
		 * @return boolean
		 */
		public static function insert($params)
		{
			$db = parent::getSqlite('vote.s3db');
			
			$base_data = array(
				'title' => $params['title'],
				'time' => time(),
				'memo' => $params['memo'],
				'mulit' => $params['mulit']
			);
			
			$db->beginTransaction();
			
			try {
				$db->insert('base', $base_data);
				$vid = $db->lastInsertId();
				foreach($params['options'] as $opt_value)
				$db->insert('options', array('vid' => $vid, 'value' => $opt_value, 'rate' => 0));
				$db->commit();
				return $vid;
				
			} catch (Exception $e) {
				$db->rollback();
				Alp_Sys::msg('exception', $e->getMessage());
			}
			return 0;
		}
	}

?>