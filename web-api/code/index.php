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
if (isset($_MGM['user'])) {
if ($_MGM['user']['level']==1 || $_MGM['user']['level']==2) {
?>
<div class="modal fade" id="response_delete" tabindex="-1" role="dialog">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				Delete Response
			</div>
			<div class="modal-body">
				<div style="display: none;" id="response_delete_id"></div>
				Are you sure that you want to delete the response with key <span id="response_delete_key"></span>
			</div>
			<div class="modal-footer">
				<button class="btn btn-default" data-dismiss="modal">Cancel</button>
				<button class="btn btn-danger btn-ok"  data-dismiss="modal" id="response_delete_confirm">Delete</button>
			</div>
		</div>
	</div>
</div>
<div id="response_edit" class="modal fade" tabindex="-1" role="dialog">
	<div class="modal-dialog" style="width: 401px;">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h3>Edit Response</h3>
			</div>
			<div class="modal-body">
				<div style="display: none;" id="response_edit_id"></div>
				<input type="text" id="response_edit_key" placeholder="Key" /><br />
				<textarea id="response_edit_message" rows="4" cols="50"></textarea>
				<div style="display: none;" id="response_edit_load"></div>
			</div>
			<div class="modal-footer">
				<button class="btn" data-dismiss="modal">Cancel</button>
				<button class="btn btn-danger" data-dismiss="modal" id="response_edit_delete">Delete</button>
				<button class="btn btn-primary" data-dismiss="modal" id="response_edit_save">Save</button>
			</div>
		</div>
	</div>
</div>
<div id="response_add" class="modal fade" tabindex="-1" role="dialog">
	<div class="modal-dialog" style="width: 401px;">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h3>Create Response</h3>
			</div>
			<div class="modal-body">
				<input type="text" id="response_add_key" placeholder="Key" /><br />
				<textarea id="response_add_message" rows="4" cols="50"></textarea>
				<div style="display: none;" id="response_add_load"></div>
			</div>
			<div class="modal-footer">
				<button class="btn" data-dismiss="modal">Cancel</button>
				<button class="btn btn-primary" data-dismiss="modal" id="response_add_create">Create</button>
			</div>
		</div>
	</div>
</div>

<button class="btn btn-primary" id="add_response">Create Response</button><br /><br />
<?php
}
?>

<table class="table table-striped table-bordered table-hover" id="response_list">
	<thead>
		<tr><th>#</th><th>Key</th><th>Message</th></tr>
	</thead>
	<tbody>
		
	</tbody>
</table>
<?php
if ($_MGM['user']['level']==1 || $_MGM['user']['level']==2) {
?>
<script type="text/javascript">
function loadResponses() {
	$("#response_list tbody").load("<?=generateURL("api/response/list")?>/");
}
$(document).ready(function() {
	$("#response_list").on("click", "tbody tr", function() {
		$("#response_edit_id").text($(this).find(".id").text());
		$("#response_edit_key").val($(this).find(".key").text());
		var html = $(this).find(".message").html();
		if (html != undefined) {
			html = html.replace(/<br>/g, "\n");
			$("#response_edit_message").val($("<div/>").html(html).text());
		}
		$("#response_edit").modal();
	});
	$("#response_edit_delete").click(function() {
		$("#response_delete_id").text($("#response_edit_id").text());
		$("#response_delete_key").text($("#response_edit_key").val());
		$("#response_delete").modal();
	});
	$("#response_delete_confirm").click(function() {
		$("#response_edit_load").load("<?=generateURL("api/response/delete")?>/", {id: $("#response_delete_id").text()}, function(response, status, xhr) {
			loadResponses();
		});
	});
	$("#response_edit_save").click(function() {
		$("#response_edit_load").load("<?=generateURL("api/response/update")?>/", {id: $("#response_edit_id").text(), key: $("#response_edit_key").val(), message: $("#response_edit_message").val()}, function(response, status, xhr) {
			loadResponses();
		});
	});
	$("#add_response").click(function() {
		$("#response_add_key").val("");
		$("#response_add_message").val("");
		$("#response_add").modal();
	});
	$("#response_add_create").click(function() {
		$("#response_edit_load").load("<?=generateURL("api/response/create")?>/", {key: $("#response_add_key").val(), message: $("#response_add_message").val()}, function(response, status, xhr) {
			loadResponses();
		});
	});
	loadResponses();
});
</script>
<?php
}
} else {
	?>You must login to view this.<?php
}
require_once("footer.php");
exit();
?>