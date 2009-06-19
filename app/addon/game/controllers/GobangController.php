<?php

    class Addon_Game_GobangController extends Zend_Controller_Action
    {   
        # 房间页面
        function roomAction(){
            $room = $this->_getParam('n');
            $this->view->room = $room;
            $players = Logic_Game_Gobang::players($room);
            $this->view->players = $players;
            $this->view->i = Cmd::uid();
        }
        
        # 聊天记录
        function chatlogAction()
        {
            $room = (int)$this->_getParam('room');
            $db = Logic_Game_Gobang::getRoomDb($room);
            $logs = $db->fetchAll('SELECT * FROM chat ORDER BY time ASC');
            $this->view->logs = $logs;
        }
        
        function chatAction()
        {
            $this->getHelper('viewRenderer')->setNoRender();
            $room = (int)$this->_getParam('room');
            if($this->getRequest()->isPost())
            {
                $talk = trim($this->_getParam('talk'));
                if(strlen($talk) > 0)
                {
                    $object = Logic_Game_Gobang::playername(Cmd::uid());
                    $db = Logic_Game_Gobang::getRoomDb($room);
                    $db->insert('chat', array(
                        'object' => $object,
                        'content' => $talk,
                        'time' => time()
                    ));
                }
            }
        }
        
        # 离开
        function leavegameAction()
        {
            $this->getHelper('viewRenderer')->setNoRender();
            $room = (int)$this->_getParam('room');
            $uid = Cmd::uid();
            $name = Logic_Game_Gobang::playername($uid);
            $db = Logic_Game_Gobang::getRoomDb($room);
            Logic_Game_Gobang::logAction($db, $uid, 'leave');
            Logic_Game_Gobang::chat($db, $name, '离开游戏');
            Logic_Game_Gobang::cleanRoom($room);
        }
        
        # 放子
        function goAction()
        {
            $this->getHelper('viewRenderer')->setNoRender();
            $room = (int)$this->_getParam('room');
            $point = $this->_getParam('point');
            Logic_Game_Gobang::go($room, $point);
            if(Alp_Sys::getMsg() == null) echo 'success';
            else echo Alp_Sys::msg('go_error');
        }
        
        # 聊天 ajax
        function chatboxAction()
        {
            
        }
        
        # 状态 ajax
        function gamestateAction()
        {
            $room = (int)$this->_getParam('room');
            $state = Logic_Game_Gobang::getState($room);
            $players = Logic_Game_Gobang::players($room);
            
            $this->view->state = $state;
            $this->view->players = $players;
        }
        
        # 棋盘 ajax
        function gobangboxAction()
        {
            $room = (int)$this->_getParam('room');
            $db = Logic_Game_Gobang::getRoomDb($room);
            $players = Logic_Game_Gobang::players($room);
            
            $point = array();
            $wp1 = array();
            $wp2 = array();
            
            // 载入p1(白)棋子
            $p1 = $db->fetchAll('SELECT x,y
                                 FROM action WHERE player = ? AND key = "point"',
                                 $players['p1']);
            if(count($p1) > 0)
            foreach($p1 as $v)
            {
                $point['p1'][] = $v['x'].'_'.$v['y'];
                if(($v['x'] > 4 && $v['x'] < 12) || ($v['y'] > 4 && $v['y'] < 12))
                $wp1[] = $v['x'].'_'.$v['y'];
            }

            // 载入p2(黑)棋子
            $p2 = $db->fetchAll('SELECT x,y
                                 FROM action WHERE player = ? AND key = "point"',
                                 $players['p2']);
            if(count($p2) > 0)
            foreach($p2 as $v)
            {
                $point['p2'][] = $v['x'].'_'.$v['y'];
                if(($v['x'] > 4 && $v['x'] < 12) || ($v['y'] > 4 && $v['y'] < 12))
                $wp2[] = $v['x'].'_'.$v['y'];
            }
            
            // 计算是否有赢家
            if(count($wp1) > 0)
            foreach($wp1 as $val)
            {
                if(Logic_Game_Gobang::isWin($val, $point['p1']) == true)
                $point['winner'] = 'p1';
            }
            if(count($wp2) > 0)
            foreach($wp2 as $val)
            {
                if(Logic_Game_Gobang::isWin($val, $point['p2']) == true)
                $point['winner'] = 'p2';
            }
            
            if(isset($point['winner']))
            {
                $winid = $players[$point['winner']];
                // 保存胜负记录
                if($winid == Cmd::uid()) // 由胜者自己保存记录..
                Logic_Game_Gobang::save($players['p1'], $players['p2'], $winid, $room);
            }
            
            echo json_encode($point);
        }
        
        # 进入房间
        function joinAction()
        {
            $this->getHelper('viewRenderer')->setNoRender();
            $room = (int)$this->_getParam('room');
            $player = Cmd::uid();
            Logic_Game_Gobang::join($room, $player);
            if(Alp_Sys::getMsg() == null)
            {
                echo 'success';
            } else echo Alp_Sys::allMsg('* ', "\n");
        }
        
        # 房间状态检查ajax
        function checkroomAction()
        {
            $timeout = Logic_Game_Gobang::$timeout;
            if($this->getRequest()->isXmlHttpRequest())
            {
                // 检测各房间状态
                $num = $this->_getParam('ref_num');
                if((int)$num > 0)
                {
                    $result = Logic_Game_Gobang::roomState($num);
                    $exptime = (int)$result['lastaction'] + $timeout;
                    
                    // 游戏中
                    if($result['p1'] && $result['p2'] && $exptime > time())
                    {
                        $this->view->state = 'playing';
                        $this->view->p1 = $result['p1'];
                        $this->view->p2 = $result['p2'];
                    }
                    
                    // 等待p2中
                    elseif($result['p1'] && $exptime > time())
                    {
                        $this->view->state = 'wait p2';
                        $this->view->p1 = $result['p1'];
                    }
                    
                    // 无人
                    if($exptime < time() || $result == false)
                    {
                        if($exptime < time()) // 清理过期房间
                        Logic_Game_Gobang::cleanRoom($num);
                        $this->view->state = 'wait';
                    }
                }
                
                $this->view->num = $num;
            }
        }
    }
    
?>