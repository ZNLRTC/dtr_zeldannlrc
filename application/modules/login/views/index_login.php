<?php

/*

 * Page Name: Login

 * Author: Jushua FF

 * Date: 09.11.2022

 */

?>



<div class="container-fluid login-pg">

	<div class="container">

		<div class="row main-row">

			<div class="main-col col-xl-4 col-lg-5 col-md-7">

				<div class="logo-con">

					<img class="logo" src="<?= base_url("assets/img/nlrc-logo-2.png") ?>">

				</div>

				<h4 class="w-100 text-center p-0">Login</h4>

				<form id="login-form">

					<div class="input-con">

						<label>Username:</label>

						<div>

							<i class="fas fa-user icon-left"></i>

							<input type="text" name="username">

						</div>

					</div>

					<div class="input-con">

						<label>Password:</label>

						<div>

							<i class="fas fa-key icon-left"></i>

							<input type="password" name="password">

							<i class="fas fa-eye icon-right" title="Toggle Password"></i>

						</div>

					</div>

					<button class="btn btn-lblue w-100">Log-in</button>

					<a href="<?= base_url("forgot_password") ?>" class="lblue w-100 d-block text-center">Forgot Password</a>
					<div class="d-flex align-items-center justify-content-center pointer mt-3"><a href="<?= base_url() ?>"><i class="fa-solid fa-arrow-left"></i> &nbsp; Home</a></div>

					<!-- 

						Upcoming Feature

						<p class="text-center">don't have an account? Click <a href="#" class="">here</a></p> 

					-->

				</form>

	  		</div>

		</div>

  	</div>

</div>