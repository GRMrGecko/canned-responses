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
$result = databaseQuery("SELECT * FROM settings WHERE name='db_version'");
if ($result==NULL) {
	databaseQuery("CREATE TABLE {$_MGM['DBPrefix']}settings (name TEXT, value TEXT)");
	databaseQuery("INSERT INTO {$_MGM['DBPrefix']}settings (name, value) VALUES ('db_version',%s)", $_MGM['version']);
	databaseQuery("CREATE TABLE {$_MGM['DBPrefix']}responses (docid INTEGER PRIMARY KEY AUTOINCREMENT, key TEXT, message TEXT)");
	databaseQuery("CREATE TABLE {$_MGM['DBPrefix']}users (docid INTEGER PRIMARY KEY AUTOINCREMENT, email TEXT, password TEXT, time INTEGER, level INTEGER)");

	require_once("header.php");
	?>
	<form action="<?=generateURL()?>" method="POST">
	<input type="hidden" name="create_user" value="first" />
	<input type="text" placeholder="Email" name="email" /><br />
	<input type="password" placeholder="Password" name="password" /><br />
	<input type="submit" value="Create Admin" class="btn" />
	</form>
	<?php
	require_once("footer.php");
	exit();
} else {
	$info = databaseFetchAssoc($result);
}

if (isset($_REQUEST['create_user'])) {
	$email = (isset($_REQUEST['email']) ? trim($_REQUEST['email']) : "");
	$password = (isset($_REQUEST['password']) ? trim($_REQUEST['password']) : "");
	if ($_REQUEST['create_user']=="first") {
		$result = databaseQuery("SELECT COUNT(*) AS count FROM users");
		$count = databaseFetchAssoc($result);
		if ($count['count']==0 && !empty($email) && !empty($password)) {
			$salt = substr(sha1(rand()),0,12);
			$epassword = $salt.hashPassword($password,hex2bin($salt));
			databaseQuery("INSERT INTO users (email, password, time, level) VALUES (%s,%s,%d,1)", $email, $epassword, $_MGM['time']);
			setcookie("{$_MGM['CookiePrefix']}user_email", $email, $_MGM['time']+31536000, $_MGM['CookiePath'], $_MGM['CookieDomain']);
			setcookie("{$_MGM['CookiePrefix']}user_password", hash("sha512", $epassword.$_MGM['time']), $_MGM['time']+31536000, $_MGM['CookiePath'], $_MGM['CookieDomain']);
			header("location: ".generateURL());
			exit();
		}
	}
}
?>