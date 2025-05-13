<?php 
	$account_session = $this->session->userdata('account_session');
	if($account_session['user_type'] == 1){
		$branch = 'Baguio City';
	}elseif($account_session['user_type'] == 5){
		$branch = 'Quezon City';
	}

	$dtr_request_count = $this->request_dtr_update_model->count_all_pending_dtr($branch);
?>
<div class="toggled" id="sidebar">
	<div class="logo-con">
		<button class="sidebar-toggle" title="Hide sidebar">
			<i class="fas fa-angle-left"></i>
		</button>
		<img class="logo" src="<?= base_url("assets/img/nlrc-logo-white.png") ?>">
		<div class="profile-con edit-profile own-profile" user-id="<?= $id ?>" data-toggle="modal" data-target="#employee-cru-modal" user-type="n/a">
			<img class="w-100" src="
				<?php
					if($profile_pic): echo base_url("assets_module/user_profile/{$profile_pic}");
					else: echo base_url("assets/img/".($gender=='Male' ? "male": "female")."-default.jpg");
					endif;
				?>
			">
		</div>
		<h5 class="name" title="<?= $name ?>"><?= $name ?></h5>
		<b class="user-type">(<?= $user_type ?>)</b>
	</div>

	<button class="drop-button" target="#employee-collapse">
		<div class="w-100">
			<i class="fas fa-users left"></i>Accounts
		</div>
		<i class="fas fa-angle-up right"></i>
	</button>
	<div class="collapsible" id="employee-collapse">
		<button id="view-employees"><a href="<?= base_url("admin/employee_list") ?>"><i class="fas fa-list"></i>Employee list</a></button>
		<button id="archived-employees"><a href="<?= base_url("admin/archived_employee") ?>"><i class="fas fa-archive"></i>Archived Accounts</a></button>
		<?php if($user_type == "admin"): ?>
			<button id="add-employee" class="sidebar-add-employee-btn"><i class="fas fa-user-plus"></i>Add employee</button>
		<?php endif; ?>
	</div>

	<button class="drop-button" target="#dtr-collapse">
		<div class="w-100">
			<i class="fas fa-calendar-check left"></i>Daily Time Record
		</div>
		<i class="fas fa-angle-up right"></i>
	</button>
	<div class="collapsible" id="dtr-collapse">
		<button id="active-dtr">
			<a href="<?= base_url("admin") ?>">
				<i class="fas fa-calendar-check"></i>Active DTR
			</a>
		</button>
		<button id="dtr-record"><a href="<?= base_url("admin/index_dtr_list") ?>"><i class="fas fa-user-tie"></i>DTR List</a></button>
		<button id="dtr-update-request"><a class="d-flex align-items-center" href="<?= base_url("admin/dtr_update_request") ?>"><i class="fas fa-rotate"></i>DTR Update Request <?= $dtr_request_count > 0 ? '&nbsp; <small class="alert alert-danger p-0 px-1 mb-0 rounded-pill dtr_request_count"><b>'.$dtr_request_count.'</b></small>' : '' ?></a></button>
		<button id="dtr-overtime"><a href="<?= base_url("admin/overtime") ?>"><i class="fas fa-user-clock"></i>Overtime List</a></button>
		<button id="dtr-undertime"><a href="<?= base_url("admin/undertime") ?>"><i class="fas fa-user-clock"></i>Undertime List</a></button>

	</div>

	<button class="drop-button">
		<div class="w-100 leave-drop-trigger">
			<a class="leave-link" href="<?= base_url("admin/leave_requests") ?>">
				<i class="fas fa-sign-out left"></i>Leaves
			</a>
			<?php
				$pending_leave = ($user_type == "admin") ? $this->request_leave_model->count_by_where(['status' => 'pending']) : $this->request_leave_model->count_all_qc_leave_pending();
				if($pending_leave){
					echo '<i class="fas fa-circle notif-dot-leave t-12px"></i>';
				}
			?>
		</div>
	</button>

	<?php if($user_type == "admin"): ?>
		<button class="drop-button d-none" target="#salary-grade">
			<div class="w-100">
				<i class="fas fa-credit-card left"></i>Salary Grade
			</div>
			<i class="fas fa-angle-up right"></i>
		</button>
		<div class="collapsible d-none" id="salary-grade">
			<button><a href="<?= base_url("admin/salary_grade") ?>"><i class="fas fa-coins"></i>Salary Grade List</a></button>
			<button id="add-salary-grade" data-toggle="modal" data-target="#salary-grade-cru-modal"><i class="fas fa-plus"></i>Add Grade</button>
		</div>

		<button class="drop-button" target="#holidays">
			<div class="w-100">
				<i class="fa-solid fa-calendar left"></i>Holidays
			</div>
			<i class="fas fa-angle-up right"></i>
		</button>
		<div class="collapsible" id="holidays">
			<button><a href="<?= base_url("admin/holidays") ?>"><i class="fas fa-calendar-day"></i>Holiday List</a></button>
			<button><a class="d-flex" href="<?= base_url("admin/custom_holidays") ?>"><i class="fas fa-calendar-edit d-flex align-items-center"></i>Custom Holidays</a></button>
			<button id="add-salary-grade" data-toggle="modal" data-target="#holiday-cru-modal"><i class="fas fa-plus"></i>Add Custom</button>
		</div>

		<button class="drop-button" target="#dev-setting">
			<div class="w-100">
				<i class="fa-brands fa-dev left"></i>Developer Options
			</div>
			<i class="fas fa-angle-up right"></i>
		</button>
		<div class="collapsible" id="dev-setting">
			<?php
				$temps = 0;
				$fileList = glob('assets_module/user_profile/*');
			    foreach($fileList as $filename){
			        if(is_file($filename)){
			            $profile = $this->users_model->get_one_by_where(['profile_pic' => basename($filename)]);
			            if(!$profile){
			            	$temps++;
			                break;
			            }
			        }   
			    }
			?>
			<button temp="with-temp" id="temp-button" <?= ($temps == 0) ? 'class="disabled" title="There are no temporary files"':'data-toggle="modal" data-target="#delete-temp-modal"' ?>><i class="fas fa-trash"></i>Delete Temp <?php if($temps == 0){echo '<span class="ongoing-temp-message">There are no temporary files</span>';} ?></button>
			<?php
				$easter =  date("Y-m-d", easter_date(date("Y")));
				$dy_array = [
					["Maundy Thursday", date('Y-m-d', strtotime($easter. ' - 3 days'))],
					["Good Friday", date('Y-m-d', strtotime($easter. ' - 2 day'))],
					["Black Saturday", date('Y-m-d', strtotime($easter. ' - 1 day'))],
					["Easter Sunday", date('Y-m-d', strtotime($easter))]
				];
				$dy = $this->holidays_model->get_all_by_where(['fdc_type' => "dynamic"]);
				$updates = 0;
				foreach($dy as $dy){
					for($a=0;$a<count($dy_array);$a++){
						if($dy["name"] == $dy_array[$a][0]){
							if($dy['date'] !== $dy_array[$a][1]){
								$updates++;
							}
						}
					}
				}
			?>
			<button temp="with-temp" id="update-dynamic-holidays" <?= ($updates == 0) ? 'class="disabled" title="All holidays are up to date"' : 'data-toggle="modal" data-target="#update-dynamic-holidays-modal"'?>><i class="fa fa-refresh"></i>Holidays <?php if($updates == 0){echo '<span class="ongoing-temp-message">All holidays are up to date</span>';}  ?></button>
		</div>
	<?php endif; ?>
</div>

<?php 
	if($user_type == "admin"): 
		$this->load->view('modal/salary-grade-cru-modal');
		$this->load->view('modal/holiday-cru-modal');
		$this->load->view('modal/delete-temp-modal');
		$this->load->view('modal/update-dynamic-holidays-modal');
	endif; 
	$this->load->view('modal/ot-status-modal');
?>