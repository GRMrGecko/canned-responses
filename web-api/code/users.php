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
require_once("header.php");
?>
<div id="user_edit" class="modal fade" tabindex="-1" role="dialog">
	<div class="modal-dialog" style="width: 260px;">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h3>Edit User</h3>
			</div>
			<div class="modal-body">
				<div style="display: none;" id="user_edit_id"></div>
				<input type="text" id="user_edit_email" placeholder="Email" /><br />
				<input type="password" id="user_edit_password" placeholder="Password" /><br />
				<select id="user_edit_level">
					<option value="3">Viewer</option>
					<option value="2">Editor</option>
					<option value="1">Administrator</option>
					<option value="0">Disabled</option>
				</select>
				<div style="display: none;" id="user_edit_load"></div>
			</div>
			<div class="modal-footer">
				<button class="btn" data-dismiss="modal">Cancel</button>
				<button class="btn btn-primary" data-dismiss="modal" id="user_edit_save">Save</button>
			</div>
		</div>
	</div>
</div>
<div id="user_add" class="modal fade" tabindex="-1" role="dialog">
	<div class="modal-dialog" style="width: 260px;">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h3>Create User</h3>
			</div>
			<div class="modal-body">
				<input type="text" id="user_add_email" placeholder="Email" /><br />
				<input type="password" id="user_add_password" placeholder="Password" /><br />
				<select id="user_add_level">
					<option value="3">Viewer</option>
					<option value="2">Editor</option>
					<option value="1">Administrator</option>
					<option value="0">Disabled</option>
				</select>
				<div style="display: none;" id="user_add_load"></div>
			</div>
			<div class="modal-footer">
				<button class="btn" data-dismiss="modal">Cancel</button>
				<button class="btn btn-primary" data-dismiss="modal" id="user_add_create">Create</button>
			</div>
		</div>
	</div>
</div>

<button class="btn btn-primary" id="add_user">Create User</button><br /><br />
<table class="table table-striped table-bordered table-hover" id="users_list">
	<thead>
		<tr><th>#</th><th>Email</th><th>Level</th></tr>
	</thead>
	<tbody>
		
	</tbody>
</table>
<script type="text/javascript">
function loadUsers() {
	$("#users_list tbody").load("<?=generateURL("api/users/list")?>/");
}
$(document).ready(function() {
	$("#users_list").on("click", "tbody tr", function() {
		$("#user_edit_id").text($(this).find(".id").text());
		$("#user_edit_email").val($(this).find(".email").text());
		$("#user_edit_level").val($(this).find(".level").attr("value"));
		$("#user_edit").modal();
	});
	$("#user_edit_save").click(function() {
		$("#user_edit_load").load("<?=generateURL("api/users/update")?>/", {id: $("#user_edit_id").text(), email: $("#user_edit_email").val(), password: $("#user_edit_password").val(), level: $("#user_edit_level").val()}, function(response, status, xhr) {
			loadUsers();
		});
	});
	$("#add_user").click(function() {
		$("#user_add_email").val("");
		$("#user_add_password").val("")
		$("#user_add").modal();
	});
	$("#user_add_create").click(function() {
		$("#user_edit_load").load("<?=generateURL("api/users/create")?>/", {email: $("#user_add_email").val(), password: $("#user_add_password").val(), level: $("#user_add_level").val()}, function(response, status, xhr) {
			loadUsers();
		});
	});
	loadUsers();
});
</script>
<?php
require_once("footer.php");
exit();
?>