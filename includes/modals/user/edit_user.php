<div id="edit_user" class="modal custom-modal fade" role="dialog">
	<div class="modal-dialog modal-dialog-centered modal-lg" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title">Edit User</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<form method="POST" enctype="multipart/form-data">
					<input type="hidden" name="user_id" id="edit_user_id">
					<div class="row">
						<div class="col-sm-6">
							<div class="form-group">
								<label>First Name <span class="text-danger">*</span></label>
								<input class="form-control" id="edit_firstname" name="firstname" type="text" required>
							</div>
						</div>
						<div class="col-sm-6">
							<div class="form-group">
								<label>Last Name</label>
								<input class="form-control" id="edit_lastname" name="lastname" type="text" required>
							</div>
						</div>
						<div class="col-sm-6">
							<div class="form-group">
								<label>Username <span class="text-danger">*</span></label>
								<input class="form-control" id="edit_username" name="username" type="text" required>
							</div>
						</div>
						<div class="col-sm-6">
							<div class="form-group">
								<label>Email <span class="text-danger">*</span></label>
								<input class="form-control" id="edit_email" name="email" type="email" required>
							</div>
						</div>
						<div class="col-sm-6">
							<div class="form-group">
								<label>Phone</label>
								<input class="form-control" id="edit_phone" name="phone" type="text">
							</div>
						</div>
						<div class="col-sm-6">
							<div class="form-group">
								<label>Address</label>
								<input class="form-control" id="edit_address" name="address" type="text">
							</div>
						</div>
					</div>
					<div class="submit-section">
						<button type="submit" name="edit_user" class="btn btn-primary submit-btn">Update</button>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>
