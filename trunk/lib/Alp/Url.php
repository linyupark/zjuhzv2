<?php

    class Alp_Url
    {
        // 图片源地址
        public static $imgroot = '/im/';
        
        // 上传目录地址
        public static $uploadroot = '/upload/';
        
        # 返回图片的完整url ::::::::::::::::::::::::::::::::::::
        static function img($file)
        {
            return self::$imgroot.$file;
        }
        
        # 返回上传内容url ::::::::::::::::::::::::::::::::::::::::
        static function upload($file)
        {
            return self::$uploadroot.$file;
        }
    }

?>