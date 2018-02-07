<?php

require_once("link_extractor.php");
require_once("curl_downloader.php");
require_once("logger.php");

$ini = parse_ini_file('../config.ini');

$log = new Logger();
 
// set path and name of log file (optional)
$log->lfile($ini['LOGGER']);

//$log_file = "../log/error.log";
$sourceURL = $ini['SRCURL'];

//get the links from the url
$link_extractor = new LinkExtractor();
echo ("EXTRACTING FILE LINKS TO DOWNLOAD\n");
$links = $link_extractor->extract_links($sourceURL);
$message = "Links extracted ".print_r($links, TRUE);
//error_log($message, 3, $log_file);
$log->lwrite($message);


echo ("EXTRACTED LINKS\n");

//iterate over each link to download the file
$curl_downloader = new CurlDownloader();
echo ("STARTING FILE DOWNLOAD, THIS MIGHT TAKE A WHILE\n");

$temp_links = array();
array_push($temp_links, $links[8]);
array_push($temp_links, $links[10]);
$i = 1;
$count = count($temp_links);
foreach($temp_links as $link) {
	$time = time();
	$filename = $ini['DOWNLOADS_FOLDER'].basename($link).$time;

	//error_log("[{$date}] DOWNLOADING FILE - ".$link.PHP_EOL, 3, $log_file);
	$message = "DOWNLOADING FILE - ".$link;
	$log->lwrite($message);
	$time_elapsed = $curl_downloader->download($link, $filename);
	echo ("DOWNLOADED FILE $i OF $count\n");
	//error_log("DOWNLOAD COMPLETE in ".$time_elapsed." SECS".PHP_EOL, 3, $log_file);
	$message = "DOWNLOAD COMPLETE in ".$time_elapsed." SECS";
	$log->lwrite($message);

	//convert the downloaded bin file to csv file
	$outfilename = str_replace(".", "", basename($link));
    $outfile = $ini['CSV_FOLDER'].$outfilename.$time.".csv";

    //run through degrib to generate csv file
    echo exec($ini['DEGRIB']." ".$filename." -out ".$outfile." -C -msg 1 -Csv");
    echo ("CONVERTED BINARY FILE TO CSV ".$outfile."\n");
  //  echo exec('$ini['DEGRIB']." ".$filename." -out ".$outfile." -C -msg 1 -Csv"');

    //run through csv to json utility to create json file
    $jsonoutfile = $ini['JSON_FOLDER'].$outfilename.$time.".json";
    echo exec($ini['CSVTOJSON']." ".$outfile.' > '.$jsonoutfile);
    echo ("CONVERTED CSV FILE TO JSON ".$jsonoutfile."\n");

    //delete csv file to free up space on the server
    unlink($outfile);
    $message = "DELETED CSV FILE ".$outfile;
	$log->lwrite($message);

	//delete bin file to free up space
	unlink($filename);
	$message = "DELETED BIN FILE ".$filename;
	$log->lwrite($message);

    $i++;
}
$log->lclose();
echo ("EXITING PROGRAM\n");
die();
?>
