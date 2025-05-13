<?php
/*
 * Page Name: Employee
 * Author: Jushua FF
 * Date: 09.11.2022
 */
$user_type_name = $this->users_type_model->get_one_by_where(['id'=>$user_type]);
$user_type_name['user_id'] = $id;
$user_name = explode(' ', $name);

?>
<form class="d-none">
    <?php
        $timezone = new DateTimeZone('Asia/Manila');
        $now = new DateTime('now', $timezone);
    ?>
    <input type="hidden" id="current-date" value="<?= $now->format('F d, Y') ?>">
    <input type="hidden" id="current-time" value="<?= $now->format('H:i:s A') ?>">
</form>

<div class="w-100 d-flex">
	<?= $this->load->view("partials/sidebar", $user_type_name) ?>
	<div class="w-100 toggled" id="main">
		<?= $this->load->view("partials/header") ?>
		<div id="main-div">
			<div class="table-search-row">
				<div class="col-sm-4">
					<h3 class="p-0 m-0"><?= $user_name[0] ?>'s Leaves</h3>
					<div class="d-none mobile-show">
						<small class="text-warning"><b>SL: <?= $employee_special_leave_count ?></b></small> |
						<small class="text-info"><b>BL: <?= $employee_birthday_leave_count ?>/1</b></small> | 
						<small class="text-danger"><b>SL: <?= $employee_sick_leave_count ?>/<?= idate('m') - $month1 ?></b></small> | 
						<small class="text-success"><b>VL: <?= $employee_vacation_leave_count ?>/<?= idate('m') - $month1 ?></b></small>
					</div>
				</div>
				
				<div class="d-none d-sm-block col-sm-8">
					<div class="d-flex align-items-center w500-100 justify-content-end">
						<button class="btn btn-maroon download-dtr-list d-none" id="download-one-employee-dtr" data-user-id="<?= $id ?>" data-toggle="modal" data-target="#download-my-dtr-list-filter-modal" title="Download DTR List Report" disabled><i class="fa fa-file-pdf"></i></button>

						<button type="button" class="btn btn-sblue dropdown-toggle py-2 d-none d-sm-block me-2" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fa-solid fa-arrows-left-right-to-line"></i> <span class="mobile-hide">Select Year</span>
						</button>
						<ul class="dropdown-menu">
							<?php foreach($years as $year): ?>
								<li><a class="dropdown-item <?= $selected_year == $year ? 'active' : '' ?>" href="<?= base_url() . 'employee/leaves?year=' . $year  ?>"><?= $year ?></a></li>
							<?php endforeach ?>
						</ul>
						<button class="btn btn-success request-leave-btn py-2 me-2">Request for Leave</button>
						<input type="text" class="search-bar m-0" placeholder="&#xF002;" id="my-leave-list-search">
					</div>
				</div>

				<div class="col-md-12 mobile-hide">
					<div class="d-flex my-2 justify-content-end">
						<div class="mb-0 py-1 me-3 alert alert-warning rounded-pill">Special: &nbsp; <span class="special-leaves"><?= $employee_special_leave_count ?></span></div>
						<div class="mb-0 py-1 me-3 alert alert-info rounded-pill">Birthday: &nbsp; <span class="birthday-leaves"><?= $employee_birthday_leave_count ?>/1</span></div>
						<div class="mb-0 py-1 me-3 alert alert-danger rounded-pill">Sick: &nbsp; <span class="sick-leaves"><?= $employee_sick_leave_count ?>/<?= idate('m') - $month1 ?></span></div>
						<div class="mb-0 py-1 alert alert-success rounded-pill">Vacation: &nbsp; <span class="vacation-leaves"><?= $employee_vacation_leave_count ?>/<?= idate('n') - $month1 ?></span></div>
					</div>
				</div>
			</div>
			
			<div id="body-row">
				
				<table id="my-leave-list" class="table table-striped w-100">
				    <thead>
				        <tr>
				        	<th>Leaves Details</th>
				        	<!-- <th class="mobile-hide">Type of Leave</th>
				        	<th class="mobile-hide">Details</th>
				        	<th class="mobile-hide">Remarks</th>
				        	<th class="mobile-hide">Status</th> -->
				        </tr>
				    </thead>
				    <tbody>
				        <?php foreach($leaves as $leave): ?>
							<?php 
								($leave['remarks'] == NULL || $leave['remarks'] == '') ? $remarks = '<span class="opacity-25">No Remarks</span>' : $remarks = $leave['remarks'];

								switch($leave['status']){
									case 'approved':
										$status_bg = 'alert-success';
										$icon = '<i class="fa-solid fa-face-smile text-success"></i>';
									break;

									case 'pending':
										$status_bg = 'alert-warning';
										$icon = '<i class="fa-solid fa-spinner text-warning fa-spin"></i>';
									break;

									case 'retraction':
										$status_bg = 'alert-warning';
										$icon = '<i class="fa-solid fa-spinner text-warning fa-spin"></i>';
									break;

									case 'retracted':
										$status_bg = 'alert-warning';
										$icon = '<i class="fa-solid fa-face-smile text-warning"></i>';
									break;

									case 'denied':
										$status_bg = 'alert-danger';
										$icon = '<i class="fa-solid fa-face-frown text-danger"></i>';
									break;
									
									default:
										$status_bg = 'alert-danger';
										$icon = '<i class="fa-solid fa-face-frown text-danger"></i>';
									break;
								}

								switch($leave['leave_type']){
									case 'birthday':
										$color = 'info';
									break;

									case 'sick':
										$color = 'danger';
									break;

									case 'vacation':
										$color = 'success';
									break;

									case 'special':
										$color = 'warning';
									break;

									default:
										$color = 'success';
									break;
								}

								$date_of_leave = date('Y-m-d', strtotime($leave['date']));
							?>
                            <tr id="leave-req-tr-<?= $leave['id'] ?>" >
								<td class="d-flex align-items-center">
									<div class="col-2"><span><?= convert_date($leave['date']) ?></span> </div>
									<div class="col-3">
										<small class="mobile-hide d-inline-block alert mb-0 alert-<?= $color ?> px-2 py-1 rounded-pill"><?= ucwords($leave['leave_type']) ?> Leave</small>
										<small class="mobile-hide d-inline-block alert mb-0 alert-warning px-2 py-1 rounded-pill"><?= $leave['leave_count'] == 1 ? 'Whole Day' : 'Half Day' ?></small>
										<?php if($leave['salary_deduction'] == 1): ?>
											<small class="mobile-hide d-inline-block alert mb-0 alert-danger px-2 py-1 rounded-pill">W/o Pay</small>
										<?php endif ?>

									</div>
									<div class="col-5"><?= ($leave['status'] == 'retraction' || $leave['status'] == 'retraction-denied' || $leave['status'] == 'retracted') ? $leave['reason_retracted'] : $leave['details']  ?></div>
									<div class="col-2 d-flex align-items-center justify-content-end <?= $leave['status'] == 'denied' ? 'view-sup-message-click pointer' : '' ?>" data-request-id="<?= $leave['id'] ?>" >
										<small class="mobile-hide alert <?= $status_bg ?> px-2 py-1 rounded-pill mb-0 me-1">
											<?= ucwords($leave['status']) ?>  <?= $icon ?>
										</small>
										<?php if($leave['status'] == 'pending'): ?>
											<small class="alert alert-danger px-2 py-1 rounded-pill mb-0 cancel-my-request-btn pointer"><i class="fa-solid fa-xmark"></i> Cancel</small>
										<?php elseif(($leave['status'] == 'approved' || $leave['status'] == 'retraction-denied') && ($date_of_leave >= date('Y-m-d') ) ): ?>
											<small class="alert alert-warning px-2 py-1 rounded-pill mb-0 retract-request-btn pointer"><i class="fa-solid fa-warning"></i>Retract</small>
										<?php endif ?>
									</div>
								</td>
                                
                            </tr>   
                        <?php endforeach ?>
				    </tbody>
				</table>
			</div>
		</div>
	</div>
</div>

<?= $this->load->view('modal/download-my-dtr-list-filter-modal') ?>
<?= $this->load->view('modal/request-dtr-update-modal') ?>
<?= $this->load->view('modal/retract-leave-request-modal') ?>
<?= $this->load->view('modal/eod-report-modal') ?>
<?= $this->load->view('modal/view-request-message-modal') ?>