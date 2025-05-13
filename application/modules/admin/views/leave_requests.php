<?php

/*

 * Page Name: Overtime Requests

 * Author: Jushua FF

 * Date: 01.30.2023

 */

?>

<div class="w-100 d-flex">

	<?= $this->load->view("partials/sidebar", $user_type_name) ?>

	<div class="w-100 toggled" id="main">

		<?= $this->load->view("partials/header") ?>

		<div id="main-div">

			<div class="table-search-row">

				<h3 class="p-0 m-0">Leaves</h3>
				<div class="d-flex align-items-center">
					<div class="me-2">
						<button type="button" class="btn btn-sblue dropdown-toggle py-2 " data-bs-toggle="dropdown" aria-expanded="false">
							<i class="fa-solid fa-arrows-left-right-to-line"></i> <span class="mobile-hide">Select Month</span>
						</button>
						<ul class="dropdown-menu">
							<li><a class="dropdown-item" href="<?= base_url() . 'admin/leave_requests' ?>">---View All---</a></li>
							<?php foreach($leave_month as $month): ?>
								<?php 
									$get_date = explode(' ', $month);	
								?>
								<li><a class="dropdown-item <?= ($month_year[0] == $get_date[0] && $month_year[1] == $get_date[2]) ? 'active' : ')' ?>" href="<?= base_url() . 'admin/leave_requests?month=' . $get_date[0] . '&year=' . $get_date[2]  ?>"><?= $get_date[1] .' '. $get_date[2] ?></a></li>
							<?php endforeach ?>
						</ul>
					</div>
					<button class="btn btn-success py-2 me-2 add-leave-btn"><i class="fa-solid fa-plus"></i> <span class="mobile-hide">Add Leave</span></button>
					<input type="search" class="search-bar mobile-hide" placeholder="&#xF002;" id="leave-list-search">
				</div>
				<input type="search" class="search-bar d-none mobile-show" placeholder="&#xF002;" id="leave-list-search-0">

			</div>

			<div id="body-row">

				<table id="leave-list" class="table table-striped w-100">

				    <thead>

				        <tr class="bg-lblue align-middle text-white">

				            <th class="long-column">Name</th>

				            <th class="mobile-hide">Date</th>

				            <th class="mobile-hide">Leave Type</th>

				            <th class="mobile-hide">Details</th>

				            <th class="mobile-hide">Remarks</th>

							<th>Action</th>

							<th>Dates</th>

				        </tr>

				    </thead>

				    <tbody>

        
				        <!-- <?php 

				            foreach($leave_requests as $lr):

								$lr['remarks'] != NULL ? $remarks = $lr['remarks'] : $remarks = '<span class="opacity-25">No Remarks</span>';
			                    echo '

			                        <tr tr-id="'.$lr['id'].'">

			                            <td class="name long-column">'.$lr['name'].'<br><small class="d-none mobile-show"><b><i>'.convert_date($lr["date"]).' | '. ucwords($lr['leave_type']) .' Leave </i></b></small></td>

			                            <td class="date mobile-hide">'.convert_date($lr['date']).'</td>

                                        <td class="leave-type mobile-hide">'.ucwords($lr['leave_type']).' Leave 
                                            <span style="margin-left: 10px;">';

                                            if ($lr['status'] == 'approved') {
                                                echo '<small class="alert alert-success px-2 py-1 rounded-pill mb-0">
                                                    <i class="fa-solid fa-check"></i> Approved</small>';
                                            } else if ($lr['status'] == 'denied'){
                                                echo '<span class="btn bg-none denied-span-btn"> <small class="alert alert-danger px-2 py-1 rounded-pill mb-0 leave-request-denied-btn">
                                                <i class="fa-solid fa-times"></i> Denied</small></span>';                
                                            }else {
                                                echo '<small class="mobile-hide alert alert-warning px-2 py-1 rounded-pill mb-0 me-1">
                                                Pending  <i class="fa-solid fa-spinner text-warning fa-spin"></i></small>';
                                            }
                                            
                                            echo'</span>
                                        </td>


			                            <td class="leave-details mobile-hide truncate-row">'.$lr['details'].'</td>

										<td class="leave-remarks mobile-hide">'.$remarks.'</td>

										<td class="action-btn">
											<a href="'.base_url() .'admin/employee_leaves?id='. $lr['user_id']. '" class="d-flex justify-content-end">
												<button class="btn view view-leaves-btn d-flex align-items-center ">
													<i class="fa-solid fa-eye me-1"></i> 
													<span class="mobile-hide">View Leaves</span>
												</button>
												
												<button class="btn edit edit-leaves-btn" data-leave-id="'.$lr['id'].'">
													<i class="fa-solid fa-pen-to-square"></i>
													<span class="mobile-hide">Edit</span>
												</button>
												<button class="btn cancel cancel-leaves-btn" data-leave-id="'.$lr['id'].'">
													<i class="fas fa-times-circle"></i>
													<span class="mobile-hide">Cancel</span>
												</button>
											</a>
										</td>

										<td>'.$lr["date"].'</td>
			                        </tr>

			                    ';

				            endforeach;

				        ?> -->

                        <?php 
                        
                            foreach ($leave_requests as $lr): ?>

                            <?php
                                $lr['remarks'] != NULL ? $remarks = $lr['remarks'] : $remarks = '<span class="opacity-25">No Remarks</span>';
                            ?>

                            <tr tr-id="<?= $lr['id'] ?>">
                                <td class="name long-column"><?= $lr['name'] ?><br><small class="d-none mobile-show"><b><i><?= convert_date($lr["date"]) ?> | <?= ucwords($lr['leave_type']) ?> Leave </i></b></small></td>
                                <td class="date mobile-hide"><?= convert_date($lr['date']) ?></td>
                                <td class="leave-type mobile-hide"><?= ucwords($lr['leave_type']) ?> Leave 
                                    <span style="margin-left: 10px;" data-req-id="<?= $lr['id'] ?>">
                                        <?php if ($lr['status'] == 'approved'): ?>
                                            <span class="btn bg-none">
                                                <small class="alert alert-success px-2 py-1 rounded-pill mb-0"><i class="fa-solid fa-check"></i> Approved</small>
                                                <small class="alert alert-primary px-2 py-1 rounded-pill generate-admin-pdf-btn">PDF</small>      
                                            </span>                          
                                        <?php elseif ($lr['status'] == 'denied'): ?>
                                            <span class="btn bg-none" data-request-id="<?= $lr['id'] ?>">
                                                <small class="alert alert-danger px-2 py-1 rounded-pill mb-0 leave-request-denied-btn"><i class="fa-solid fa-times"></i> Denied</small> 
                                                <small class="alert alert-primary px-2 py-1 rounded-pill generate-admin-pdf-btn-denied">PDF</small> 
                                            </span>
                                        <?php else: ?>
                                            <small class="mobile-hide alert alert-warning px-2 py-1 rounded-pill mb-0 me-1">
                                                Pending  <i class="fa-solid fa-spinner text-warning fa-spin"></i>
                                            </small>

                                        <?php endif; ?>

                                    </span>
                                </td>
                                <td class="leave-details mobile-hide truncate-row"><?= $lr['details'] ?></td>
                                <td class="leave-remarks mobile-hide"><?= $remarks ?></td>
                                <td class="action-btn">
                                    <a href="<?= base_url() ?>admin/employee_leaves?id=<?= $lr['user_id'] ?>" class="d-flex justify-content-end">
                                        <button class="btn view view-leaves-btn d-flex align-items-center">
                                            <i class="fa-solid fa-eye me-1"></i> 
                                            <span class="mobile-hide">View Leaves</span>
                                        </button>
                                        <button class="btn edit edit-leaves-btn" data-leave-id="<?= $lr['id'] ?>">
                                            <i class="fa-solid fa-pen-to-square"></i>
                                            <span class="mobile-hide">Edit</span>
                                        </button>
                                        <button class="btn cancel cancel-leaves-btn" data-leave-id="<?= $lr['id'] ?>">
                                            <i class="fas fa-times-circle"></i>
                                            <span class="mobile-hide">Cancel</span>
                                        </button>
                                    </a>
                                </td>
                                <td><?= $lr["date"] ?></td>
                            </tr>

                        <?php endforeach; ?>


				    </tbody>

				</table>

			</div>

		</div>

	</div>

</div>



<?= $this->load->view('modal/leave-status-modal') ?>
<?= $this->load->view('modal/add-leave-modal') ?>
<?= $this->load->view('modal/cancel-leave-modal') ?>
<?= $this->load->view('modal/generate-request') ?>
