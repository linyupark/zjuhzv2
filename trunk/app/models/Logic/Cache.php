<?php

	class Logic_Cache extends DbModel 
	{
		public static function factory($type = 'Output', $life = 900)
		{
			$frontendOptions = array(
			   'lifetime' => $life,
			   'automatic_serialization' => true
			);
			
			$backendOptions = array(
			    'cache_dir' => '../cache/'
			);
			
			return Zend_Cache::factory($type, 'File', $frontendOptions, $backendOptions);
		}
	}

?>