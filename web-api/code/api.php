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

if (isset($_MGM['user']) && $_MGM['user']['level']==1 && $_MGM['path'][1]=="users") {
	if ($_MGM['path'][2]=="list") {
		$results = databaseQuery("SELECT * FROM {$_MGM['DBPrefix']}users");
		while ($result = databaseFetchAssoc($results)) {
			$level = "Viewer";
			if ($result['level']==0)
				$level = "Disabled";
			if ($result['level']==1)
				$level = "Administrator";
			if ($result['level']==2)
				$level = "Editor";
			?><tr><td class="id"><?=htmlspecialchars($result['docid'], ENT_COMPAT | ENT_HTML401, 'UTF-8', true)?></td><td class="email"><?=htmlspecialchars($result['email'], ENT_COMPAT | ENT_HTML401, 'UTF-8', true)?></td><td class="level" value="<?=htmlspecialchars($result['level'], ENT_COMPAT | ENT_HTML401, 'UTF-8', true)?>"><?=$level?></td></tr><?php
		}
	}
	if ($_MGM['path'][2]=="update") {
		$id = (isset($_REQUEST['id']) ? trim($_REQUEST['id']) : "");
		$email = (isset($_REQUEST['email']) ? trim($_REQUEST['email']) : "");
		$password = (isset($_REQUEST['password']) ? trim($_REQUEST['password']) : "");
		$level = (isset($_REQUEST['level']) ? trim($_REQUEST['level']) : "");
		$results = databaseQuery("SELECT * FROM {$_MGM['DBPrefix']}users WHERE docid=%s", $id);
		$result = databaseFetchAssoc($results);
		if ($result!=NULL) {
			if (empty($email))
				$email = $result['email'];
			$epassword = $result['password'];
			if (!empty($password)) {
				$salt = substr(sha1(rand()),0,12);
				$epassword = $salt.hashPassword($password,hex2bin($salt));
			}
			if ($level=="")
				$level = $result['level'];
			databaseQuery("UPDATE {$_MGM['DBPrefix']}users SET email=%s,password=%s,level=%s WHERE docid=%s", $email, $epassword, $level, $id);
		}
	}
	if ($_MGM['path'][2]=="create") {
		$email = (isset($_REQUEST['email']) ? trim($_REQUEST['email']) : "");
		$password = (isset($_REQUEST['password']) ? trim($_REQUEST['password']) : "");
		$level = (isset($_REQUEST['level']) ? trim($_REQUEST['level']) : "");
		if (!empty($email) && !empty($level)) {
			$salt = substr(sha1(rand()),0,12);
			$epassword = $salt.hashPassword($password,hex2bin($salt));
			databaseQuery("INSERT INTO {$_MGM['DBPrefix']}users (email, password, time, level) VALUES (%s,%s,%d,%s)", $email, $epassword, $_MGM['time'], $level);
		}
	}
	exit();
}
if ($_MGM['path'][1]=="response") {
	if (isset($_MGM['user'])) {
		if ($_MGM['path'][2]=="list") {
			$results = databaseQuery("SELECT * FROM {$_MGM['DBPrefix']}responses");
			while ($result = databaseFetchAssoc($results)) {
				?><tr><td class="id"><?=htmlspecialchars($result['docid'], ENT_COMPAT | ENT_HTML401, 'UTF-8', true)?></td><td class="key"><?=htmlspecialchars($result['key'], ENT_COMPAT | ENT_HTML401, 'UTF-8', true)?></td><td class="message"><?=str_replace("\n", "<br />", htmlspecialchars($result['message'], ENT_COMPAT | ENT_HTML401, 'UTF-8', true))?></td></tr><?php
			}
		}
		if ($_MGM['user']['level']==1 || $_MGM['user']['level']==2) {
			if ($_MGM['path'][2]=="update") {
				$id = (isset($_REQUEST['id']) ? trim($_REQUEST['id']) : "");
				$key = (isset($_REQUEST['key']) ? trim($_REQUEST['key']) : "");
				$message = (isset($_REQUEST['message']) ? trim($_REQUEST['message']) : "");
				$results = databaseQuery("SELECT * FROM {$_MGM['DBPrefix']}responses WHERE docid=%s", $id);
				$result = databaseFetchAssoc($results);
				if ($result!=NULL) {
					databaseQuery("UPDATE {$_MGM['DBPrefix']}responses SET key=%s,message=%s WHERE docid=%s", $key, $message, $id);
				}
			}
			if ($_MGM['path'][2]=="delete") {
				$id = (isset($_REQUEST['id']) ? trim($_REQUEST['id']) : "");
				databaseQuery("DELETE FROM {$_MGM['DBPrefix']}responses WHERE docid=%s", $id);
			}
			if ($_MGM['path'][2]=="create") {
				$key = (isset($_REQUEST['key']) ? trim($_REQUEST['key']) : "");
				$message = (isset($_REQUEST['message']) ? trim($_REQUEST['message']) : "");
				if (!empty($key) && !empty($message)) {
					databaseQuery("INSERT INTO {$_MGM['DBPrefix']}responses (key, message) VALUES (%s,%s)", $key, $message);
				}
			}
		}
	}
	if (count($_MGM['PublicAPIWhiteListedIPs'])==0 || in_array($_MGM['ip'], $_MGM['PublicAPIWhiteListedIPs'])) {
		if ($_MGM['path'][2]=="get") {
			$results = databaseQuery("SELECT * FROM {$_MGM['DBPrefix']}responses WHERE key=%s OR docid=%s", $_REQUEST['key'], $_REQUEST['key']);
			$response = array("type" => "success", "results" => array());
			while ($result = databaseFetchAssoc($results)) {
				$response['results'][] = $result;
			}
			header("content-type: application/json");
			echo json_encode($response);
		}
		if ($_MGM['path'][2]=="find") {
			$results = databaseQuery("SELECT * FROM {$_MGM['DBPrefix']}responses WHERE key LIKE %s OR docid=%s", "%".$_REQUEST['key']."%", $_REQUEST['key']);
			$response = array("type" => "success", "results" => array());
			while ($result = databaseFetchAssoc($results)) {
				$response['results'][] = $result;
			}
			header("content-type: application/json");
			echo json_encode($response);
		}
		if ($_MGM['path'][2]=="get-all") {
			$results = databaseQuery("SELECT * FROM {$_MGM['DBPrefix']}responses");
			$response = array("type" => "success", "results" => array());
			while ($result = databaseFetchAssoc($results)) {
				$response['results'][] = $result;
			}
			header("content-type: application/json");
			echo json_encode($response);
		}
	}
	exit();
}
$response = array("type" => "failure", "error" => "Not a valid request.");
header("content-type: application/json");
echo json_encode($response);
exit();