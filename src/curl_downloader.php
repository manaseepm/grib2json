 <?php

 class CurlDownloader {

 	public function download($url, $filename) {
 		//start time count to check download times
	 	$start = microtime(true);
	 	//download using curl
	     // 
	    $fp = fopen($filename, 'w');
	 
	    $ch = curl_init($url);
	    curl_setopt($ch, CURLOPT_FILE, $fp);
	 
	    $data = curl_exec($ch);
	 
	    curl_close($ch);
	    fclose($fp);
	    //end download with curl

	     //download using chunking
	 //    define('BUFSIZ', 4095);
		// $rfile = fopen($url, 'r');
		// $lfile = fopen($filename, 'w');
		// while(!feof($rfile))
		// fwrite($lfile, fread($rfile, BUFSIZ), BUFSIZ);
		// fclose($rfile);
		// fclose($lfile);
		
		//end downloading with chunking


		//download with wget
		//echo exec('wget '.$url.' -O '.$filename);
		
	    //end time count for download times
		$time_elapsed_secs = microtime(true) - $start;
		return $time_elapsed_secs;
   	}
 }
 ?>