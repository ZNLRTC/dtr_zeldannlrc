<?php
/*
 * Page Name: Employee
 * Author: MIgs
 * Date: 6.13.2024
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
</form>

<div class="w-100 d-flex">
	<?= $this->load->view("partials/sidebar", $user_type_name) ?>
	<div class="w-100 toggled" id="main">
		<?= $this->load->view("partials/header") ?>
		<div id="main-div">
			<div class="table-search-row">
				<div class="col-sm-4"><h3 class="p-0 m-0">Change Time Request</h3></div>
                <div class="col-sm-8 row justify-content-end">
                    <!-- <div class="col-sm-4 d-flex justify-content-end px-0">
                        <button class="btn btn-success change-time-request-btn"><i class="fa-solid fa-plus"></i> Request</button>
                    </div> -->

                    <div class="col-sm-6 offset-sm-6 d-flex">
                        <div class="col-sm-6 d-flex justify-content-end me-1">
							<button type="button" class="btn btn-sblue dropdown-toggle py-2 d-none d-sm-block me-1" data-bs-toggle="dropdown" aria-expanded="false">
								Date Range
							</button>
							<ul class="dropdown-menu">
								<?php foreach($months as $month): ?>
									<?php 
										$get_date = explode(' ', $month);	
									?>
									<li><a class="dropdown-item <?= ($month_year[0] == $get_date[0] && $month_year[1] == $get_date[2]) ? 'active' : ')' ?>" href="<?= base_url() . 'employee/my_change_time_requests?month=' . $get_date[0] . '&year=' . $get_date[2]  ?>"><?= $get_date[1] .' '. $get_date[2] ?></a></li>
								<?php endforeach ?>
							</ul>

                            <button class="btn btn-success change-time-request-btn"><i class="fa-solid fa-plus"></i> Request</button>
                        </div>

                        <div class="col-sm-6">
                            <input id="ctr-leave-list-search" type="search" class="form-control" placeholder="Search">
                        </div>
                    </div>
                </div>
			</div>
			
			<div id="body-row">
				
				<table id="ctr-leave-list" class="table table-striped">
				    <thead>
				        <tr class="bg-lblue align-middle text-white">
				        	<th>Change Time Requests</th>
				        </tr>
				    </thead>
				    <tbody>
                        <?php foreach($change_time_requests as $ctr): ?>
                            <tr id="ctr-leave-list-tr-<?= $ctr['id'] ?>" data-ctr-id="<?= $ctr['id'] ?>">
                                <?php switch($ctr['status']){
                                    case 'pending':
                                        $color = 'warning';
                                        $text = '<i class="fa-solid fa-spinner fa-spin"></i> Pending';
                                    break;

                                    case 'denied':
                                        $color = 'danger';
                                        $text = '<i class="fa-solid fa-face-sad-cry"></i> Denied';
                                    break;

                                    case 'approved':
                                        $color = 'success';
                                        $text = '<i class="fa-solid fa-face-smile"></i> Approved';
                                    break;
                                } ?>
                                <td class="d-flex">
                                    <div class="col-2"><?= date("M d, Y (D)", strtotime($ctr['date_created'])) ?></div>
                                    <div class="col-8"><?= $ctr['details'] ?></div>
                                    <div class="col-2">
                                        <small class="alert alert-<?= $color ?> px-2 py-1 rounded-pill"><?= $text ?></small> 
                                        <?php if($ctr['status'] == 'pending'): ?>
                                            <small class="alert alert-danger px-2 py-1 rounded-pill pointer cancel-my-ctr-request"><i class="fa-solid fa-xmark"></i> Cancel</small> 
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

<?= $this->load->view('modal/change-time-request-modal') ?>