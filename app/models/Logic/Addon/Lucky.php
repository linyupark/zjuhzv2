<?php

	class Logic_Addon_Lucky extends DbModel
    {
        const DBNAME = 'lucky.s3db';

        static function getPartyList()
        {
            return parent::getSqlite(self::DBNAME)
            ->fetchAll('SELECT * FROM `party` ORDER BY `stop_at` DESC');
        }

        static function getParty($id)
        {
            return parent::getSqlite(self::DBNAME)
            ->fetchRow('SELECT * FROM `party` WHERE `id` = ?', $id);
        }

        static function getLuckymen($id)
        {
            return parent::getSqlite(self::DBNAME)
            ->fetchAll('SELECT * FROM `luckyman` WHERE `party_id` = ? ORDER BY `got_at` DESC', $id);
        }

        static function luckyLimit($id, $uid, $limit=1)
        {
            $got_lucky_num = parent::getSqlite(self::DBNAME)
            ->fetchOne('SELECT COUNT(id) AS `num` FROM `luckyman` WHERE `party_id` = ? AND `id` = ?', array($id, $uid));
            return (bool) ($got_lucky_num >= $limit);
        }

        static function played($id, $uid)
        {
            parent::getSqlite(self::DBNAME)->insert('log', array(
                'party_id' => $id,
                'user_id' => $uid,
                'play_at' => date('Y-m-d H:i:s')
            ));
        }

        static function bingo($id, $uid, $info)
        {
            $db = parent::getSqlite(self::DBNAME);
            $db->beginTransaction();
			try
			{
                $data = array(
                    'id' => $uid,
                    'party_id' => $id,
                    'info' => $info,
                    'got_at' => date('Y-m-d H:i:s')
				);
                $db->insert('luckyman', $data);
                $db->commit();
            } catch (Exception $e) {
                $db->rollback();
            }
        }

        static function isPlayed($id, $uid)
        {
            $row = parent::getSqlite(self::DBNAME)
            ->fetchRow('SELECT * FROM `log` WHERE `user_id` = ? AND `party_id` = ?', array($uid, $id));
            if( ! $row) return false;
            else
            {
                $today = date('Y-m-d');
                if(strstr($row['play_at'], $today))
                return true;
                else return false;
            }
        }

        static function save($params)
        {
            $db = parent::getSqlite(self::DBNAME);
			$db->beginTransaction();
			try
			{
                $data = array(
                    'name' => $params['name'],
                    'content' => $params['content'],
                    'lucky_num' => $params['lucky_num'],
                    'lucky_rate' => $params['lucky_rate'],
                    'lucky_limit' => $params['lucky_limit'],
                    'start_at' => $params['start_at'],
                    'stop_at' => $params['stop_at']
				);

                if((int)$params['id'] > 0)
                {
                    $db->update('party', $data, 'id = '.$params['id']);
                    $id = $params['id'];
                }
                else
                {
                    $db->insert('party', $data);
                    $id = $db->lastInsertId();
                }
				$db->commit();
				return $id;

			} catch (Exception $e) {

				$db->rollback();
				Alp_Sys::msg('exception', $e->getMessage());
			}
			return false;
        }
    }