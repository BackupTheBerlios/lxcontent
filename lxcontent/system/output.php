<?php
	/**
	 * strip the body-tags of text
	 */
	function stripBodyTag($text)
	{
		$text = stripTag("body", $text);
		return $text;
	}
	
	/**
	 * generates the output from a xhtml-file
	 */
	function html_output(&$xmlParser)
	{
		if ($xmlParser->match("/html[1]/body[1]"))
			return stripBodyTag($xmlParser->exportAsXml("/html[1]/body[1]"));
		else
			return "No content found!";
	}

	/**
	 * detects the script which should be used to
	 * generate content from an xml file and execute it.
	 */
	function generateContentFromXml($xmlParser)
	{
		if ($xmlParser->match("/content[1]/script[1]"))
		{
			$script = $xmlParser->getData("/content[1]/script[1]");
			include("scripts/$script.php");
			return generateOutput($xmlParser);
		}
		else
		{
			return "Don't know what script to use for generating output!";
		}
	}
?>
