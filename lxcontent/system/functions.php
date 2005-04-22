<?php
	/**
	 * returns the absolute URL of $relative_path
	 * Don't work with symbolic links!!!
	 */
	function htmlpath($relative_path) {
		$realpath=realpath($relative_path);
		$htmlpath=str_replace($_SERVER['DOCUMENT_ROOT'],'',$realpath);
		return $htmlpath;
	}
	
	/**
	 * strips the $tag-tag from $text
	 */
	function stripTag($tag, $text)
	{
		$text = str_replace("<$tag>", "", $text);
		$text = str_replace("</$tag>", "", $text);
		return $text;
	}

	/**
	 * gets categroy name from given XML
	 */
	function getCategoryName($xmlParser, $catdir)
	{
		if ($xmlParser->match("/directory/category_name"))
		{
			$cat = $xmlParser->getData("/directory/category_name");
		}
		else
		{
			$cat = getPathElement($catdir, 1);
		}
		return $cat;
	}
	
	/**
	 * Splits the given filename into the filename and the
	 * parts of the path and returns an array.
	 * The first element of the array is the directory with
	 * the highest level, the last element is the filename
	 * itself.
	 */
	function splitPath($filename)
	{
		$parts = explode("/", $filename);
		return $parts;
	}

	/**
	 * Returns the the $element_nr element of $filename
	 * If $element_nr is too large or lesser then 0
	 * the function returns the last element of $filename.
	 */
	function getPathElement($filename, $element_nr)
	{
		$elements = splitPath($filename);
		if (($element_nr >= count($elements)) or ($element_nr < 0))
		{
			return $elements[count($elements)-1];
		}
		return $elements[$element_nr];
	}
	
	function readDirectoryFiles($dir)
	{
		$ret = array();
		$handle = opendir($dir);
		while (false !== ($file = readdir($handle)))
		{
			if (($file[0] != '.') and (is_file($dir . "/" . $file)))
				$ret[] = $file;
		}
		closedir($handle);
		return $ret;
	}

  /**
    * Reads out an directory an returns an array
    * with the founded files (included the relativ path
    */
   function readDirectoryRecursiv($path)
   {
      $result = array();

      $handle = opendir($path);

      if ($handle)
      {
         while (false !== ($file = readdir($handle)))
         {
             if ($file != "." && $file != "..")
             {
                $name = $path . "/" . $file;
                if (is_dir($name))
                {
                   $ar = readDirectoryRecursiv($name);
                   foreach ($ar as $value)
                   {
                      $result[] = $value;
                   }
                }
                else
                {
                   $result[] = $name;
                }
             }
         }
      }
      closedir($handle);
      return $result;
   }
	
	/**
	 * Put the elements of $array together to
	 * a string. Between the elements the
	 * function puts $catstr.
	 */
	function catArray($array, $catstr="")
	{
		$str = "";
		for ($i=0; $i<count($array); $i++)
		{
			$str .= $array[$i];
			if ($i != (count($array)-1))
				$str .= $catstr;
		}
		return $str;
	}

	/**
	 * Extracts the path of a filename.
	 * For example $filename is "foo/bar/test.txt"
	 * the function returns "foo/bar"
	 */
	function getPath($filename)
	{
		$elements = splitPath($filename);
		unset($elements[count($elements)-1]);
		$path = catArray($elements, "/");
		return $path;
	}


	/**
	 * Returns the extension of a given filename.
	 */
	function extractFileExt($filepath)
	{
		$filename = getPathElement($filepath, -1);
		$parts = explode(".", $filename);
		return $parts[count($parts)-1];
	}
	
	/**
	 * the function returns the given file with extension.
	 */
	function getWholeFilepath($filepath_without_ext)
	{
		$all_files = readDirectoryRecursiv(getPath($filepath_without_ext));
		for ($i=0; $i<count($all_files); $i++)
		{
			if (strstr($all_files[$i], $filepath_without_ext) !== false)
			{
				return $all_files[$i];
			}
		}
		return "";
	}


?>
