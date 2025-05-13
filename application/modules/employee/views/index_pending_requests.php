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
				<div class="col-sm-4"><h3 class="p-0 m-0">Leaves for approval</h3></div>
                <div class="col-sm-8 row">
                    <div class="col-sm-4 offset-sm-8">
                        <input type="search" class="form-control" placeholder="Search">
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
                            <tr>
                                <td class="d-flex align-items-center">
                                    <div class="col-2"><?= $leave['user_info']['name'] ?></div>
                                    <div class="col-2"><?= convert_date($leave['date']) ?></div>
                                    <div class="col-2"><small class="alert alert-<?= $alert_color ?> px-2 py-1 rounded-pill"> <?= ucwords($leave['leave_type']) ?> <span class="mobile-hide">Leave</span> </small></div>
                                    <div class="col-4"><?= $leave['details'] ?></div>
                                    <div class="col-2" data-req-id="<?= $leave['id'] ?>" data-emp-name="<?= $leave['user_info']['name'] ?>">
                                        <div class="btn bg-none text-success d-inline-block approve-leave-request-btn d-inline-block"><span class="d-none mobile-show"><i class="fa-solid fa-check"></i></span><span class="mobile-hide">Approve</span></div>
                                        <div class="btn bg-none text-danger d-inline-block decline-leave-request-btn d-inline-block"><span class="d-none mobile-show"><i class="fa-solid fa-xmark"></i></span><span class="mobile-hide">Decline</span></div>
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
<?= $this->load->view('modal/approve-leave-request-modal') ?>