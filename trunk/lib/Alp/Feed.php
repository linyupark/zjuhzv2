<?php
	class Alp_Feed
	{
		/*
		 	// 必 -------------------------------------
			'title' => '', 
			'link' => '',
			'description' => '',
			// 选 -------------------------------------
			'language' => '', //Chinese (Simplified): zh-cn English (United States): en-us
			'copyright' => '',
			'managingEditor' => '',
			'webMaster' => '',
			'pubDate' => '', // Sat, 07 Sep 2002 00:00:01 GMT
			'lastBuildDate' => '', // Sat, 07 Sep 2002 09:42:31 GMT
			'category' => '', // Specify one or more categories that the channel belongs to
			'generator' => '', 
			'docs' => '', // http://blogs.law.harvard.edu/tech/rss
			'cloud' => '', // Allows processes to register with a cloud to be notified of updates to the channel, implementing a lightweight publish-subscribe protocol for RSS feeds
			'ttl' => '', // ttl stands for time to live. It's a number of minutes that indicates how long a channel can be cached before refreshing from the source
			'image' => '', // Specifies a GIF, JPEG or PNG image that can be displayed with the channel
			'rating' => '', // The PICS rating for the channel.
			'textInput' => '', // Specifies a text input box that can be displayed with the channel.
			'skipHours' => '', // A hint for aggregators telling them which hours they can skip.
			'skipDays' => '', // A hint for aggregators telling them which days they can skip.
		 */
		
		protected static $rss_items = array(); // 单元汇集器
		
		/**
			 * title, link, description, author, category, 
			 * comments: URL of a page for comments relating to the item
			 * enclosure: Describes a media object that is attached to the item
			 * guid: A string that uniquely identifies the item
			 * pubDate: Indicates when the item was published
			 * source: The RSS channel that the item came from
			 */ 
		public static function addRssItem($item)
		{
			self::$rss_items[] = $item;
		}
		
		public static function rest()
		{
			self::$rss_items = array();
		}
		
		public static function generateChannel($channel)
		{
			$header = '<channel>';
			foreach ($channel as $key => $val)
			{
				$channel .= "<{$key}>{$val}</{$key}>\n";
			}
			if(count(self::$rss_items) > 0)
			{
				$items = '';
				foreach (self::$rss_items as $v)
				{
					$items .= '<item>';
					foreach ($v as $key => $val)
					{
						$items .= "<{$key}>{$val}</{$key}>\n";
					}
					$items .= '</item>';
				}
			}
			$footer = '</channel>';
			return $header.$channel.$items.$footer;
		}
	}
?>