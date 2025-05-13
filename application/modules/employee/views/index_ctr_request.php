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
                <div class="col-sm-8 row">
                    <div class="d-flex justify-content-end">
                        <button class="btn text-primary clear-filter-btn me-2"><b>Clear Filter</b></button>
                        <button type="button" class="btn btn-sblue dropdown-toggle py-2 d-none d-sm-block me-2" data-bs-toggle="dropdown" data-target="emp-range" aria-expanded="false">
                            Employees
                        </button>
                        <ul class="dropdown-menu" id="emp-range">
                            <li data-url-emp="all" class="li-emp-range dropdown-item pointer">--View All--</li>
                            <?php foreach($employees as $e): ?>
                                <li data-url-emp="<?= $e['id'] ?>" class="li-emp-range dropdown-item pointer <?= $emp_active == $e['id'] ? 'active' : '' ?>"><?= $e['name'] ?></li>
                            <?php endforeach ?>
                        </ul>

                        <button type="button" class="btn btn-sblue dropdown-toggle py-2 d-none d-sm-block me-2" data-bs-toggle="dropdown" data-target="#date-range" aria-expanded="false">
                            Date Range
                        </button>
                        <ul class="dropdown-menu" id="date-range">
                            <?php foreach($months as $month): ?>
                                <?php 
                                    $get_date = explode(' ', $month);	
                                ?>
                                <li data-url-date="<?= $get_date[0] . '-' .$get_date[2] ?>" class="li-date-range dropdown-item pointer <?= ($month_year[0] == $get_date[0] && $month_year[1] == $get_date[2]) ? 'active' : ')' ?>"><?= $get_date[1] .' '. $get_date[2] ?></li>
                            <?php endforeach ?>
                        </ul>

                        <button type="button" class="btn btn-sblue dropdown-toggle py-2 d-none d-sm-block me-2" data-bs-toggle="dropdown" data-target="#status-range" aria-expanded="false">Status</button>

                        <ul class="dropdown-menu" id="status-range">
                            <li data-url-status="all" class="li-date-range dropdown-item pointer">--View All--</li>
                            <li data-url-status="pending" class="li-date-range dropdown-item pointer <?= $status == 'pending' ? 'active' : '' ?>">Pending</li>
                            <li data-url-status="approved" class="li-date-range dropdown-item pointer <?= $status == 'approved' ? 'active' : '' ?>">Approved</li>
                            <li data-url-status="denied" class="li-date-range dropdown-item pointer <?= $status == 'denied' ? 'active' : '' ?>">Denied</li>
                            <li data-url-status="cancelled" class="li-date-range dropdown-item pointer <?= $status == 'cancelled' ? 'active' : '' ?>">Cancelled</li>

                        </ul>

                        <div>
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
                            <tr id="ctr-leave-list-tr-<?= $ctr['id'] ?>" data-user-id="<?= $ctr['id'] ?>" data-ctr-id = "<?= $ctr['id'] ?>" data-request-id = "<?= $ctr['id'] ?>" data-employee="<?= $ctr['employee'] ?>">
                                <?php switch($ctr['status']){
                                    case 'pending':
                                        $color = 'warning';
                                        $text = '<i class="fa-solid fa-spinner fa-spin"></i> Pending';
                                        $by = '<a class="bg-none btn edit-profile hide-from-th" href="view_profile?id='.$ctr["user_id"].'">Change</a> 
                                                <div class="btn text-success approve-ctr-btn">Approve</div>
                                                <div class="btn text-danger decline-ctr-btn">Decline</div>';
                                    break;

                                    case 'denied':
                                        $color = 'danger';
                                        $text = '<i class="fa-solid fa-face-sad-cry"></i> Denied';
                                        $by = '<div class="btn text-left">
                                                    <small class="alert alert-'.$color.' px-2 py-1 rounded-pill denied-ctr-btn me-1">
                                                        <i class="fa-solid fa-xmark"></i> '.ucwords($ctr["status"]).'
                                                    </small>
                                                    <small class="alert alert-primary px-2 py-1 rounded-pill generate-pdf-denied-btn">PDF</small><br>
                                                    <b>By: '.$ctr["updated_by"].'</b>
                                                </div>';
                                    break;

                                    case 'approved':
                                        $color = 'success';
                                        $text = '<i class="fa-solid fa-face-smile"></i> Success';
                                        $by = '<div class="btn text-left">
                                                    <small class="alert alert-'.$color.' px-2 py-1 rounded-pill me-1">
                                                        <i class="fa-solid fa-check"></i> '.ucwords($ctr["status"]).'
                                                    </small> 
                                                    <small class="alert alert-primary px-2 py-1 rounded-pill generate-pdf-btn">PDF</small>
                                                    <small class="alert alert-danger px-2 py-1 rounded-pill cancel-my-ctr-request">Cancel</small><br>
                                                    <b>By: '.$ctr["updated_by"].'</b>
                                                </div>';
             
                                    break;

                                    case 'cancelled':
                                        $color = 'danger';
                                        $text = '<i class="fa-solid fa-face-sad-cry"></i> Cancelled';
                                        $by = '<div class="btn text-left">
                                                    <small class="alert alert-'.$color.' px-2 py-1 rounded-pill cancel-ctr-btn me-1">
                                                        <i class="fa-solid fa-xmark"></i> '.ucwords($ctr["status"]).'
                                                    </small>
                                                     <small class="alert alert-danger px-2 py-1 rounded-pill delete-my-ctr-request">Delete</small><br>
                                                    <b>By: '.$ctr["updated_by"].'</b>
                                                </div>';
                                    break;

                                } ?>
                                <td class="d-flex align-items-center">
                                    <div class="col-2"><?= $ctr['employee'] ?></div>
                                    <div class="col-2"><?= date("M d, Y | h:iA", strtotime($ctr['date_created'])) ?></div>
                                    <div class="col-5"><?= $ctr['details'] ?></div>
                                    <div class="col-3 d-flex align-items-center">
                                        <?= $by ?>
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
<?= $this->load->view('modal/approve-change-time-request-modal') ?>
<?= $this->load->view('modal/decline-change-time-request-modal') ?>
<?= $this->load->view('modal/cancel-change-time-request-modal') ?>
<?= $this->load->view('modal/delete-change-time-request-modal') ?>
<?= $this->load->view('modal/view-denied-ct-request-modal') ?>
<?= $this->load->view('partials/modal/employee-cru-modal') ?>
<?= $this->load->view('modal/generate-request') ?>
<?= $this->load->view('partials/kendo-page-template') ?>