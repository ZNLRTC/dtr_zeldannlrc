<?php
/*
 * Page Name: Employee
 * Author: Jushua FF
 * Date: 09.11.2022
 */
$user_type_name = $this->users_type_model->get_one_by_where(['id'=>$user_type]);
$user_type_name['user_id'] = $id;
$user_name = explode(' ', $name);
$current_day_dtr = false;

if($current_dtr == ""){
	$action = 'time-in';
	$text = 'Time-In';
}else{
	if($schedule_minutes > 280){
		if($current_dtr['break'] == ""){
			$action = 'break-in';
			$text = 'Break-In';
		}else{
			$break = explode('-', $current_dtr['break']);
			if(count($break) == 1){
				$action = 'break-out';
				$text = 'Break-Out';
			}else{
				$action = 'eod-report';
				$text = 'EOD Report';
			}
		}
	}else{
		$action = 'eod-report';
		$text = 'EOD Report';
	}
	
}

$schedule = explode('-', $current_schedule['in-out']);



?>

<form class="d-none">
	<input type="hidden" value="<?=  $current_schedule['in-out']?>" name="sched-in-out">
	<input type="hidden" value="<?=  $current_schedule['workbase']?>" name="sched-workbase">
</form>
<div class="w-100 d-flex">
	<?= $this->load->view("partials/sidebar", $user_type_name) ?>
	<div class="w-100 toggled" id="main">
		<?= $this->load->view("partials/header") ?>
		<div id="main-div">
			<div class="table-search-row">
				<div class="col-lg-6 col-md-12 col-sm-6 d-flex justify-content-between">
					<div>
						<h3 class="p-0 m-0 me-5"><?= $user_name[0] ?>'s DTR</h3>
						<b><?= date('h:i a', strtotime($schedule[0])) .' - '. date('h:i a', strtotime($schedule[1])) .' | '. $current_schedule['workbase'] ?></b>
					</div>
					<div class="d-flex align-items-center">
						<h3 class="m-0 p-0 dtr-stop-watch mobile-hide">--:--:--</h3>
					</div>
				</div>
				
				<div class="d-none d-sm-block col-lg-6 col-md-12 col-sm-6">
					<div class="d-flex align-items-center w500-100 justify-content-end">
						<div class="btn-group">
							<div id="workbase-options" class="dtr-workbase-container me-3">
								<div class="form-check me-1 m-auto <?= !empty($current_dtr) ? ($action == 'break-in' || $action == 'eod-report') ? 'd-none' : '' : '' ?>">
									<input class="form-check-input" type="radio" value="WFH" id="checkbox-home" name="dtr-work-base" <?= $current_schedule['workbase'] == 'WFH' ? 'checked' : '' ?>>
									<label class="form-check-label pointer" for="checkbox-home">WFH</label>
								</div>

								<div class="form-check m-auto <?= !empty($current_dtr) ? ($action == 'break-in' || $action == 'eod-report') ? 'd-none' : '' : '' ?>">
									<input class="form-check-input" type="radio" value="Office" id="checkbox-office" name="dtr-work-base" <?= $current_schedule['workbase'] == 'Office' ? 'checked' : '' ?>>
									<label class="form-check-label pointer" for="checkbox-office">Office</label>
								</div>
							</div>

							<?php if(!$leave_checker):?>
								<button class="btn btn-success dtr-time-btn" data-action="<?= $action ?>" data-user-id="<?= $id ?>" data-dtr-id="<?= $current_dtr != "" ? $current_dtr['id'] : '0' ?>" value="<?= !empty($current_dtr) ? $current_dtr['time_in'] : '0' ?>"><?= $text ?></button> &nbsp;
							<?php endif ?>

							<button type="button" class="btn btn-sblue dropdown-toggle py-2 d-none d-sm-block" data-bs-toggle="dropdown" aria-expanded="false">
								Date
							</button>
							<ul class="dropdown-menu">
								<?php foreach($dtr_month as $month): ?>
									<?php 
										$get_date = explode(' ', $month);	
									?>
									<li><a class="dropdown-item <?= ($month_year[0] == $get_date[0] && $month_year[1] == $get_date[2]) ? 'active' : ')' ?>" href="<?= base_url() . 'employee?month=' . $get_date[0] . '&year=' . $get_date[2]  ?>"><?= $get_date[1] .' '. $get_date[2] ?></a></li>
								<?php endforeach ?>
							</ul>
						</div> &nbsp;

						<button class="btn btn-maroon download-dtr-list d-none" id="download-one-employee-dtr" data-user-id="<?= $id ?>" data-toggle="modal" data-target="#download-my-dtr-list-filter-modal" title="Download DTR List Report" disabled><i class="fa fa-file-pdf"></i></button>
						<input type="search" class="search-bar form-control m-0" placeholder="&#xF002;" id="my-dtr-list-search">
					</div>
				</div>

				<div class="col-sm-6 d-block d-sm-none">
					<div class="d-flex justify-content-end">
						<div class="d-flex align-items-center dtr-workbase-container me-2">
							<div class="form-check me-1 m-auto <?= !empty($current_dtr) ? ($action == 'break-in' || $action == 'eod-report') ? 'd-none' : '' : '' ?>">
								<input class="form-check-input" type="radio" value="WFH" id="checkbox-home-mobile" name="dtr-work-base-mobile" <?= $current_schedule['workbase'] == 'WFH' ? 'checked' : '' ?>>
								<label class="form-check-label pointer fs-6" for="checkbox-home-mobile">WFH</label>
							</div>

							<div class="form-check me-1 m-auto <?= !empty($current_dtr) ? ($action == 'break-in' || $action == 'eod-report') ? 'd-none' : '' : '' ?>">
								<input class="form-check-input" type="radio" value="Office" id="checkbox-office-mobile" name="dtr-work-base-mobile" <?= $current_schedule['workbase'] == 'Office' ? 'checked' : '' ?>>
								<label class="form-check-label pointer fs-6" for="checkbox-office-mobile">Office</label>
							</div>
						</div>
	
						<button type="button" class="btn border border-warning text-warning fs-6 dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false"><i class="fa-solid fa-arrows-left-right-to-line"></i> Range</button>
						<ul class="dropdown-menu">
							<?php foreach($dtr_month as $month): ?>
								<?php 
									$get_date = explode(' ', $month);	
								?>
								<li><a class="dropdown-item <?= ($month_year[0] == $get_date[0] && $month_year[1] == $get_date[2]) ? 'active' : ')' ?>" href="<?= base_url() . 'employee?month=' . $get_date[0] . '&year=' . $get_date[2]  ?>"><?= $get_date[1] .' '. $get_date[2] ?></a></li>
							<?php endforeach ?>
						</ul>
					</div>
				</div>
			</div>
			
			<div id="body-row">
				<div class="d-flex mx-2 justify-content-end">
					<div class="w-100">
						<button class="btn btn-success dtr-time-btn-mobile w-100 mb-3 d-none mobile-show border-0" data-action="<?= $action ?>" data-user-id="<?= $id ?>" data-dtr-id="<?= $current_dtr != "" ? $current_dtr['id'] : '0' ?>" value="<?= !empty($current_dtr) ? $current_dtr['time_in'] : '0' ?>"><?= $text ?></button>
					</div>
				</div>
				<span class="dtr-stop-watch fs-6 d-block mb-3 text-center d-none mobile-show">--:--:--</span>
				<table id="my-dtr-list" class="table table-striped w-100">
				    <thead>
				        <tr>
				        	<th><i class="fas fa-calendar-alt"></i> Date</th>
				        	<th class="w500-hide">Schedule</th>
				            <th><i class="fas fa-clock"></i> Time-log</th>
				            <th class="w500-hide">Break</th>
				            <th class="w500-hide eod-column-head">EOD Report</th>
				            <th class="tablet-hide"><i class="fas fa-chair"></i> Work</th>
							<th>Action</th>
							<th></th>
				        </tr>
				    </thead>
				    <tbody>
				        <?php foreach($dtr_lists as $dtr_list): ?>
							<?php 
								$date = date( "Y-m-d",strtotime( $dtr_list['date']));
								$array_date = explode('-', $date);
								$holiday = $this->holidays_model->get_one_by_where(['date' => $dtr_list['date']]);
								$ti_workbase = $dtr_list['time_in_work_base'];
								$bo_workbase = $dtr_list['break_out_work_base'];
								

								if($ti_workbase != NULL && $bo_workbase != NULL){
									$daily_workbase = $ti_workbase == $bo_workbase ? $ti_workbase : $ti_workbase.'/'.$bo_workbase;
								}else{
									$daily_workbase = $dtr_list['work_base'];
								}

								$schedule = explode('-', $dtr_list['schedule_time']);
								$get_in = date('H:i:s', strtotime($dtr_list['time_in']));
								$get_out = date('H:i:s', strtotime($dtr_list['time_out']));

								if($dtr_list['time_out'] != NULL):
									if($dtr_list['break'] != null){
										$get_break = explode('-', $dtr_list['break']);
										$get_exp_break_in = date('H:i:s', strtotime($get_break[0]));
										$get_exp_break_out = date('H:i:s', strtotime($get_break[1]));
									}else{
										$get_exp_break_in = NULL;
										$get_exp_break_out = NULL;
									}
									
									$get_schedule_in = date('H:i:s', strtotime($schedule[0]));
									$get_schedule_out = date('H:i:s', strtotime($schedule[1]));
									$sched_in = new DateTime($get_schedule_in);
									$sched_out = new DateTime($get_schedule_out);
									$get_schedule_hours = $sched_in->diff($sched_out);
									$schedule_minutes = $get_schedule_hours->h * 60;
									$leave_checker = $this->request_leave_model->get_one_by_where(['user_id' => $dtr_list['user_id'], 'date' => $dtr_list['date'], 'status' => 'approved', 'leave_count' => '0.5']);

									$compute_hour_total = compute_total_worked_hours($dtr_list['date'], $get_in, $get_out, $get_exp_break_in, $get_exp_break_out, $get_schedule_in, $get_schedule_out, $dtr_list['schedule_workbase'], $schedule_minutes);
									$c2 = date( "h:i a",strtotime( $dtr_list['time_out']));

									if($compute_hour_total['total_over_time'] == 0 && $compute_hour_total['total_under_time'] != '0:0'){
										$ut_ot_class = 'text-danger';
										$ut_ot_text = '-'.$compute_hour_total['total_under_time'];
									}elseif($compute_hour_total['total_over_time'] != '0:0' && $compute_hour_total['total_under_time'] == 0){
										$ut_ot_class = 'text-success';
										$ut_ot_text = '+'.$compute_hour_total['total_over_time'];
									}elseif($compute_hour_total['total_over_time'] == '0:0' && $compute_hour_total['total_under_time'] == '0:0'){
										$ut_ot_class = 'text-success';
										$ut_ot_text = '0:0';
									}else{
										$ut_ot_class = 'text-info';
										$ut_ot_text = 'Invalid';
									}
									
									$c2_1 = '<div>
												<span class="t-12px">Total: '.$compute_hour_total["total_renderred_time"].'</span>
												<b class="t-12px '.$ut_ot_class.'"> '.$ut_ot_text.'mins </b>
											<div>';
								else:
									$c2_1 = '';
									$c2 = '<b><i>On going</i></b>';
								endif;

								if($dtr_list['break'] !== "" && $dtr_list['break'] !== NULL):
									$break = explode("-",$dtr_list['break']);
									if( count($break) == 2 ){
										$break = date( "h:i a",strtotime( $break[0])).' - '.date( "h:i a",strtotime($break[1]));
									}else{
										$break = date('h:i a', strtotime($dtr_list['break']));
									}
									
								else:
									$break = "<span class='opacity-25'>No Record</span>";
								endif;
							?>

							<tr tr-id="<?= $dtr_list['id'] ?>" class="<?= !$dtr_list['time_out'] ? 'active' : ($holiday ? 'bg-lgreen' : '') ?>">
								<td class="date lh-12px fw-bold"> 
									<?= date( "M d, Y (D)", strtotime( $dtr_list['date'])) ?> <br> <?= $holiday ? '<i class="t-12px">'.$holiday['name'].'</i>' : '' ?>
								</td>
								<td class="w500-hide"><?= date('h:i a', strtotime($schedule[0])) ." - ". date('h:i a', strtotime($schedule[1])) ?></td>
								<td class="time lh-12px"> 
									<?= date( "h:i a",strtotime( $dtr_list['time_in'])).' - '. $c2 .'<br>'. $c2_1?> 
								</td>
								<td class="break w500-hide"><?= $break ?></td>
								<td class="eod w500-hide truncate-row pointer eod-report-view-btn" data-user-id="<?= $dtr_list['user_id'] ?>" data-eod-date="<?= $dtr_list["date"] ?>">
									<?= $dtr_list['end_of_day'] ?>
								</td>
								<td class="work lh-12px tablet-hide"><?= $daily_workbase ?></td>
								<td><?= $holiday ? '<div class="t-12px pointer move-to-holiday-btn" data-dtr-id="'.$dtr_list["id"].'"><i class="fas fa-long-arrow-alt-right"></i> Move to holiday</div>' : '' ?></td>
								<td><?= date('Y-m-d', strtotime($dtr_list['date_created'])) ?></td>
							</tr>
								
				        <?php endforeach ?>
				    </tbody>
				</table>
			</div>
		</div>
	</div>
</div>

<?= $this->load->view('modal/download-my-dtr-list-filter-modal') ?>
<?= $this->load->view('modal/request-dtr-update-modal') ?>
<?= $this->load->view('modal/eod-report-modal') ?>
<?= $this->load->view('modal/eod-ot-report-modal') ?>
<?= $this->load->view('modal/write-eod-report-modal') ?>
<?= $this->load->view('modal/time-in-error-modal') ?>
<?= $this->load->view('modal/dtr-request-result-modal') ?>
<?= $this->load->view('modal/overtime-request-modal') ?>
<?= $this->load->view('modal/view-denied-ot-req-modal') ?>
<?= $this->load->view('modal/hybrid-schedule-no-workbase-modal') ?>
<?= $this->load->view('modal/less-than-minimum-break-time-modal') ?>
<?= $this->load->view('modal/move-dtr-to-ot-modal') ?>