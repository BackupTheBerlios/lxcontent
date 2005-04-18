<?php
	/**
	 * generates news articles
	 */
	function generateOutput($xmlParser)
	{
		$conf['articles_per_site'] = 10;
		$conf['template'] = "scripts/news/templates/article.html";
		
		if (file_exists("conf/news.conf.php"))
		{
			include("conf/news.conf.php");
		}
		
		$page = 1;
		if (isset($_GET['page']) and ($_GET['page'] != ""))
			$page = $_GET['page'];
		
		$html = "";
		
		if (!isset($_GET['page']) and $xmlParser->match("/content[1]/welcome_text[1]"))
		{
			$html .= "<div class=\"welcome_text\">\n" . stripTag("text", $xmlParser->exportAsXml("/content[1]/welcome_text[1]")) . "\n</div>\n";
		}
		
		$i = ($page-1)*$conf['articles_per_site']+1;
		while (($i < $page+$conf['articles_per_site']) and $xmlParser->match("/content[1]/article[$i]"))
		{
			$news['author'] = $xmlParser->getData("/content[1]/article[$i]/author[1]");
			$news['date'] = $xmlParser->getData("/content[1]/article[$i]/date[1]");
			$news['title'] = $xmlParser->getData("/content[1]/article[$i]/title[1]");
			$news['text'] = stripTag("article", $xmlParser->exportAsXml("/content[1]/article[$i]/text[1]"));
			$html .= template($news, $conf['template']);
			$i++;
		}
		//TODO: use a template for the whole news-site too!
		return $html;
	}
?>
