<?php

	/**
	 * 记录一些有关联的操作
	 *
	 */
	class Logic_Log extends DbModel 
	{
		public static function insert($tb, $params)
		{
			parent::Log()->insert($tb, $params);
		}
	}

?>