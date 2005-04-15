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
?>
