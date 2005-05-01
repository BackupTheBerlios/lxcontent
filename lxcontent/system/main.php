<?php
	/**
	 * lX Content is a simple XML and XHTML based
	 * content management system.
	 * It's licensed under the GNU General Public License
	 * (c) 2005 Bodo Akdeniz
	 */
	include("system/XPath.class.php");
	include("conf/config.php");
	include("system/functions.php");
	include("system/output.php");
	include("system/menu.php");
	include("system/template.php");
	
	$var = array();

	$var['start_time'] = time();

	// set all values to default
	$var['filepath_without_ext'] = $config['start_page'];
	$var['template'] = $config['template'];
	$var['title'] = $config['title'];
	$var['describtion'] = $config['describtion'];
	$var['keywords'] = $config['keywords'];
	$var['script'] = $config['default_script'];
	$var['stylesheet'] = $config['stylesheet'];
	
	// get given filename (without extension!)
	if (isset($_GET['p']) and ($_GET['p'] != ""))
	{
		$var['filepath_without_ext'] = $_GET['p'];
	}

	// get filename with path and extension
	$var['filepath'] = getWholeFilePath("content/" . $var['filepath_without_ext']);

	// extract filename (without_path)
	$var['filename'] = getPathElement($var['filepath'], -1);
	
	// extract category-directory
	if ($var['filepath'] != "")
		$var['catdir'] = getPath($var['filepath']);
	else
		$var['catdir'] = "foobar";

	// setting error filepath if necsessary
	if ($var['filepath'] == "")
	{
		$var['filepath'] = "template/" . $var['template'] . "/error_pages/404.html";
	}
	
	// setting up xml parser
	$xmlParser = new XPath($var['filepath']);
	
	// get page title
	if ($xmlParser->match("/html/head/title"))
		$var['page_title'] = $xmlParser->getData("/html/head/title");
	elseif ($xmlParser->match("/content/title[1]"))
		$var['page_title'] = $xmlParser->getData("/content/title[1]");
	else
		$var['page_title'] = $var['filename'];
	$var['title'] = $var['title'] . " - " . $var['page_title'];

	// get keywords
	if ($xmlParser->match("//meta[@name='KEYWORDS']"))
		$var['keywords'] = $var['keywords'] . "," . $xmlParser->getData("//meta[@name='KEYWORDS']/attribute::content");
	
	// get describtion
	if ($xmlParser->match("//meta[@name='DESCRIBTION']"))
		$var['describtion'] = $xmlParser->getData("//meta[@name='DESCRIBTION']/attribute::content");
	
	// generating content
	$var['file_ext'] = extractFileExt($var['filename']);
	switch ($var['file_ext'])
	{
		case "html"  :
		case "xhtml" :
			$var['content'] = html_output($xmlParser);
			break;
		case "xml" :
			$var['content'] = generateContentFromXml($xmlParser);
			break;
	}


	// category directory stuff
	if (file_exists($var['catdir'] . "/directory.xml"))
	{
		// generate some stuff with directory.xml
		$xmlParser = new XPath($var['catdir'] . "/directory.xml");

		// get category name
		$var['category'] = getCategoryName($xmlParser, $catdir);

		// generate (sub)menu
		if ($xmlParser->match("/directory[1]/menu[1]"))
		{
			$var['catmenu'] = menuFromXml($xmlParser->exportAsXml("/directory[1]/menu[1]"), $var['catdir']);
		}
		else
		{
			$var['catmenu'] = menuFromCatdir($var['catdir']);
		}

		// get category stylesheet
		if ($xmlParser->match("/directory/stylesheet"))
		{
			$var['stylesheet'] = $xmlParser->getData("/directory/stylesheet");
		}
	}
	else
	{
		// generate all "by hand"
		$var['category'] = getPathElement($var['catdir'], 1);
		$var['catmenu'] = menuFromCatdir($var['catdir']);
	}

	// generate mainmenu
	$var['mainmenu'] = mainMenu();

	// set some more variables to use in template
	$var['absolute_content_filename'] = htmlpath($var['filepath']);
	
	// calculate runtime
	$var['end_time'] = time();
	$var['generation_time'] =  $var['end_time'] - $var['start_time'];

	if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml") ) {
		header("Content-type: application/xhtml+xml");
	} else {
		header("Content-type: text/html");
	}

	$output = template($var, "templates/" . $var['template'] . "/main.html");
	echo $output;
?>
