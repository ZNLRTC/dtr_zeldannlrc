<?php
/*
 * Page Name: Reset Password
 * Author: Jushua FF
 * Date: 12.06.2022
 */
?>

<div class="container-fluid rp-pg">
	<div class="container">
		<div class="row main-row">
			<div class="main-col col-xl-4 col-lg-5 col-md-7">
				<div class="d-flex justify-content-center w-100">
					<img class="logo" src="<?= base_url("assets/img/nlrc-logo-2.png") ?>">
				</div>
				<form id="rp-form" user-id="<?= $id ?>">
					<div class="input-con">
						<label>New Password:</label>
						<div>
							<i class="fas fa-key icon-left"></i>
							<input type="password" name="new-password">
							<i class="fas fa-eye icon-right" title="Toggle Password"></i>
						</div>
					</div>
					<div class="input-con">
						<label>Confirm Password:</label>
						<div>
							<i class="fas fa-key icon-left"></i>
							<input type="password" name="confirm-password">
							<i class="fas fa-eye icon-right" title="Toggle Password"></i>
						</div>
					</div>
					<button class="btn btn-lblue w-100">Submit</button>
				</form>
	  		</div>
		</div>
  	</div>
</div>