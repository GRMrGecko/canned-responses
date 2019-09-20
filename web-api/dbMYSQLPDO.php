<?php
//
// Copyright (c) 2019, Mr. Gecko's Media (James Coleman)
// All rights reserved.
//
// Redistribution and use in source and binary forms, with or without modification,
//    are permitted provided that the following conditions are met:
//
// 1. Redistributions of source code must retain the above copyright notice, this
//    list of conditions and the following disclaimer.
//
// 2. Redistributions in binary form must reproduce the above copyright notice,
//    this list of conditions and the following disclaimer in the documentation
//    and/or other materials provided with the distribution.
//
// 3. Neither the name of the copyright holder nor the names of its contributors
//    may be used to endorse or promote products derived from this software without
//    specific prior written permission.
//
// THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND
//    ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED
//    WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED.
//    IN NO EVENT SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT,
//    INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING,
//    BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA,
//    OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY,
//    WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE)
//    ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
//    POSSIBILITY OF SUCH DAMAGE.
//
function connectToDatabase() {
	global $_MGM;
	if (isset($_MGM['DBConnection'])) closeDatabase();
	$_MGM['DBConnection'] = NULL;
	$options = array();
	if ($_MGM['DBPersistent'])
		$options = array(PDO::ATTR_PERSISTENT => true);
	try {
		$_MGM['DBConnection'] = new PDO("mysql:host={$_MGM['DBHost']};dbname={$_MGM['DBName']};charset=utf8", $_MGM['DBUser'], $_MGM['DBPassword'], $options);
		$_MGM['DBConnection']->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	} catch (Exception $e) {
		mail("Server Admin <{$_MGM['adminEmail']}>", "MySQL Error", "URL: ".$_SERVER['SERVER_NAME'].$_MGM['installPath'].$_MGM['fullPath']."\n\nError ".$e->getMessage().": ".mysql_error());
		//echo $e->getMessage()."<br />\n";
		error("Failed to connect to database");
	}
	if ($_MGM['DBConnection']==NULL) error("Database Connection Failed");
}
function closeDatabase() {
	global $_MGM;
	if (isset($_MGM['DBConnection'])) {
		$_MGM['DBConnection'] = NULL;
	}
}
function escapeString($theString) {
	global $_MGM;
	return $_MGM['DBConnection']->quote($theString);
}
function quoteObject($theObject) {
	global $_MGM;
	if (is_null($theObject)) {
		return "''";
	} else if (is_string($theObject)) {
		return escapeString($theObject);
	} else if (is_float($theObject) || is_integer($theObject)) {
		return $theObject;
	} else if (is_bool($theObject)) {
		return ($theObject ? 1 : 0);
	}
	return "''";
}
function databaseQuery($format) {
	global $_MGM;
	$result = NULL;
	try {
		if (isset($_MGM['DBConnection'])) {
			$args = func_get_args();
			array_shift($args);
			$args = array_map("quoteObject", $args);
			$query = vsprintf($format, $args);
			//echo $query."\n";
			$result = $_MGM['DBConnection']->query($query);
		}
		//if ($result==NULL) error("Failed to run query on database");
	} catch (Exception $e) {
		mail("Server Admin <{$_MGM['adminEmail']}>", "MySQL Error", "URL: ".$_SERVER['SERVER_NAME'].$_MGM['installPath'].$_MGM['fullPath']."\n\nError ".$e->getMessage().": ".mysql_error());
		//echo $e->getMessage()."<br />\n";
		//error("Failed to run query on database");
	}
	return $result;
}
function databaseRowCount($theResult) {
	global $_MGM;
	if ($theResult==NULL)
		return 0;
	return $theResult->rowCount();
}
function databaseFieldCount($theResult) {
	global $_MGM;
	if ($theResult==NULL)
		return 0;
	return $theResult->columnCount();
}
function databaseLastID() {
	global $_MGM;
	$result = 0;
	if (isset($_MGM['DBConnection'])) {
		$result = $_MGM['DBConnection']->lastInsertId();
	}
	return $result;
}
function databaseFetch($theResult) {
	global $_MGM;
	return $theResult->fetch();
}
function databaseFetchNum($theResult) {
	global $_MGM;
	return $theResult->fetch(PDO::FETCH_NUM);
}
function databaseFetchAssoc($theResult) {
	global $_MGM;
	return $theResult->fetch(PDO::FETCH_ASSOC);
}
function databaseResultSeek($theResult, $theLocation) {
	global $_MGM;
	return false;
}
function databaseFreeResult($theResult) {
	global $_MGM;
	$theResult = NULL;
}
?>