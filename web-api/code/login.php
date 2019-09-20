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
$error = "";
if (isset($_REQUEST['login'])) {
	$email = (isset($_REQUEST['email']) ? trim($_REQUEST['email']) : "");
	$password = (isset($_REQUEST['password']) ? trim($_REQUEST['password']) : "");
	
	$result = databaseQuery("SELECT * FROM users WHERE email=%s AND level!=0", $email);
	$user = databaseFetchAssoc($result);
	if ($user==NULL) {
		$error = "Invalid login credentials.";
	} else {
		$salt = substr($user['password'], 0, 12);
		$epassword = $salt.hashPassword($password,hex2bin($salt));
		if ($epassword!=$user['password']) {
			$error = "Invalid login credentials.";
		} else {
			databaseQuery("UPDATE users SET time=%d WHERE email=%s", $_MGM['time'], $email);
			setcookie("{$_MGM['CookiePrefix']}user_email", $email, $_MGM['time']+31536000, $_MGM['CookiePath'], $_MGM['CookieDomain']);
			setcookie("{$_MGM['CookiePrefix']}user_password", hash("sha512", $epassword.$_MGM['time']), $_MGM['time']+31536000, $_MGM['CookiePath'], $_MGM['CookieDomain']);
			header("location: ".generateURL());
			exit();
		}
	}
}
require_once("header.php");
if (!empty($error)) {
	?><div style="color: #ff0000; font-weight: bold;"><?=$error?></div><?php
}
?>
<form action="<?=generateURL("login")?>" method="POST">
<input type="hidden" name="login" value="true" />
<input type="text" placeholder="Email" name="email" /><br />
<input type="password" placeholder="Password" name="password" /><br />
<input type="submit" value="Login" class="btn" />
</form>
<?php
require_once("footer.php");
exit();
?>