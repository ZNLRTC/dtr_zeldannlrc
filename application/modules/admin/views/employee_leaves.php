<?php
/*
 * Page Name: DTR List
 * Author: Jushua FF
 * Date: 02.06.2023
 */

?>
<div class="w-100 d-flex">
	<?= $this->load->view("partials/sidebar", $user_type_name) ?>
	<div class="w-100 toggled" id="main">
		<?= $this->load->view("partials/header") ?>
		<div id="main-div">
			<div class="table-search-row">
                <?php $first_name = explode(' ', $employee_info['name']) ?>
				<input name='accumulated-leaves' type="hidden" value="<?= $employee_info['employee_leaves_accumulated'] ?>">
				<input name='remaining-leaves' type="hidden" value="<?= $employee_info['employee_leaves_remaining'] ?>">
				<div>
					<h3 class="p-0 m-0"><?= $first_name[0] ?>'s Leaves</h3>
					<small class="d-none mobile-show">
						<span class="text-warning"><b>Special: <span class="special-leaves"><?= $employee_info['employee_special_leave_count'] ?></span></b></span> | 
						<span class="text-info"><b>Birthday: <span class="birthday-leaves"><?= $employee_info['employee_birthday_leave_count'] ?></span></b></span> | 
						<span class="text-danger"><b>Sick: <span class="sick-leaves"><?= $employee_info['employee_sick_leave_count'] ?></span></b></span> | 
						<span class="text-success"><b>Vacation: <span class="vacation-leaves"><?= $employee_info['employee_vacation_leave_count'] ?></span></b></span> 
					</small>
				</div>
				<button class="btn btn-success py-2 me-2 add-leave-btn d-none mobile-show" data-user-name="<?= $employee_info['name'] ?>" data-user-id="<?= $employee_info['id'] ?>"><i class="fa-solid fa-plus"></i> <span class="mobile-hide">Add Leave</span></button>
				<div class="d-flex align-items-center w500-100 justify-content-end">
					<!--button class="btn btn-maroon download-dtr-list" data-toggle="modal" data-target="#download-dtr-list-filter-modal" title="Download DTR List Report"><i class="fa fa-file-pdf"></i></button-->
					
					<button class="btn btn-green download-employee-leave-btn py-2 me-2 mobile-hide"><i class="fa fa-file-excel"></i></button>
                    <button class="btn btn-success py-2 me-2 add-leave-btn mobile-hide" data-user-name="<?= $employee_info['name'] ?>" data-user-id="<?= $employee_info['id'] ?>"><i class="fa-solid fa-plus"></i> <span class="mobile-hide">Add Leave</span></button>
                    <?php if($employee_info['years']): ?>
						<button type="button" class="btn btn-sblue dropdown-toggle py-2 d-none d-sm-block me-2" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fa-solid fa-arrows-left-right-to-line"></i> <span class="mobile-hide">Select Year</span>
						</button>
					<?php endif ?>
                    <ul class="dropdown-menu">
                        <?php foreach($employee_info['years'] as $year): ?>
                            <li><a class="dropdown-item <?= $employee_info['selected_year'] == $year ? 'active' : '' ?>" href="<?= base_url() . 'admin/employee_leaves?id='.$employee_info['id']. '&year=' . $year  ?>"><?= $year ?></a></li>
                        <?php endforeach ?>
                    </ul>
					<input type="text" class="search-bar mobile-hide" placeholder="&#xF002;" id="employee-leave-list-search">
				</div>
				<input type="text" class="search-bar d-none mobile-show" placeholder="&#xF002;" id="employee-leave-list-search-mobile">
			</div>

            <!-- edit here -->
			<div class="px-3">
				<div class="mobile-hide">
					<div class="d-flex justify-content-end">
						<div class="mb-0 py-1 me-3 alert alert-warning rounded-pill d-flex align-items-center">Special: &nbsp; <span class="special-leaves"><?= $employee_info['employee_special_leave_count'] ?></span></div>
						<div class="mb-0 py-1 me-3 alert alert-info rounded-pill d-flex align-items-center">Birthday: &nbsp; <span class="birthday-leaves"><?= $employee_info['employee_birthday_leave_count'] ?>/1</span></div>
						<div class="mb-0 py-1 me-3 alert alert-danger rounded-pill d-flex align-items-center">Sick: &nbsp; <span class="sick-leaves"><?= $employee_info['employee_sick_leave_count'] ?>/<?= idate('m') - $month1 ?></span></div>
						<div class="mb-0 py-1 alert alert-success rounded-pill d-flex align-items-center">Vacation: &nbsp; <span class="vacation-leaves"><?= $employee_info['employee_vacation_leave_count'] ?>/<?= idate('m') - $month1 ?></span></div>
                        
					</div>
				</div> 
			</div>
			<div id="body-row">
				<table id="employee-leave-list" class="table table-striped w-100">
				    <thead>
				        <tr class="bg-lblue align-middle text-white">
				            <th>Date</th>
				            <th class="mobile-hide">Leave Type</th>
				            <th class="mobile-hide">Details</th>
				            <th class="mobile-hide">Remarks</th>
							<th class="">Action</th>
				        </tr>
				    </thead>
				    <tbody>
                        <?php foreach($employee_info['employee_leaves'] as $leave): ?>
							<?php ($leave['remarks'] == NULL || $leave['remarks'] == '') ? $remarks = '<span class="opacity-25">No Remarks</span>' : $remarks = $leave['remarks'] ?>
                            <tr tr-id=<?= $leave['id'] ?>>
                                <td class="date">
									<?= convert_date($leave['date']) ?>
									<div class="d-none mobile-show">
										<br>
										<small>
											<b><i><?= ucwords($leave['leave_type']) ?> Leave | <span class="<?= $leave['status'] == 'approved' ? 'text-success' : 'text-danger ' ?>"><?= ucwords($leave['status']) ?></span></i></b>
										</small>
									</div>
								</td>
                                <td class="mobile-hide leave-type" ><?= ucwords($leave['leave_type']) ?> Leave
                                    <span style="margin-left: 10px;" data-req-id="<?= $leave['id'] ?>"  data-request-id="<?= $leave['id'] ?>" data-emp-name="<?= $name ?>">
                                        <?php if($leave['status'] == 'pending'): ?>
											<small class="mobile-hide alert alert-warning px-2 py-1 rounded-pill mb-0 me-1">Pending<i class="fa-solid fa-spinner text-warning fa-spin"></i></small>
										<?php elseif ($leave['status'] == 'approved'): ?>
                                            <span class="btn bg-none">
                                                <small class="alert alert-success px-2 py-1 rounded-pill mb-0"><i class="fa-solid fa-check"></i> Approved</small>
                                                <small class="alert alert-primary px-2 py-1 rounded-pill generate-admin-pdf-btn">PDF</small> 
                                            </span>     
                                        <?php else: ?>
                                            <span class="btn bg-none">
                                                <small class="alert alert-danger px-2 py-1 rounded-pill mb-0 leave-request-denied-btn"><i class="fa-solid fa-times"></i> Denied</small>
                                                <small class="alert alert-primary px-2 py-1 rounded-pill generate-admin-pdf-btn">PDF</small> 
                                            </span>     
                                        <?php endif ?>
                                    </span>
                                </td>
                                <td class="mobile-hide leave-details"><?= ucwords($leave['details']) ?></td>
                                <td class="mobile-hide leave-remarks"><?= ucwords($remarks) ?></td>
								<td class="d-flex align-items-center">
									<button class="btn edit edit-leaves-btn" data-leave-id="<?= $leave['id'] ?>" data-user-name="<?= $employee_info['name'] ?>">
										<i class="fa-solid fa-pen-to-square"></i>
										<span class="mobile-hide">Edit</span>
									</button>

									<button class="btn cancel cancel-leaves-btn" data-leave-id="<?= $leave['id'] ?>">
										<i class="fas fa-times-circle"></i>
										<span class="mobile-hide">Cancel</span>
									</button>
								</td>
                            </tr>
                        <?php endforeach; ?>
				    </tbody>
				</table>
			</div>
		</div>
	</div>
</div>

<?= $this->load->view('modal/add-leave-modal') ?>
<?= $this->load->view('modal/cancel-leave-modal') ?>
<?= $this->load->view('modal/download-employee-leaves-modal') ?>
<?= $this->load->view('modal/view-denied-leave-request-modal') ?>   
<?= $this->load->view('modal/decline-leave-request-modal') ?>
<?= $this->load->view('modal/approve-leave-request-modal') ?>
<?= $this->load->view('modal/cancel-leave-request-modal') ?>
<?= $this->load->view('modal/delete-leave-request-modal') ?>
<?= $this->load->view('modal/generate-request') ?>

