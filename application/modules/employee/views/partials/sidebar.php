<div class="toggled m-hide" id="sidebar">
	<div class="logo-con">
		<button class="sidebar-toggle" title="Hide sidebar">
			<i class="fas fa-angle-left"></i>
		</button>
		<img class="logo" src="<?= base_url("assets/img/nlrc-logo-white.png") ?>">
		<a class="profile-con" user-id="<?= $user_id ?>" user-type="n/a" href="<?= base_url('employee') ?>/view_profile?id=<?= $user_id ?>">
			<img class="w-100" src="
				<?php
					if($profile_pic):
						echo base_url("assets_module/user_profile/{$profile_pic}");
					else:
						echo base_url("assets/img/".($gender=='Male' ? "male": "female")."-default.jpg");
					endif;
				?>
			">
		</a>
		<h5 class="name" title="<?= $name ?>"><?= $name ?></h5>
		<b class="user-type">(<?= ucwords($user_type) ?>)</b>
	</div>

	<button class="drop-button" target="#dtr-collapse">
		<div class="w-100">
			<i class="fa-solid fa-clock left"></i>Daily Time Record
		</div>
		<i class="fas fa-angle-up right"></i>
	</button>
	<div class="collapsible" id="dtr-collapse">
		<?php 
			$read_grade = $this->salary_grade_model->get_row($salary_grade);
			$salary = null;

			$active = $this->dtr_model->count_by_where(['user_id'=>$user_id,'time_out'=>NULL]);

			if($active > 0){
				$html = 'class="disabled create-new-dtr" title="You still have an active dtr"';
				$alert = '<span class="ongoing-dtr-message">You still have an active dtr</span>';
			}else{
				$html = 'class="create-new-dtr"';
				$alert = '';
			}

			if(!empty($current_leave[0])){
				$html = 'class="disabled create-new-dtr" title="You are currently on '.ucwords($current_leave[0]['leave_type']).' Leave"';
				$alert = '<span class="ongoing-dtr-message"> You are currently on '.ucwords($current_leave[0]['leave_type']).' Leave</span>';
			}
		?>
		<!-- <button id="dtr-today" <?= $html ?> user-id="<?= $user_id ?>" per-hour="<?= $salary ?>" >
			<i class="fas fa-calendar"></i>Create <br><?= $alert ?>
		</button> -->
		<button id="my-dtr"><a href="<?= base_url("employee"); ?>"><i class="fas fa-list"></i>My DTR</a></button>
		<button id="my-leaves"><a href="<?= base_url("employee/leaves"); ?>"><i class="fa-solid fa-person-walking-luggage"></i>My Leaves</a></button>
		<button id="my-ctr"><a href="<?= base_url("employee/my_change_time_requests"); ?>"><i class="fa-solid fa-clock"></i>My Change Time Requests</a></button>
		<button><a href="<?= base_url("employee/my_undertime"); ?>"><i class="fas fa-hourglass-end"></i>Undertime Records</a></button>
		<button><a href="<?= base_url("employee/my_overtime"); ?>"><i class="fas fa-hourglass-start"></i>Overtime Records</a></button>
	</div>

	<?php if($user_type == 'supervisor' || $user_type == 'Secretary' || $user_type == 'IT Administrator' || $user_type == 'Accounting'): ?>
		<button class="drop-button" target="#employee-collapse">
			<div class="w-100">
				<i class="fa-solid fa-users left"></i> Employees
			</div>
			<i class="fas fa-angle-up right"></i>
		</button>
		<div class="collapsible" id="employee-collapse">
			<button><a href="<?= base_url("employee/active"); ?>"><i class="fa-solid fa-calendar-check"></i>Active DTR</a></button>
			<button><a href="<?= base_url("employee/employee_list"); ?>"><i class="fa-solid fa-user"></i>Employees</a></button>
		</div>

		<?php if($user_type != 'Accounting'): ?>
			<button class="drop-button" target="#request-collapse">
				<div class="w-100">
					<i class="fa-solid fa-bell left"></i> Requests
				</div>
				<i class="fas fa-angle-up right"></i>
			</button>
			<div class="collapsible" id="request-collapse">

				<?php 
					$lr = $this->request_leave_model->count_by_where(['status' => 'pending']);
					$lr_retract = $this->request_leave_model->count_by_where(['status' => 'retraction']);
					$otr = $this->overtime_logs_model->count_by_where(['status' => 'pending', 'time_out IS NOT NULL' => null]);
					$ctr = $this->request_change_time_model->count_by_where(['status' => 'pending']);
				?>

				<button>
					<a href="<?= base_url("employee/leave_requests"); ?>">
						<i class="fa-solid fa-folder-open"></i>Leave Requests 
						<small class="alert alert-danger rounded-pill px-2 py-0 ms-1"><?= $lr + $lr_retract ?></small>
					</a>
				</button>
				<button>
					<a href="<?= base_url("employee/overtime_requests"); ?>">
						<i class="fa-solid fa-user-clock"></i>Overtime Requests 
						<small class="alert alert-danger rounded-pill px-2 py-0 ms-1"><?= $otr ?></small>
					</a>
				</button>
				<button>
					<a href="<?= base_url("employee/change_time_requests"); ?>">
						<i class="fa-solid fa-clock-rotate-left"></i>Change Time Requests 
						<small class="alert alert-danger rounded-pill px-2 py-0 ms-1"><?= $ctr ?></small>
					</a>
				</button>
			</div>
		<?php endif ?>
	<?php endif ?>
</div>

<?= $this->load->view('modal/request-ot-cru-modal') ?>
<?= $this->load->view('modal/request-leave-modal') ?>