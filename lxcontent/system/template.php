<?php
	/**
	 * applies the data of $vars (array) to the given
	 * templatefile (also given in $vars)
	 */
	function template($vars, $templatefile)
	{
		$templatefile = $templatefile;
		if (file_exists($templatefile))
		{
			$fp = fopen($templatefile, "r");
			$template = fread($fp, filesize($templatefile));
			fclose($fp);
			
			foreach ($vars as $name => $value)
			{
				$template = str_replace("%$name%", $value, $template);
			}
		}
		else
		{
			return "Templatefile ($templatefile) does not exist!";
		}
		return $template;
	}
?>
