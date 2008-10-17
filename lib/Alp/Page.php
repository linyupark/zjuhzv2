<?php

	class Alp_Page
	{
	  public static $_set = array("href_open" => "", "href_close" => "", "num_rows" => 0, "cur_page" => 1, );
	  public static $page_str = ""; #分页内容
	  public static $num_pages = 0; #总页数
	  public static $offset = 0;
	  public static $link_span = 4; #分页跨度
	  public static $pagesize = 10; #分页大小
	  public static $text_open = "<b>"; #当前页头
	  public static $text_close = "</b>"; #当前页尾
	  public static $first = "第一页";
	  public static $prev = "前一页";
	  public static $next = "下一页";
	  public static $last = "最后页";
	
	  static function init($_set)
	  {
	    if (!is_array($_set))
	      return ;
	    foreach($_set as $k => $v)
	    {
	      if (isset(self::$_set[$k]))
	        self::$_set[$k] = $v;
	    }
	  }
	
	  static function create($_set)
	  {
	    self::init($_set);
	    //是否需要分页
	    if (self::$_set['num_rows'] <= self::$pagesize)
	      return ;
	    //计算总页
	    self::$num_pages = ceil(self::$_set['num_rows'] / self::$pagesize);
	    
	    if (self::$_set['cur_page'] == 0)
	    self::$_set['cur_page'] = 1;
	    
	    //分页内容初始化
	    $page_str = "";
	    //首,上
	    if (self::$_set['cur_page'] != 1)
	    {
	      $page_str .= sprintf(self::$_set['href_open'].self::$first.self::$_set['href_close'], 1);
	      $page_str .= sprintf(self::$_set['href_open'].self::$prev.self::$_set['href_close'], (self::$_set['cur_page'] - 1));
	    }
	    //开始index
	    if (self::$_set['cur_page'] - self::$link_span > 1)
	    {
	      $start = self::$_set['cur_page'] - self::$link_span;
	    }
	    else
	      $start = 1;
	    //结束index
	    if (self::$_set['cur_page'] + self::$link_span > self::$num_pages)
	    {
	      $end = self::$num_pages;
	    }
	    else
	      $end = self::$_set['cur_page'] + self::$link_span;
	    //循环index
	    for ($i = $start; $i <= $end; $i++)
	    {
	      if ($i == self::$_set['cur_page'])
	        $page_str .= sprintf(self::$text_open.$i.self::$text_close, $i);
	      else
	        $page_str .= sprintf(self::$_set['href_open'].$i.self::$_set['href_close'], $i);
	    }
	    //下,尾
	    if (self::$_set['cur_page'] != self::$num_pages)
	    {
	      $page_str .= sprintf(self::$_set['href_open'].self::$next.self::$_set['href_close'], (self::$_set['cur_page'] + 1));
	      $page_str .= sprintf(self::$_set['href_open'].self::$last.self::$_set['href_close'], self::$num_pages);
	    }
	    self::$offset = (self::$_set['cur_page'] - 1) * self::$pagesize;
	    return self::$page_str = $page_str;
	  }
	}

?>