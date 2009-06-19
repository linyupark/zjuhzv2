<?php

    class Logic_Game_Gobang
    {
        public static $timeout = 300; #5分钟超时房间自动清理
        
        # 初始化新玩家数据
        static function init_player()
        {
            $db = DbModel::getSqlite('gobang/base.s3db');
            $user = Cmd::getSess('profile');
            $result = $db->fetchRow('SELECT uid FROM players WHERE uid = ?',
                                    $user['uid']);
            // 创建新玩家数据
            if($result == false) $db->insert('players', array(
                'uid' => $user['uid'],
                'name' => $user['username'],
                'winnum' => 0
            ));
        }
        
        # 保存游戏结局
        static function save($p1, $p2, $winner, $room)
        {
            $db = DbModel::getSqlite('gobang/base.s3db');
            $db->insert('history', array(
                'p1' => $p1,
                'p2' => $p2,
                'winner' => $winner,
                'time' => time()
            ));
        }
        
        # 判断是否获胜
        static function isWin($point, $points)
        {
            $ps = explode('_', $point);
            $x = (int)$ps[0];
            $y = (int)$ps[1];
            
            // 判断8个方向第5个子是否同色(左上开始顺时针)
            if(in_array(($x-4).'_'.($y-4), $points))
            {
                $lt = 1;
                for($i = 1; $i < 4; $i ++)
                {
                    if(!in_array(($x-$i).'_'.($y-$i), $points))
                    $lt = 0;
                }
            }
            if(in_array($x.'_'.($y-4), $points))
            {
                $t = 1;
                for($i = 1; $i < 4; $i ++)
                {
                    if(!in_array($x.'_'.($y-$i), $points))
                    $t = 0;
                }
            }
            if(in_array(($x+4).'_'.($y-4), $points))
            {
                $tr = 1;
                for($i = 1; $i < 4; $i ++)
                {
                    if(!in_array(($x+$i).'_'.($y-$i), $points))
                    $tr = 0;
                }
            }
            if(in_array(($x+4).'_'.$y, $points))
            {
                $r = 1;
                for($i = 1; $i < 4; $i ++)
                {
                    if(!in_array(($x+$i).'_'.$y, $points))
                    $r = 0;
                }
            }
            if(in_array(($x+4).'_'.($y+4), $points))
            {
                $rb = 1;
                for($i = 1; $i < 4; $i ++)
                {
                    if(!in_array(($x+$i).'_'.($y+$i), $points))
                    $rb = 0;
                }
            }
            if(in_array($x.'_'.($y+4), $points))
            {
                $b = 1;
                for($i = 1; $i < 4; $i ++)
                {
                    if(!in_array($x.'_'.($y+$i), $points))
                    $b = 0;
                }
            }
            if(in_array(($x-4).'_'.($y+4), $points))
            {
                $lb = 1;
                for($i = 1; $i < 4; $i ++)
                {
                    if(!in_array(($x-$i).'_'.($y+$i), $points))
                    $lb = 0;
                }
            }
            if(in_array(($x-4).'_'.$y, $points))
            {
                $l = 1;
                for($i = 1; $i < 4; $i ++)
                {
                    if(!in_array(($x-$i).'_'.$y, $points))
                    $l = 0;
                }
            }
            
            return ($lt||$t||$tr||$r||$rb||$b||$lb||$l);
        }
        
        # 放子
        static function go($room, $point)
        {
            $db = self::getRoomDb($room);
            
            // 获取坐标
            $point = explode('_', $point);
            $x = $point[0]; $y = $point[1];
            
            // 是否可放
            $row = $db->fetchRow('SELECT id FROM action WHERE x = ? AND y = ?',
                          array($x, $y));
            if($row != false)
            Alp_Sys::msg('go_error', '无效的摆放位置');
            else
            // 插入action,更新lastaction
            self::logAction($db, Cmd::uid(), 'point', $x, $y);
        }
        
        # 获取游戏过程中的状态
        static function getState($room)
        {
            $db = self::getRoomDb($room);
            $room_state = self::roomState($room);
            $players = self::players($room);
            
            // 强制离开
            if($room_state['lastaction'] + self::$timeout < time())
            return 'focus leave';
            
            // 是否p1,p2都到位
            $row = $db->fetchRow('SELECT COUNT(id) AS nums
                                 FROM action WHERE key = "join"');
            if($row['nums'] < 2) return 'waiting';
    
            // 轮到谁下        
            $last_turn = $db->fetchRow('SELECT `player` FROM action
                                       WHERE `key` = "point"
                                       ORDER BY `time` DESC');
            // 默认p1先下
            if($last_turn == false || $last_turn['player'] == $players['p2'])
            return 'p1 turn';
            else return 'p2 turn';
        }
        
        # 获取玩家名称
        static function playername($uid)
        {
            $db = DbModel::getSqlite('gobang/base.s3db');
            $row= $db->fetchRow('SELECT name FROM players WHERE uid = ?', $uid);
            return $row['name'];
        }
        
        # 是否已经加入
        static function isJoin($room_num, $player)
        {
            $db = self::getRoomDb($room_num);
            return $db->fetchRow('SELECT id FROM action
                          WHERE player = ? AND key = "join"', $player);
        }
        
        # 进入房间
        static function join($room_num, $player)
        {
            $now = time();
            $state = self::roomState($room_num, self::$timeout);
            $db = self::getRoomDb($room_num);

            $db->beginTransaction();
            try {
                if($state == false) // 第一个加入房间的玩家
                {
                    // 更新房间状态
                    $db->insert('state', array(
                        'p1' => $player,
                        'lastaction' => $now
                    ));
                }
                elseif(!$state['p2'] && $player != $state['p1']) // 第二个玩家进入
                {
                    // 更新房间状态
                    $db->update('state', array(
                        'p2' => $player,
                        'lastaction' => $now
                    ), 'p1 = '.$state['p1']);
                }
                else
                {
                    Alp_Sys::msg('join_error', '该房间已满座');
                }
                
                if(!self::isJoin($room_num, $player))
                {
                    self::logAction($db, $player, 'join');
                    self::chat($db, self::playername($player), '加入游戏');
                }
                $db->commit();
                
            } catch (Exception $e) {
                $db->rollback();
                Alp_Sys::msg('join_error', $e->getMessage());
            }
        }
        
        # 清理过期房间
        static function cleanRoom($room_num)
        {
            $db = self::getRoomDb($room_num);
            $db->delete('state');
            $db->delete('chat');
            $db->delete('action');
        }
        
        # 房间状态
        static function roomState($room_num)
        {
            $db = self::getRoomDb($room_num);
            return $db->fetchRow('SELECT * FROM `state`');
        }
        
        # 获取房间数据库对象
        static function getRoomDb($room_num)
        {
            return DbModel::getSqlite('gobang/room_'.$room_num.'.s3db');
        }
        
        # 行动记录
        static function logAction($db, $player, $key, $x=null, $y=null)
        {
            $now = time();
            $db->insert('action', array(
                'player' => $player,
                'key' => $key,
                'x' => $x,
                'y' => $y,
                'time' => $now
            ));
            
            $db->update('state', array(
                'lastaction' => $now
            ), 'lastaction < '.$now);
        }
        
        # 插入聊天
        static function chat($db, $obj, $content)
        {
            $db->insert('chat', array(
                'object' => $obj,
                'content' => $content,
                'time' => time()
            ));
        }
        
        # 返回某房间的玩家id
        static function players($room)
        {
            return self::getRoomDb($room)->fetchRow('SELECT p1,p2 FROM state');
        }
    }

?>