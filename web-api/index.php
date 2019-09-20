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

error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING & ~E_STRICT);

function error($error) {
	echo $error;
	exit();
}

$_MGM = array();
$_MGM['version'] = "1";
$_MGM['title'] = "Canned Response";
$_MGM['author'] = "James Coleman";
$_MGM['DBType'] = "SQLITE"; // MYSQL, POSTGRESQL, SQLITE.
$_MGM['DBPersistent'] = false;
$_MGM['DBHost'] = "localhost";
$_MGM['DBUser'] = "";
$_MGM['DBPassword'] = "";
$_MGM['DBName'] = "databases/main.db"; // File location for SQLite.
$_MGM['DBPort'] = 0; // 3306 = MySQL Default, 5432 = PostgreSQL Default.
$_MGM['DBPrefix'] = "";
$_MGM['adminEmail'] = "name@example.com";
$_MGM['PublicAPIWhiteListedIPs'] = array(); // IP Addresses to whitelist access to public API to list canned responses.
require_once("db{$_MGM['DBType']}.php");

putenv("TZ=US/Central");
$_MGM['time'] = time();
$_MGM['domain'] = $_SERVER['HTTP_HOST'];
$_MGM['domainname'] = str_replace("www.", "", $_MGM['domain']);
$_MGM['port'] = $_SERVER['SERVER_PORT'];
$_MGM['ssl'] = ($_SERVER['HTTPS']=="on");

if ($_SERVER['REMOTE_ADDR'])
	$_MGM['ip'] = $_SERVER['REMOTE_ADDR'];

$_MGM['installPath'] = substr($_SERVER['SCRIPT_NAME'], 0, strlen($_SERVER['SCRIPT_NAME'])-strlen(end(explode("/", $_SERVER['SCRIPT_NAME']))));
if (!isset($_GET['d'])) {
	$tmp = explode("?", substr($_SERVER['REQUEST_URI'], strlen($_MGM['installPath'])));
	$tmp = urldecode($tmp[0]);
	if (substr($tmp, 0, 9)=="index.php")
		$tmp = substr($tmp, 10, strlen($tmp)-10);
	$_MGM['fullPath'] = $tmp;
} else {
	$tmp = $_GET['d'];
	if (substr($tmp, 0, 1)=="/")
		$tmp = substr($tmp, 1, strlen($tmp)-1);
	$_MGM['fullPath'] = $tmp;
}
if (strlen($_MGM['fullPath'])>255) error("The URI you entered is to large");
$_MGM['path'] = explode("/", strtolower($_MGM['fullPath']));

$_MGM['CookiePrefix'] = "";
$_MGM['CookiePath'] = $_MGM['installPath'];
$_MGM['CookieDomain'] = ".".$_MGM['domainname'];

function generateURL($path="") {
	global $_MGM;
	return "http".($_MGM['ssl'] ? "s" : "")."://".$_MGM['domain'].(((!$_MGM['ssl'] && $_MGM['port']==80) || ($_MGM['ssl'] && $_MGM['port']==443)) ? "" : ":{$_MGM['port']}").$_MGM['installPath'].$path;
}

function hashPassword($password, $salt) {
	$hashed = hash("sha512", $salt.$password);
	for ($i=0; $i<10000; $i++) {
		$hashed = hash("sha512", $salt.hex2bin($hashed));
	}
	return $hashed;
}

connectToDatabase();

if (file_exists("code/setup.php")) {
	require("code/setup.php");
}

if (isset($_COOKIE["{$_MGM['CookiePrefix']}user_email"])) {
	$result = databaseQuery("SELECT * FROM {$_MGM['DBPrefix']}users WHERE email=%s AND level!=0", $_COOKIE["{$_MGM['CookiePrefix']}user_email"]);
	$user = databaseFetchAssoc($result);
	if ($user!=NULL && hash("sha512", $user['password'].$user['time'])==$_COOKIE["{$_MGM['CookiePrefix']}user_password"]) {
		$_MGM['user'] = $user;
	}
}

if (!isset($_MGM['user']) && $_MGM['path'][0]=="login") {
	require("code/login.php");
}
if (isset($_MGM['user']) && $_MGM['path'][0]=="logout") {
	require("code/logout.php");
}

if ($_MGM['path'][0]=="api") {
	require("code/api.php");
}

if (isset($_MGM['user']) && $_MGM['user']['level']==1 && $_MGM['path'][0]=="users") {
	require("code/users.php");
}

if ($_MGM['path'][0]!="") {
	require("code/404.php");
}

require("code/index.php");
?>