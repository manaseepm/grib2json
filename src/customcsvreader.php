<?php

class CustomCsvReader {

	public $key = array();

	public function file_get_contents_chunked($file,$chunk_size,$jsonoutfile,$callback)
	{
	    try
	    {
	        $handle = fopen($file, "r");
	        $outhandle = fopen($jsonoutfile, "a");
	        $i = 0;
	        while (!feof($handle))
	        {
	            call_user_func_array($callback,array(fgets($handle,$chunk_size),&$handle,$i, &$outhandle));
	            $i++;
	        }

	        fclose($handle);

	    }
	    catch(Exception $e)
	    {
	         trigger_error("file_get_contents_chunked::" . $e->getMessage(),E_USER_NOTICE);
	         return false;
	    }

	    return true;
	}

	public function convert_file($csvfile, $ini) {
		//get the first line which is the headers
		$start = microtime(true);
		//get first line of the file, these are the headers
		$line = fgets(fopen($csvfile, 'r'));
		$this->key = explode(",", $line);
		$json = array();
		$outfile = $this->get_string_between($csvfile, "csv/", ".");
		$jsonoutfile = $ini['JSON_FOLDER'].$outfile.".json";
    
		//get next chunks and convert to json
		$success = $this->file_get_contents_chunked($csvfile,1024,$jsonoutfile,function($chunk,&$handle,$iteration,&$outhandle)
		{
		    $json = $this->combine_keys(explode(",",$chunk));
		    fwrite($outhandle, json_encode($json));    
		});

		if(!$success)
		{
		    //It Failed
		}

		//fclose($outhandle);
		$time_elapsed = microtime(true) - $start;
		//delete the csv file after conversion
		unlink($csvfile);

	}

	public function combine_keys($chunk) {
		$line = array();
		try {
			$line = array_combine($this->key, $chunk);
		} catch (Exception $e) {
			echo $e.' variables are '.print_r($chunk);
		}
		return $line;
	}

	function get_string_between($string, $start, $end){
	    $string = ' ' . $string;
	    $ini = strpos($string, $start);
	    if ($ini == 0) return '';
	    $ini += strlen($start);
	    $len = strpos($string, $end, $ini) - $ini;
	    return substr($string, $ini, $len);
	}
}
?>