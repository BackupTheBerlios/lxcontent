<?php
	function getTitle($filename)
	{
	
		$xmlParser = new XPath($filename);
		if ($xmlParser->match("/html/head/title"))
			$title = $xmlParser->getData("/html/head/title");
		elseif ($xmlParser->match("/content/title"))
			$title = $xmlParser->getData("/content/title");
		else
			$title = getPathElement($filename, -1);
		return $title;
	}

	/**
	 * Returns mainpage of category (without path).
	 * Given values are the category-directory (withour "content/")
	 * and a XPath object with loaded directory.xml
	 */
	function getCategoryMainpage($catdir, $xmlParser=NULL)
	{
		if ($xmlParser != NULL)
		{
			if ($xmlParser->match("/directory/main_page"))
			{
				return $xmlParser->getData("/directory/main_page");
			}
		}
		$files = readDirectoryFiles("content/$catdir");
		$i=0;
		while ($i < count($files))
		{
			if (extractFileExt(($page=$files[$i])) == "html")
				return $page;
			$i++;
		}
		return "";
	}

	/**
	 * generates a Link for the mainmenu
	 */
	function generateMainMenuItem($catdir)
	{
		$xmlParser = NULL;
		if (file_exists("content/$catdir/directory.xml"))
		{
			$xmlParser = new XPath("content/$catdir/directory.xml");
			$title = getCategoryName($xmlParser, $catdir);
		}
		else
		{
			$title = $catdir;
		}
		$link = $catdir . "/" . getCategoryMainpage($catdir, $xmlParser);
		
		return "<a href=\"index.php?p=$link\">$title</a>\n";
	}

	/**
	 * generates the mainmenu an returns the html code
	 */
	function mainMenu()
	{
		$html = "";
		$dir = opendir("content");
		while ($file=readdir($dir))
		{
			if (($file!=".") and ($file!="..") and (is_dir("content/" . $file)))
			{
				$files[] = $file;
			}
		}
		sort($files);
		foreach ($files  as $file)
		{
			$html .= "<span class=\"mainmenu\">\n";
			$html .= generateMainMenuItem($file);
			$html .= "</span>";
		}
		return $html;
	}
	
	/**
	 * generates the category menu from the menu data
	 * of directory.xml
	 */
	function menuFromXml($xmlData, $catdir)
	{
		$html = "";
		$subdir = substr($catdir, strlen("content/"));
		$xmlParser = new XPath();
		$xmlParser->importFromString($xmlData);
		$i = 1;
		while ($xmlParser->match("/menu[1]/item[$i]"))
		{
			$html .= "<span class=\"menu\">\n";
			if ($xmlParser->match("/menu[1]/item[$i]/file[1]"))
			{
				$filename = $xmlParser->getData("/menu[1]/item[$i]/file[1]");
				$title = getTitle($catdir . "/" . $filename);
				$link = $subdir . "/" . $filename;
				$html .= "<a href=\"index.php?p=$link\">$title</a>\n";
			}
			elseif ($xmlParser->match("/menu[1]/item[$i]/raw_data[1]"))
			{
				$data =	$xmlParser->exportAsXml("/menu[1]/item[$i]/raw_data[1]");
				$data = stripTag("raw_data", $data);
				$html .= $data;
			}
			$html .= "</span>\n";
			$i++;
		}
		return $html;
	}

	/**
	 * generates the Menu from the titles of the
	 * files in $catdir
	 */
	function menuFromCatdir($catdir)
	{
		$html = "";
		$subdir = substr($catdir, strlen("content/"));
		$files = readDirectoryFiles($catdir);
		foreach ($files as $filename)
		{
			if ($filename != "directory.xml")
			{
				$link = $subdir . "/" . $filename;
				$title = getTitle($catdir . "/" . $filename);
				$html .= "<span class=\"menu\"><a href=\"index.php?p=$link\">$title</a></span>\n";
			}
		}
		return $html;
	}
?>
