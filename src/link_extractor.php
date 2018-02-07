<?php

class LinkExtractor {

	public function extract_links($sourceURL) {
		$content=file_get_contents($sourceURL);
		$content = strip_tags($content,"<a>");
		$query = "ds";
		$output_links = array();

		$subString = preg_split("/<\/a>/",$content);
		foreach ( $subString as $val ){
			 if( strpos($val, "<a href=") !== FALSE ){
				 $val = preg_replace("/.*<a\s+href=\"/sm","",$val);
				 $val = preg_replace("/\".*/","",$val);

				 if(substr( $val, 0, strlen($query) ) === $query) {
				 	array_push($output_links, $sourceURL.$val);
				 }
			 }
		}

		return $output_links;
	}	
}
?>