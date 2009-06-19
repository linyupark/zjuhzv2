<?php

    class Addon_Game_IndexController extends Zend_Controller_Action
    {   
        function indexAction()
        {
            
        }
        
        # 大厅 ------------------------------------
        
        // 五子棋
        function gobangAction()
        {
            // 房间数量设置
            $room_num = 8;
            
            // 用户数据初始化
            Logic_Game_Gobang::init_player();
            
            // 罗列房间
            $this->view->rooms = $room_num;
        }
    }

?>