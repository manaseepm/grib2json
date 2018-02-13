<?php

require_once("link_extractor.php");
require_once("logger.php");
require_once("customcsvreader.php");

error_reporting(E_ERROR | E_PARSE);

$start = microtime(true);
$ini = parse_ini_file('../config.ini');

$log = new Logger();
 
// set path and name of log file (optional)
$log->lfile($ini['LOGGER']);

//$log_file = "../log/error.log";
$sourceURL = $ini['SRCURL'];

//get the links from the url
$link_extractor = new LinkExtractor();
$links = $link_extractor->extract_links($sourceURL);
$message = "Links extracted ".print_r($links, TRUE);
$log->lwrite($message);

$temp_links = array();

//add 4 files as a sample test
array_push($temp_links, $links[1]);


//use curl multiple connections for faster download
$mh = curl_multi_init();
$i=0;
foreach ($temp_links as $link) {
	$time = time();
	$g = $ini['DOWNLOADS_FOLDER'].basename($link).$time;

    if(!is_file($g)){
        $conn[$i]=curl_init($link);
        $fp[$i]=fopen ($g, "w");
        curl_setopt ($conn[$i], CURLOPT_FILE, $fp[$i]);
        curl_setopt ($conn[$i], CURLOPT_HEADER ,0);
        curl_setopt($conn[$i],CURLOPT_CONNECTTIMEOUT,60);
        curl_multi_add_handle ($mh,$conn[$i]);
    }
    $i++;
}

do {
    $n=curl_multi_exec($mh,$active);
}
while ($active);

$cnt=0;
foreach ($temp_links as $link) {
    curl_multi_remove_handle($mh,$conn[$cnt]);
    curl_close($conn[$cnt]);
    fclose ($fp[$cnt]);
    $cnt++;
}

//$log->lclose();
curl_multi_close($mh);
$time_elapsed_secs = microtime(true) - $start;
$message = 'Downloaded '.$i.' files in '.$time_elapsed_secs.' secs';
$log->lwrite($message);

//after downloading all files run them through convertors
$files = array();

$csvreader = new CustomCsvReader();

foreach (scandir($ini['DOWNLOADS_FOLDER']) as $file) {
    if ('.' === $file) continue;
    if ('..' === $file) continue;
	//convert the downloaded bin file to csv file
	$outfilename = str_replace(".", "", $file);
    $outfile = $ini['CSV_FOLDER'].$outfilename.".csv";
    //convert the binary to csv through degrib
    $start = microtime(true);
    echo exec($ini['DEGRIB']." ".$ini['DOWNLOADS_FOLDER'].$file." -out ".$outfile." -C -msg 1 -Csv");
	$time_elapsed_secs = microtime(true) - $start;
	$message = 'Executed drgrib for '.$file.' in '.$time_elapsed_secs.' seconds';
	$log->lwrite($message);
    //convert the csv to json
    $start = microtime(true);
    $csvreader->convert_file($outfile, $ini); 
    $time_elapsed_secs = microtime(true) - $start;
	$message = 'Converted csv to json for '.$outfile.' in '.$time_elapsed_secs.' seconds'; 
    //delete binary file
    unlink($ini['DOWNLOADS_FOLDER'].$file);
}

?>
