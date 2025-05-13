<?php
    $user_type_name = $this->users_type_model->get_one_by_where(['id'=>$user_type]);
    $user_type_name['user_id'] = $id;
?>

<div class="w-100 d-flex">
	<?= $this->load->view("partials/sidebar", $user_type_name) ?>
	<div class="w-100 toggled" id="main">
		<?= $this->load->view("partials/header") ?>
		<div id="main-div">
			<div class="table-search-row">
				<div class="col-lg-4 col-md-12 col-sm-12">
                    <?php $first_name = explode(' ', $employee_info['name']) ?>
					<h3 class="p-0 m-0 me-5"><?= $first_name[0] ?>'s Leaves</h3>
                    <small class="d-none mobile-show">
						<span class="text-warning"><b>SL: <span class="special-leaves"><?= $employee_info['employee_special_leave_count'] ?></span></b></span> | 
						<span class="text-info"><b>BL: <span class="birthday-leaves"><?= $employee_info['employee_birthday_leave_count'] ?></span></b></span> | 
						<span class="text-danger"><b>SL: <span class="sick-leaves"><?= $employee_info['employee_sick_leave_count'] ?></span></b></span> | 
						<span class="text-success"><b>VL: <span class="vacation-leaves"><?= $employee_info['employee_vacation_leave_count'] ?></span></b></span> 
					</small>
				</div>
                <div class="col-lg-8 col-md-12 col-sm-6 d-flex justify-content-end">
                    <div class="mobile-hide">
						<div class="d-flex">
							<div class="mb-0 py-1 me-3 alert alert-warning rounded-pill d-flex align-items-center">Special: &nbsp; <span class="special-leaves"><?= $employee_info['employee_special_leave_count'] ?></span></div>
							<div class="mb-0 py-1 me-3 alert alert-info rounded-pill d-flex align-items-center">Birthday: &nbsp; <span class="birthday-leaves"><?= $employee_info['employee_birthday_leave_count'] ?>/1</span></div>
							<div class="mb-0 py-1 me-3 alert alert-danger rounded-pill d-flex align-items-center">Sick: &nbsp; <span class="sick-leaves"><?= $employee_info['employee_sick_leave_count'] ?>/<?= idate('m') - $month1 ?></span></div>
							<div class="mb-0 py-1 me-3 alert alert-success rounded-pill d-flex align-items-center">Vacation: &nbsp; <span class="vacation-leaves"><?= $employee_info['employee_vacation_leave_count'] ?>/<?= idate('m') - $month1 ?></span></div>
						</div>
					</div> 
                    <div>
					    <input id="employee-leave-list-search" class="form-control mobile-hide" type="search" placeholder="Search">
                    </div>
				</div>
			</div>
            <div class="px-3">
                <table id="employee-leave-list" class="table table-striped w-100">
				    <thead>
				        <tr class="bg-lblue align-middle text-white">
				            <th>Date</th>
				            <th class="mobile-hide">Leave Type</th>
				            <th class="mobile-hide">Details</th>
				            <th class="mobile-hide">Remarks</th>
				            <th class="mobile-hide">Action</th>
				        </tr>
				    </thead>
				    <tbody>
                        <?php foreach($employee_info['employee_leaves'] as $leave): ?>
							<?php 
								($leave['remarks'] == NULL || $leave['remarks'] == '') ? $remarks = '<span class="opacity-25">No Remarks</span>' : $remarks = $leave['remarks'];

								switch($leave['status']){
									case 'approved':
										$status_bg = 'alert-success';
										$icon = '<i class="fa-solid fa-check"></i>';
									break;

									case 'retracted':
										$status_bg = 'alert-warning';
										$icon = '<i class="fa-solid fa-check"></i>';
									break;

									case 'pending':
										$status_bg = 'alert-warning';
										$icon = '<i class="fa-solid fa-spinner text-warning fa-spin"></i>';
									break;

									case 'denied':
										$status_bg = 'alert-danger';
										$icon = '<i class="fa-solid fa-xmark"></i>';
									break;
									
									default:
										$status_bg = 'alert-danger';
										$icon = '<i class="fa-solid fa-xmark"></i>';
									break;
								}
								
								switch($leave['leave_count']){
									case 1:
										$leave_count = '<small class="alert alert-success px-2 py-1 rounded-pill">Whole-day</small>';
									break;

									case 0.5:
										$leave_count = '<small class="alert alert-warning px-2 py-1 rounded-pill">Half-day</small>';
									break;
								}
							?>
                            <tr tr-id=<?= $leave['id'] ?>>
                                <td class="date col-2">
									<?= convert_date($leave['date']) ?>
									<div class="d-none mobile-show">
										<br>
										<small>
											<b><i><?= ucwords($leave['leave_type']) ?> Leave | <span class="<?= $leave['status'] == 'approved' ? 'text-success' : 'text-danger ' ?>"><?= ucwords($leave['status']) ?></span></i></b>
										</small>
									</div>
								</td>
                                <td class="mobile-hide leave-type col-2">
									<?= ucwords($leave['leave_type']) ?> Leave 
									<?= $leave_count ?>
									<?php if($leave['salary_deduction'] == 1): ?>
										<small class="mobile-hide d-inline-block alert mb-0 alert-danger px-2 py-1 rounded-pill">W/o Pay</small>
									<?php endif ?>
								</td>
                                <td class="mobile-hide leave-details col-4"><?= ucwords($leave['details']) ?></td>
                                <td class="mobile-hide leave-remarks col-1"><?= ucwords($remarks) ?></td>
                                <td class="mobile-hide leave-remarks col-2" data-req-id="<?= $leave['id'] ?>">
									<?php if($leave['status'] == 'pending'): ?>
										<div class="text-success d-inline-block me-2 pointer approve-leave-request-btn">Approve</div>
										<div class="text-danger d-inline-block pointer decline-leave-request-btn">Decline</div>
									<?php elseif($leave['status'] == 'approved'): ?>
										<div class="btn bg-none text-left">
											<small class="alert alert-success px-2 py-1 rounded-pill"><i class="fa-solid fa-check"></i> Approved</small><br>
											<small class="mobile-hide"><b>By: <?= $leave['approved_by_name'] ?></b></small>
										</div>
									<?php elseif($leave['status'] == 'retracted'): ?>
										<div class="btn bg-none text-left">
											<small class="alert alert-warning px-2 py-1 rounded-pill"><i class="fa-solid fa-check"></i> Retracted</small><br>
											<small class="mobile-hide"><b>By: <?= $leave['approved_by_name'] ?></b></small>
										</div>
									<?php elseif($leave['status'] == 'denied'): ?>
										<small class="alert alert-danger px-2 py-1 rounded-pill"><i class="fa-solid fa-xmark"></i> Denied</small>
									<?php endif ?>
								</td>
								
                            </tr>
                        <?php endforeach; ?>
				    </tbody>
				</table>
            </div>
			
		</div>
	</div>
</div>

<?php $this->load->view('modal/request-leave-modal'); ?>
<?php $this->load->view('modal/approve-leave-request-modal'); ?>
<?php $this->load->view('modal/decline-leave-request-modal'); ?>