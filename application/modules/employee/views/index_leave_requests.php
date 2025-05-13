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
</form>

<div class="w-100 d-flex">
	<?= $this->load->view("partials/sidebar", $user_type_name) ?>
	<div class="w-100 toggled" id="main">
		<?= $this->load->view("partials/header") ?>
		<div id="main-div">
			<div class="table-search-row">
				<div class="col-sm-4"><h3 class="p-0 m-0">Leaves Requests</h3></div>
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
                            <li data-url-status="retraction" class="li-date-range dropdown-item pointer <?= $status == 'retraction' ? 'active' : '' ?>">Retraction</li>
                            <li data-url-status="retracted" class="li-date-range dropdown-item pointer <?= $status == 'retracted' ? 'active' : '' ?>">Retracted</li>
                            <li data-url-status="denied" class="li-date-range dropdown-item pointer <?= $status == 'denied' ? 'active' : '' ?>">Denied</li>
                            <li data-url-status="cancelled" class="li-date-range dropdown-item pointer <?= $status == 'cancelled' ? 'active' : '' ?>">Cancelled</li>
                        </ul>

                        <div>
                            <input id="pending-leave-list-search" type="search" class="form-control" placeholder="Search">
                        </div>
                        
                    </div>
                </div>
			</div>
			
			<div id="body-row">
				
				<table id="pending-leave-list" class="table table-striped w-100">
				    <thead>
				        <tr class="bg-lblue align-middle text-white">
				        	<th>Leaves Details</th>
				        </tr>
				    </thead>
				    <tbody>
				        <?php foreach($pending_leaves as $leave): ?>
                            <?php 
                                switch($leave['leave_type']){
                                    case 'special':
                                        $alert_color = 'warning';
                                    break;

                                    case 'birthday':
                                        $alert_color = 'info';
                                    break;

                                    case 'sick':
                                        $alert_color = 'danger';
                                    break;

                                    case 'vacation':
                                        $alert_color = 'success';
                                    break;

                                    default:
                                        $alert_color = 'info';
                                    break;
                                }    
                            ?>
                            <tr id="leave-req-tr-<?= $leave['id'] ?>">
                                <td class="d-flex align-items-center">
                                    <div class="col-2">
                                        <?= ucwords($leave['employee_name']) ?> 
                                        <?= $leave['status'] == 'retraction' ? '<br><small class="bg-warning px-2 rounded-pill"><i>For Retraction</i></small>' : '' ?>
                                    </div>
                                    <div class="col-2"><?= convert_date($leave['date']) ?></div>
                                    <div class="col-2">
                                        <small class="alert d-inline-block alert-<?= $alert_color ?> px-2 py-1 me-1 my-0 rounded-pill"> <?= ucwords($leave['leave_type']) ?> <span class="mobile-hide">Leave</span> </small>
                                        <small class="alert d-inline-block alert-info px-2 py-1 me-1 my-0 rounded-pill"><?= $leave['leave_count'] == 1 ? 'Whole-day' : 'Half-day' ?></small>
                                        <?php if($leave['salary_deduction'] == 1): ?>
											<small class="mobile-hide d-inline-block alert mb-0 alert-danger px-2 py-1 rounded-pill">W/o Pay</small>
										<?php endif ?>
                                    </div>
                                    <div class="col-4"><?= ($leave['status'] == 'retraction' || $leave['status'] == 'retraction-denied' || $leave['status'] == 'retracted') ? $leave['reason_retracted'] : ucwords($leave['details'])  ?></div>
                                    <div class="col-2" data-req-id="<?= $leave['id'] ?>" data-request-id="<?= $leave['id'] ?>" data-emp-name="<?= $name ?>">
                                        <?php if($leave['status']  == 'pending'): ?>
                                            <div class="btn bg-none text-success d-inline-block approve-leave-request-btn d-inline-block"><span class="d-none mobile-show"><i class="fa-solid fa-check"></i></span><span class="mobile-hide">Approve</span></div>
                                            <div class="btn bg-none text-danger d-inline-block decline-leave-request-btn d-inline-block"><span class="d-none mobile-show"><i class="fa-solid fa-xmark"></i></span><span class="mobile-hide">Decline</span></div>
                                        <?php elseif($leave['status']  == 'retraction'): ?>
                                            <div class="btn bg-none text-success d-inline-block approve-leave-retraction-request-btn d-inline-block"><span class="d-none mobile-show"><i class="fa-solid fa-check"></i></span><span class="mobile-hide">Approve</span></div>
                                            <div class="btn bg-none text-danger d-inline-block decline-retract-leave-request-btn d-inline-block"><span class="d-none mobile-show"><i class="fa-solid fa-xmark"></i></span><span class="mobile-hide">Decline</span></div>
                                        <?php elseif($leave['status'] == 'approved' || $leave['status'] == 'retraction-denied'): ?>
                                            <div class="btn bg-none text-left">
                                                <small class="alert alert-success px-2 py-1 rounded-pill">Approved</small>
                                                <small class="alert alert-primary px-2 py-1 rounded-pill generate-pdf-btn-leaves">PDF</small>
                                                <small class="alert alert-danger px-2 py-1 rounded-pill cancel-request-btn">Cancel</small><br>
                                                <?php if($leave['status'] == 'retraction-denied'): ?>
                                                    <small class="text-warning view-retraction-denied-msg"><i>Retraction Denied</i></small><br>
                                                <?php endif ?>
                                                <b>By: <?= $leave['updated_by'] ?></b>
                                            </div>
                                        <?php elseif($leave['status'] == 'retracted'): ?>
                                            <div class="btn bg-none text-left">
                                                <small class="alert alert-warning px-2 py-1 rounded-pill">Retracted</small>
                                                <small class="alert alert-primary px-2 py-1 rounded-pill generate-pdf-btn-retracted-leaves">PDF</small>
                                                <small class="alert alert-danger px-2 py-1 rounded-pill cancel-request-btn">Cancel</small><br>
                                                <b>By: <?= $leave['updated_by'] ?></b>
                                            </div>
                                        <?php elseif($leave['status'] == 'denied'): ?>
                                            <div class="btn bg-none text-left">
                                                <small class="alert alert-danger px-2 py-1 rounded-pill leave-request-denied-btn">Denied</small>
                                                <small class="alert alert-primary px-2 py-1 rounded-pill generate-pdf-btn-leaves-denied" >PDF</small><br>
                                                <b>By: <?= $leave['updated_by'] ?></b>
                                            </div>
                                            <?php else: ?>
                                            <div class="btn bg-none text-left">
                                                <small class="alert alert-danger px-2 py-1 rounded-pill cancel-request-cancelled-btn">Cancelled</small>
                                                <small class="alert alert-danger px-2 py-1 rounded-pill delete-request-btn">Delete</small><br>
                                                <b>By: <?= $leave['updated_by'] ?></b>
                                            </div>
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

<?= $this->load->view('modal/decline-leave-request-modal') ?>
<?= $this->load->view('modal/decline-retract-leave-request-modal') ?>
<?= $this->load->view('modal/approve-leave-request-modal') ?>
<?= $this->load->view('modal/approve-leave-retraction-request-modal') ?>
<?= $this->load->view('modal/cancel-leave-request-modal') ?>
<?= $this->load->view('modal/delete-leave-request-modal') ?>
<?= $this->load->view('modal/view-denied-leave-request-modal') ?>
<?= $this->load->view('modal/generate-request') ?>
<?= $this->load->view('partials/kendo-page-template') ?>