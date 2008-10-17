<?php

    class Plugins_Layout extends Zend_Controller_Plugin_Abstract
    {
        public function preDispatch(Zend_Controller_Request_Abstract $request)
        {
            $layout = 'default'; // 默认布局名
            $path = $_SERVER['DOCUMENT_ROOT'].'/layout/';
            $file = $path.$request->module.'.phtml';
            
            if(file_exists($file))
            $layout = $request->module;
            
            $options = array(
                'layoutPath'    => $path,
                'layout'        => $layout
            );
            
            $layout = Zend_Layout::startMvc($options);
            
            if($request->isXmlHttpRequest())
            {
                $layout->disableLayout();
            }
        }
    }

?>