<?php

	// remove all cookies

	$files = glob( __DIR__ .'/cookies/*'); // get all file names
	foreach($files as $file){ // iterate files
	  if(is_file($file)) {
	    unlink($file); // delete file
	  }
	}