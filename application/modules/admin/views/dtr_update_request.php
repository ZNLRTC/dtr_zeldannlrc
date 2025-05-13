<?php
/*
 * Page Name: DTR Update Request Page
 * Author: Jushua FF
 * Date: 08.22.2023
 */
$user_type_name = $this->users_type_model->get_one_by_where(['id'=>$user_type]);
?>
<div class="w-100 d-flex">
	<?= $this->load->view("partials/sidebar", $user_type_name) ?>
	<div class="w-100 toggled" id="main">
		<?= $this->load->view("partials/header") ?>
		<div id="main-div">
			<div class="table-search-row">
				<h3 class="p-0 m-0">DTR Update Request List</h3>
				<div class="d-flex align-items-center w500-100">
					<?php if($dtr_month): ?>
						<button type="button" class="btn btn-sblue dropdown-toggle py-2 d-none d-sm-block me-2" data-bs-toggle="dropdown" aria-expanded="false">
							Select Date Range
						</button>
					<?php endif ?>
					<ul class="dropdown-menu">
                        <?php foreach($dtr_month as $month): ?>
                            <?php 
                                $get_date = explode(' ', $month);	
                            ?>
                            <li><a class="dropdown-item <?= ($month_year[0].'-'.$month_year[1] == $get_date[0].'-'.$get_date[2]) ? 'active' : '' ?>" href="<?= base_url() . 'admin/dtr_update_request?month=' . $get_date[0] . '&year=' . $get_date[2]  ?>"><?= $get_date[1] .' '. $get_date[2] ?></a></li>
                        <?php endforeach ?>
                    </ul>
					<input type="text" class="search-bar" placeholder="&#xF002;" id="request-dtr-update-list-search">
				</div>
			</div>
			<div id="body-row">
				<table id="request-dtr-update-list" class="table table-striped w-100">
				    <thead>
				        <tr class="bg-lblue align-middle text-white">
				            <th>Name</th>
				            <th class="mobile-hide">Date</th>
				            <th class="mobile-hide">Message</th>
				            <th class="mobile-hide">Status</th>
				            <th></th>
				            
				        </tr>
				    </thead>
				    <tbody>
				        
						<?php foreach($rdtr as $rdtr): ?>

							<?php 
								$button_disabled = '<button class="update-rdtr-status btn edit d-none mobile-show py-0 opacity-0" disabled>
														<i class="fas fa-edit"></i>
													</button>';
													
								switch($rdtr['status']){
									case 'approved':
										$status = '<span class="mobile-hide text-success">Update Complete</span>' . $button_disabled;
									break;
									case 'denied':
										$status = '<span class="mobile-hide text-danger">Reason Denied: ' .$rdtr["reason_denied"]. '</span>' . $button_disabled;
									break;
									case 'done':
										$status = '<span class="mobile-hide text-success">Update completed</span>' . $button_disabled;
									break;
									default: 
										$status = '<button class="update-rdtr-status btn edit" data-toggle="modal" data-updater="'.$id.'" data-target="#dtr-update-request-status-modal" title="Update Status" rdtr-id="'.$rdtr['id'].'">
														<i class="fas fa-edit"></i> <span class="mobile-hide">Update</span>
													</button>';
								}	
							?>

							<tr class="col-2" tr-id="<?= $rdtr['id'] ?>">
								<td class="name"> <?= (strlen($rdtr['name']) > 20)?  substr($rdtr['name'], 0, 17) . '...' : $rdtr['name'] ?><br>
									<small class="d-none mobile-show"><b><?= date( "M d, Y (D)", strtotime( $rdtr['date'])) .' | '. $rdtr["status"] ?> </b></small>
								</td>
								<td class="col-2 date lh-12px mobile-hide">
									<?= date( "M d, Y (D)",strtotime( $rdtr['date'])) ?>
								</td>
								<td class="col-5 message lh-12px mobile-hide"> 
									<?= $rdtr['message'] ?>
								</td>
								<td class="col-1 status lh-12px mobile-hide">
									<span class="<?= $rdtr['status'] == 'pending' ? 'text-warning' : '' ?>"><?= ucwords($rdtr["status"]) ?></span>
								</td>
								<td class="col-2">
									<a href="<?= base_url() . 'admin/employee_dtr?user_id=' . $rdtr['user_id'] ?>" class="me-1"><button class="btn view"><i class="fa-solid fa-eye"></i> <span class="mobile-hide">View DTR</span></button></a>
									<?= $status ?>
								</td>
							</tr>
						<?php $total_hours_per_row = 0; endforeach ?>
				    </tbody>
				</table>
			</div>
		</div>
	</div>
</div>

<?= $this->load->view('modal/dtr-update-request-status-modal') ?>
