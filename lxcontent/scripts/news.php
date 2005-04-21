<?php
	/**
	 * generates news articles
	 */
	function generateOutput($xmlParser)
	{
		$conf['articles_per_site'] = 10;
		$conf['article_template'] = "scripts/news/templates/article.html";
		$conf['template'] = "scripts/news/templates/page.html";
		
		if (file_exists("conf/news.conf.php"))
		{
			include("conf/news.conf.php");
		}
		
		$page = 1;
		if (isset($_GET['page']) and ($_GET['page'] != ""))
			$page = $_GET['page'];
		
		$content['articles']= "";
		
		if (!isset($_GET['page']) and $xmlParser->match("/content[1]/header[1]"))
		{
			$content['header'] .= "<div class=\"news_header\">\n" . stripTag("header", $xmlParser->exportAsXml("/content[1]/header[1]")) . "\n</div>\n";
		}
		
		if (!isset($_GET['page']) and $xmlParser->match("/content[1]/footer[1]"))
		{
			$content['footer'] .= "<div class=\"news_footer\">\n" . stripTag("footer", $xmlParser->exportAsXml("/content[1]/footer[1]")) . "\n</div>\n";
		}
		
		$i = ($page-1)*$conf['articles_per_site']+1;
		while (($i < $page+$conf['articles_per_site']) and $xmlParser->match("/content[1]/news[1]/article[$i]"))
		{
			$news['author'] = $xmlParser->getData("/content[1]/news[1]/article[$i]/author[1]");
			$news['date'] = $xmlParser->getData("/content[1]/news[1]/article[$i]/date[1]");
			$news['title'] = $xmlParser->getData("/content[1]/news[1]/article[$i]/title[1]");
			$news['text'] = stripTag("text", $xmlParser->exportAsXml("/content[1]/news[1]/article[$i]/text[1]"));
			$content['articles'].= template($news, $conf['article_template']);
			$i++;
		}
		$html = template($content, $conf['template']);
		return $html;
	}
?>
