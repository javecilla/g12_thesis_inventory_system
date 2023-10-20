<?php
	try {
	    $timezone = date_default_timezone_set('Asia/Manila');

	    //load private credentials from config.ini->then create dbconnection	    
	    $config = parse_ini_file('db.configuration.ini'); 
	    $connection = new mysqli($config['host'], $config['username'], $config['password'], $config['dbname']);

	    //check for connection errors
	    if($connection->connect_error) {
    		throw new Exception("Connection failed: " . $connection->connect_error);
  		}

  		//set the character set to UTF-8 to handle non-ASCII characters
  		$connection->set_charset('utf8');

	} catch(Exception $e) {
	    echo "An error occured: " . $e->getMessage();
	}
