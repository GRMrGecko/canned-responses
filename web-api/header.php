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
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<title><?=$_MGM['title']?></title>
	<meta name="viewport" content="width=device-width, initial-scale=1.0" />
	<meta name="author" content="<?=$_MGM['author']?>" />
	
	<link href="<?=$_MGM['installPath']?>css/bootstrap-3.1.1.min.css" rel="stylesheet" />
	<!--<link href="<?=$_MGM['installPath']?>css/bootstrap-theme.min.css" rel="stylesheet">-->
	<style>
	body {
		padding-top: 60px;
	}
	@media (min-width: 768px) {
		body {
			padding-top: 130px;
		}
	}
	@media (min-width: 992px) {
		body {
			padding-top: 60px;
		}
	}
	@media (min-width: 1200px) {
		body {
			padding-top: 60px;
		}
	}
	</style>
	<script type="text/javascript" src="<?=$_MGM['installPath']?>js/jquery.min.js"></script>
	<script type="text/javascript" src="<?=$_MGM['installPath']?>js/bootstrap-3.1.1.min.js"></script>
	<script type="text/javascript" src="<?=$_MGM['installPath']?>js/date.js"></script>
</head>

<body>
	<div class="navbar navbar-default navbar-fixed-top" role="navigation">
		<div class="container">
			<div class="navbar-header">
				<button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
					<span class="sr-only">Toggle navigation</span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
				</button>
				<a class="navbar-brand" href="<?=$_MGM['installPath']?>"><?=$_MGM['title']?></a>
			</div>
			<div class="navbar-collapse collapse">
				<ul class="nav navbar-nav">
					<?php
					if (isset($_MGM['user'])) {	
					?>
						<?php
						if ($_MGM['user']['level']==1) {
						?>
							<li<?=($_MGM['path'][0]=="users" ? " class=\"active\"" : "")?>><a href="<?=$_MGM['installPath']?>users/">User Management</a></li>
						<?php
						}
						?>
					<?php
					}
					?>
					<?php
					if (isset($_MGM['user'])) {
					?>
						<li><a href="<?=$_MGM['installPath']?>logout">Logout</a></li>
					<?php
					} else {
					?>
						<li><a href="<?=$_MGM['installPath']?>login">Login</a></li>
					<?php
					}
					?>
				</ul>
			</div><!--/.nav-collapse -->
		</div>
	</div>
	
	<div class="container">