<?php
    $user_type_name = $this->users_type_model->get_one_by_where(['id'=>$user_type]);
    $user_type_name['user_id'] = $id;

	if($employee['profile_pic'] != NULL){
		$profile_picture = base_url() . 'assets_module/user_profile/' . $employee['profile_pic'];
	}else{
		$profile_picture = base_url() . 'assets/img/' . strtolower($employee['gender']) . '-default.jpg';
	}
?>

<div class="w-100 d-flex">
	<?= $this->load->view("partials/sidebar", $user_type_name) ?>
	<div class="w-100 toggled" id="main">
		<?= $this->load->view("partials/header") ?>
		
		<section>
			<div class="container-fluid">
				<div class="container">
					<div class="row mt-5">
						<div class="col-md-4 d-flex justify-content-center">
							<div class="profile-container">
								<img src="<?= $profile_picture ?>" alt="<?= $employee['name'] ?> Photo">
							</div>
						</div>
						
						<div class="col-md-8" id="profile-content-container" data-user-id = "<?= $employee['id'] ?>">
							<div class="d-flex align-items-center justify-content-between">
								<div>
									<h2 class="mb-0 pb-0 employee-name" contenteditable><?= $employee['name'] ?></h2>
									<h5 class="p-0 text-primary on-edit-d-none"><?= $employee['user_type'] ?></h5>
									<select class="form-select d-none" name="department">
										<?php foreach($user_roles as $role): ?>
											<option value="<?= $role['id']?>" class="user-type-<?= $role['id'] ?>" <?= $employee['user_type'] == $role['user_type'] ? 'selected' : '' ?>><?= $role['user_type'] ?></option>
										<?php endforeach ?>
									</select>
								</div>
								<?php if($user_type == 1 || $user_type == 5 || $user_type == 8 || $user_type == 9 || $user_type == 11): ?>
									<div class="pointer edit-employee-profile-btn text-primary">
										<h4 title="Edit Profile" class="mb-0 pb-0"><i class="fa-solid fa-pen-to-square"></i> Edit </h4>
									</div>
								<?php endif ?>
							</div>
							
							<div class="row border-top mt-3">
								<div class="col-md-6 py-1"><b>Username:</b></div>
								<div class="col-md-6 py-1">
									<span class="on-edit-d-none"><?= $employee['username'] ? $employee['username'] : '<span class="opacity-25">No Record</span>' ?></span>
									<input type="text" value="<?= $employee['username'] ?>" name="user-name" class="form-control d-none">
								</div>

								<div class="col-md-6 py-1"><b>Email:</b></div>
								<div class="col-md-6 py-1">
									<span class="on-edit-d-none"><?= $employee['email'] ? $employee['email'] : '<span class="opacity-25">No Record</span>' ?></span>
									<input type="email" value="<?= $employee['email'] ?>" name="email" class="form-control d-none">
								</div>

								<div class="col-md-6 py-1"><b>Mobile Number:</b></div>
								<div class="col-md-6 py-1">
									<span class="on-edit-d-none"><?= $employee['mobile_number'] ? $employee['mobile_number'] : '<span class="opacity-25">No Record</span>' ?></span>
									<input type="number" value="<?= $employee['mobile_number'] ?>" name="mobile-number" class="form-control d-none">
								</div>

								<div class="col-md-6 py-1"><b>Branch:</b></div>
								<div class="col-md-6 py-1">
									<span class="on-edit-d-none"><?= $employee['branch'] ? $employee['branch'] : '<span class="opacity-25">No Record</span>' ?></span>
									<select class="form-select d-none" name="branch">
										<option value="Baguio City" <?= $employee['branch'] == 'Baguio City' ? 'selected' : ''  ?>>Baguio City</option>
										<option value="Quezon City" <?= $employee['branch'] == 'Quezon City' ? 'selected' : ''  ?>>Quezon City</option>
									</select>
								</div>

								<div class="col-md-6 py-1"><b>Gender:</b></div>
								<div class="col-md-6 py-1">
									<span class="on-edit-d-none"><?= $employee['gender'] ? $employee['gender'] : '<span class="opacity-25">No Record</span>' ?></span>
									<select class="form-select d-none" name="gender">
										<option value="Male" <?= $employee['gender'] == 'Male' ? 'selected' : ''  ?>>Male</option>
										<option value="Female" <?= $employee['gender'] == 'Female' ? 'selected' : ''  ?>>Female</option>
									</select>
								</div>
							</div>
						</div>

						<div class="row mt-5 mx-0 py-3 bg-light rounded">
							<h2 class="mb-3 border-bottom ">Schedules:</h2>
							
							<div class="col-md-6 schedule-info-column">
								<?php 
									$emp_sched = $employee['schedule'];
									$monday = explode('-', $emp_sched['monday']);
									$tuesday = explode('-', $emp_sched['tuesday']);
									$wednesday = explode('-', $emp_sched['wednesday']);
									$thursday = explode('-', $emp_sched['thursday']);
									$friday = explode('-', $emp_sched['friday']);

									$mon_wb = explode('/', $emp_sched['monday_workbase']);
									$tue_wb = explode('/', $emp_sched['tuesday_workbase']);
									$wed_wb = explode('/', $emp_sched['wednesday_workbase']);
									$thu_wb = explode('/', $emp_sched['thursday_workbase']);
									$fri_wb = explode('/', $emp_sched['friday_workbase']);
								?>
								<h5 class="mb-0 pb-0 border-bottom col-md-11">Fixed Schedule:</h5>
								<div class="user-fixed-schedule-response"></div>
								<input type="hidden" name="user-id" value="<?= $employee['id'] ?>">
								<div class="row input-con mt-3">
									<div class="col-md-2 d-flex align-items-center justify-content-end"><label>Monday: </label></div>
										<div class="col-md-6 d-flex align-items-center">
											<input class="form-control w-50 me-1" type="time" name="schedule-monday-in" value="<?= $monday[0] ?>" required> - <input class="form-control w-50 ms-1" type="time" name="schedule-monday-out" value="<?= $monday[1] ?>">
										</div>
										<div class="col-md-3 d-flex align-items-center justify-content-between">
											<div class="form-check mb-0">
												<input class="form-check-input" type="checkbox" value="WFH" name="monday-workbase" id="monday-workbase-wfh" <?= in_array('WFH', $mon_wb) ? 'checked' : '' ?>>
												<label for="monday-workbase-wfh" class="pointer form-check-label">WFH</label> 
											</div>
											<div class="form-check mb-0">
												<input class="form-check-input" type="checkbox" value="Office" name="monday-workbase" id="monday-workbase-office" <?= in_array('Office', $mon_wb) ? 'checked' : '' ?>>
												<label for="monday-workbase-office" class="pointer form-check-label">Office</label> 
											</div>
										</div>
									</div>

									<div class="row input-con mt-3">
										<div class="col-md-2 d-flex align-items-center justify-content-end"><label>Tuesday: </label></div>
										<div class="col-md-6 d-flex align-items-center">
											<input class="form-control w-50 me-1" type="time" name="schedule-tuesday-in" value="<?= $tuesday[0] ?>"> - <input class="form-control w-50 ms-1" type="time" name="schedule-tuesday-out" value="<?= $tuesday[1] ?>">
										</div>
										<div class="col-md-3 d-flex align-items-center justify-content-between">
											<div class="form-check mb-0">
												<input class="form-check-input" type="checkbox" value="WFH" name="tuesday-workbase" id="tuesday-workbase-wfh" <?= in_array('WFH', $tue_wb) ? 'checked' : '' ?>>
												<label for="tuesday-workbase-wfh" class="pointer form-check-label">WFH</label> 
											</div>
											<div class="form-check mb-0">
												<input class="form-check-input" type="checkbox" value="Office" name="tuesday-workbase" id="tuesday-workbase-office" <?= in_array('Office', $tue_wb) ? 'checked' : '' ?>>
												<label for="tuesday-workbase-office" class="pointer form-check-label">Office</label> 
											</div>
										</div>
									</div>

									<div class="row input-con mt-3">
										<div class="col-md-2 d-flex align-items-center justify-content-end"><label>Wednesday: </label></div>
										<div class="col-md-6 d-flex align-items-center">
											<input class="form-control w-50 me-1" type="time" name="schedule-wednesday-in" value="<?= $wednesday[0] ?>"> - <input class="form-control w-50 ms-1" type="time" name="schedule-wednesday-out" value="<?= $wednesday[1] ?>">
										</div>
										<div class="col-md-3 d-flex align-items-center justify-content-between">
											<div class="form-check mb-0">
											<input class="form-check-input" type="checkbox" value="WFH" name="wednesday-workbase" id="wednesday-workbase-wfh" <?= in_array('WFH', $wed_wb) ? 'checked' : '' ?>>
											<label for="wednesday-workbase-wfh" class="pointer form-check-label">WFH</label> 
											</div>
											<div class="form-check mb-0">
											<input class="form-check-input" type="checkbox" value="Office" name="wednesday-workbase" id="wednesday-workbase-office" <?= in_array('Office', $wed_wb) ? 'checked' : '' ?>>
											<label for="wednesday-workbase-office" class="pointer form-check-label">Office</label> 
											</div>
										</div>
									</div>

									<div class="row input-con mt-3">
										<div class="col-md-2 d-flex align-items-center justify-content-end"><label>Thursday: </label></div>
										<div class="col-md-6 d-flex align-items-center">
											<input class="form-control w-50 me-1" type="time" name="schedule-thursday-in" value="<?= $thursday[0] ?>"> - <input class="form-control w-50 ms-1" type="time" name="schedule-thursday-out" value="<?= $thursday[1] ?>">
										</div>
										<div class="col-md-3 d-flex align-items-center justify-content-between">
											<div class="form-check mb-0">
											<input class="form-check-input" type="checkbox" value="WFH" name="thursday-workbase" id="thursday-workbase-wfh" <?= in_array('WFH', $thu_wb) ? 'checked' : '' ?>>
											<label for="thursday-workbase-wfh" class="pointer form-check-label">WFH</label> 
											</div>
											<div class="form-check mb-0">
											<input class="form-check-input" type="checkbox" value="Office" name="thursday-workbase" id="thursday-workbase-office" <?= in_array('Office', $thu_wb) ? 'checked' : '' ?>>
											<label for="thursday-workbase-office" class="pointer form-check-label">Office</label> 
											</div>
										</div>
									</div>

									<div class="row input-con mt-3">
										<div class="col-md-2 d-flex align-items-center justify-content-end"><label>Friday: </label></div>
										<div class="col-md-6 d-flex align-items-center">
											<input class="form-control w-50 me-1" type="time" name="schedule-friday-in" value="<?= $friday[0] ?>"> - <input class="form-control w-50 ms-1" type="time" name="schedule-friday-out" value="<?= $friday[1] ?>">
										</div>
										<div class="col-md-3 d-flex align-items-center justify-content-between">
											<div class="form-check mb-0">
												<input class="form-check-input" type="checkbox" value="WFH" name="friday-workbase" id="friday-workbase-wfh" <?= in_array('WFH', $fri_wb) ? 'checked' : '' ?>>
												<label for="friday-workbase-wfh" class="pointer form-check-label">WFH</label> 
											</div>
											<div class="form-check mb-0">
												<input class="form-check-input" type="checkbox" value="Office" name="friday-workbase" id="friday-workbase-office" <?= in_array('Office', $fri_wb) ? 'checked' : '' ?>>
												<label for="friday-workbase-office" class="pointer form-check-label">Office</label> 
											</div>
										</div>
									</div>
								
								<!-- button -->
									<?php if($user_type == 1 || $user_type == 5 || $user_type == 8 || $user_type == 9 || $user_type == 11): ?>
										<div class="row input-con emp-cru-update-schedule-container mt-3">
											<div class="col-md-7 offset-md-4 text-right">
												<button class="btn btn-sblue emp-cru-update-schedule-btn">Save Schedule</button>
											</div>
										</div>
									<?php endif ?>
								</div>

							<div class="col-md-6 ">
								<h5 class="pb-0 border-bottom">Custom Schedule:</h5>

								<div class="user-temporary-schedule-response"></div>
								<div class="row input-con mt-3">
									<div class="col-md-10 offset-md-2 d-flex">
										<div class="form-check mb-0 me-3">
											<input class="form-check-input" type="checkbox" value="WFH" name="temp-workbase" id="temp-workbase-wfh">
											<label for="temp-workbase-wfh" class="pointer form-check-label">WFH</label> 
										</div>
										<div class="form-check mb-0">
											<input class="form-check-input" type="checkbox" value="Office" name="temp-workbase" id="temp-workbase-office">
											<label for="temp-workbase-office" class="pointer form-check-label">Office</label> 
										</div>
									</div>
								</div>

								<div class="row input-con mt-3">
									<div class="col-md-2 d-flex align-items-center justify-content-end"><label>Date: </label></div>
									<div class="col-md-10 d-flex align-items-center">
										<input class="form-control me-1" type="date" name="temp-schedule-date-from"> - <input class="form-control ms-1" type="date" name="temp-schedule-date-to">
									</div>
									
								</div>
								
								<div class="row input-con mt-3">
									<div class="col-md-2 d-flex align-items-center justify-content-end"><label>Time: </label></div>
									<div class="col-md-10 d-flex align-items-center">
									<input class="form-control w-50 me-1" type="time" name="temp-schedule-in"> - <input class="form-control w-50 ms-1" type="time" name="temp-schedule-out">
									</div>
								</div>
								
								<!-- button -->
								<?php if($user_type == 1 || $user_type == 5 || $user_type == 8 || $user_type == 9 || $user_type == 11): ?>
									<div class="row input-con emp-cru-save-temp-schedule-container mt-3">
										<div class="col-md-10 offset-md-2 text-right">
											<button type="submit" class="btn btn-sblue emp-cru-save-temp-schedule-btn">Save Schedule</button>
										</div>
									</div>
								<?php endif ?>
								
								<div class="d-flex justify-content-end mt-5 mb-3">
									<div>
										<select name="temp-sched-month-filter" id="" class="form-select">
											<option selected disabled>--Select Month--</option>
											<?php foreach($months as $month): ?>
												<?php 
													$exp_month = explode(' ', $month);	
												?>
												<option <?= $current_month_year == $exp_month[0] .'-'. $exp_month[2] ? 'selected' : '' ?> value="<?=$employee['id'] .'-'. $exp_month[0] .'-'. $exp_month[2] ?>"><?= $exp_month[1] ?></option>
											<?php endforeach ?>
										</select>
									</div>
								</div>
								
								<table id="temporary-schedule-table" class="w-100 table-striped ">
								<thead>
									<tr>
										<th>Date</th>
										<th>Time In</th>
										<th>Time Out</th>
										<th>Area</th>
										<th class="text-center">Action</th>
									</tr>
								</thead>

								<tbody>
									<?php foreach($employee['temp_schedule'] as $sched): ?>
										<?php 
											if($sched['in-out'] != NULL){
												$exp_sched = explode('-', $sched['in-out']);
												$in = date('h:iA', strtotime($exp_sched[0]));
												$out = date('h:iA', strtotime($exp_sched[1]));
											}	
										?>
										<tr id="temp-sched-row-<?= $sched['id'] ?>">
											<td><?= date( "M d, Y (D)",strtotime( $sched['date'])) ?></td>
											<td><?= $in ?></td>
											<td><?= $out ?></td>
											<td><?= $sched['workbase'] ?></td>
											<td class="text-right sched-<?= $sched['id'] ?>">
												<?php if($user_type == 1 || $user_type == 5 || $user_type == 8 || $user_type == 9 || $user_type == 11): ?>
													<button class="btn edit" data-sched-id="<?= $sched['id'] ?>" data-user-id="<?= $employee['id'] ?>" data-date="<?= $sched['date'] ?>" data-time-in="<?= $exp_sched[0] ?>" data-time-out="<?= $exp_sched[1] ?>" data-workbase="<?= $sched['workbase'] ?>">Edit</button>
													<button class="btn cancel" data-sched-id="<?= $sched['id'] ?>">Cancel</button>
												<?php else: ?>
													<span class="opacity-25">-</span>
												<?php endif ?>
											</td>
										</tr>
									<?php endforeach ?>
								</tbody>
								</table>
							</div>
							
						</div>
						
					</div>
				</div>
			</div>
		</section>
	</div>
</div>