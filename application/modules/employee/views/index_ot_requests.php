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
				<div class="col-sm-4"><h3 class="p-0 m-0">Overtimes for approval</h3></div>
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
                            <input id="ot-leave-list-search" type="search" class="form-control" placeholder="Search">
                        </div>
                    </div>
                </div>
			</div>
			
			<div id="body-row">
				
				<table id="ot-leave-list" class="table table-striped w-100">
				    <thead>
				        <tr class="bg-lblue align-middle text-white">
				        	<th>Leaves Details</th>
				        </tr>
				    </thead>
				    <tbody>
				        <?php foreach($requests as $req): ?>
                            <?php 
                                switch($req['type']){
                                    case 'holiday':
                                        $alert_color = 'info';
                                    break;

                                    case 'regular':
                                        $alert_color = 'danger';
                                    break;

                                    default:
                                        $alert_color = 'danger';
                                    break;
                                }    

                                //$ot_time = explode('-', $req['time']);
                            ?>
                            <tr id="ot-request-tr-<?= $req['id'] ?>">
                                <td class="d-flex align-items-center">
                                    <div class="col-2"><?= $req['name'] ?></div>
                                    <div class="col-4"><?= convert_date($req['date']) .' | '. date('h:i A', strtotime($req['time_in'])) .' - '. date('h:i A', strtotime($req['time_out'])) ?> | <b class="text-<?= $alert_color ?>">  <?= ucwords($req['type']) ?> OT </b></div>
                                    <div class="col-4">
                                        <?= $req['eod'] ?>
                                    </div>
                                    <div class="col-2" data-req-id="<?= $req['req_id'] ?>" data-emp-name="<?= $req['name'] ?>">
                                        <?php if($req['status'] == 'pending'): ?>
                                            <div class="btn bg-none text-success d-inline-block approve-ot-request-btn d-inline-block"><span class="d-none mobile-show"><i class="fa-solid fa-check"></i></span><span class="mobile-hide">Approve</span></div>
                                            <div class="btn bg-none text-danger d-inline-block decline-ot-request-btn d-inline-block"><span class="d-none mobile-show"><i class="fa-solid fa-xmark"></i></span><span class="mobile-hide">Decline</span></div>
                                        <?php elseif($req['status'] == 'approved'): ?>
                                            <div class="btn bg-none text-left">
                                                <small class="alert d-inline-block alert-success px-2 py-1 rounded-pill d-none mobile-show mb-0">
                                                    <i class="fa-solid fa-check"></i>
                                                </small> 
                                                <small class="alert d-inline-block alert-success px-2 py-1 rounded-pill mobile-hide mb-0">Approved</small>
                                                <small class="alert d-inline-block alert-primary px-2 py-1 rounded-pill mobile-hide generate-pdf-btn mb-0">PDF</small>
                                                <small class="alert d-inline-block alert-danger px-2 py-1 rounded-pill mobile-hide cancel-ot-request-btn mb-0">Cancel</small><br>
                                                <small class="mobile-hide"><b>By: <?= $req['updated_ni'] ?></b></small>
                                            </div>
                                        <?php elseif($req['status'] == 'denied'): ?>
                                            <div class="btn bg-none text-left">
                                                <small class="alert d-inline-block alert-danger px-2 py-1 rounded-pill d-none mobile-show">
                                                    <i class="fa-solid fa-xmark"></i>
                                                </small>
                                                <small class="alert d-inline-block alert-danger px-2 py-1 rounded-pill mobile-hide denied-otreq-btn">
                                                    <i class="fa-solid fa-xmark"></i> Denied
                                                </small>
                                                <small class="alert d-inline-block alert-primary px-2 py-1 rounded-pill mobile-hide denied-generate-pdf-btn">
                                                    PDF
                                                </small><br>
                                                <small class="mobile-hide"><b>By: <?= $req['updated_ni'] ?></b></small>
                                            </div>
                                        <?php elseif($req['status'] == 'cancelled'): ?>
                                            <div class="btn bg-none text-left">
                                                <small class="alert d-inline-block alert-danger px-2 py-1 rounded-pill d-none mobile-show">
                                                    <i class="fa-solid fa-xmark"></i>
                                                </small>
                                                <small class="alert d-inline-block alert-danger px-2 py-1 rounded-pill mobile-hide cancel-ot-request-btn">
                                                    <i class="fa-solid fa-xmark"></i> Cancelled
                                                </small>
                                                </small>
                                                <small class="alert d-inline-block alert-danger px-2 py-1 rounded-pill mobile-hide delete-ot-request-btn">
                                                    Delete
                                                </small><br>
                                                <small class="mobile-hide"><b>By: <?= $req['updated_ni'] ?></b></small>
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

<?= $this->load->view('modal/decline-ot-request-modal') ?>
<?= $this->load->view('modal/approve-ot-request-modal') ?>
<?= $this->load->view('modal/cancel-ot-request-modal') ?>
<?= $this->load->view('modal/delete-ot-request-modal') ?>
<?= $this->load->view('modal/view-denied-ot-req-modal') ?>
<?= $this->load->view('modal/generate-request') ?>
<?= $this->load->view('partials/kendo-page-template') ?>