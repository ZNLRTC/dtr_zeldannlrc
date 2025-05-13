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
				<h3 class="p-0 m-0"><?= $first_name[0] ?>'s DTR</h3>
				<div class="d-flex align-items-center w500-100 justify-content-end">
					<!--button class="btn btn-maroon download-dtr-list" data-toggle="modal" data-target="#download-dtr-list-filter-modal" title="Download DTR List Report"><i class="fa fa-file-pdf"></i></button-->
					<!-- <button class="btn btn-green download-dtr-list" data-toggle="modal" data-target="#download-dtr-list-filter-modal" title="Download DTR List Report" disabled><i class="fa fa-file-excel"></i></button> -->
                    <?php if($employee_info['dtr_month']): ?>
						<button type="button" class="btn btn-sblue dropdown-toggle py-2 d-none d-sm-block" data-bs-toggle="dropdown" aria-expanded="false">
							Select Date Range
						</button>
					<?php endif ?>
                    <ul class="dropdown-menu">
                        <?php foreach($employee_info['dtr_month'] as $month): ?>
                            <?php 
                                $get_date = explode(' ', $month);	
                            ?>
                            <li><a class="dropdown-item <?= ($employee_info['month_year'][0] == $get_date[0] && $employee_info['month_year'][1] == $get_date[2]) ? 'active' : ')' ?>" href="<?= base_url() . 'admin/employee_dtr?user_id='.$employee_info['id'].'&month=' . $get_date[0] . '&year=' . $get_date[2]  ?>"><?= $get_date[1] .' '. $get_date[2] ?></a></li>
                        <?php endforeach ?>
                    </ul>
                    &nbsp;
					<input type="text" class="search-bar" placeholder="&#xF002;" id="dtr-list-search">
				</div>
			</div>
			<div id="body-row">
				<table id="employee-dtr-list" class="table table-striped w-100">
				    <thead>
				        <tr class="bg-lblue align-middle text-white">
				            <th>Date</th>
				            <th>Schedule</th>
				            <th>Time-in - Time-out</th>
				            <th class="w500-hide">Break Time</th>
				            <th class="w500-hide">EOD Report</th>
				            <th class="w500-hide">Work</th>
				            <th class="tablet-hide">Overtime</th>
				        </tr>
				    </thead>
				    <tbody>
                        <?php foreach($employee_info['dtrs'] as $dtr): ?>
							<?php 

								$get_in = date('H:i:s', strtotime($dtr['time_in']));
                                $get_out = date('H:i:s', strtotime($dtr['time_out']));
								
								if($dtr['schedule_time'] != NULL){
									$daily_schedule = explode('-', $dtr['schedule_time']);
									$d_sched_in = date('h:i A', strtotime($daily_schedule[0]));
									$d_sched_out = date('h:i A', strtotime($daily_schedule[1]));
									$d_sched = $d_sched_in .' - '.$d_sched_out;
								}else{
									$daily_schedule = explode('-', $employee_info['schedule']);
									$d_sched_in = date('h:i A', strtotime($daily_schedule[1]));
									$d_sched_out = date('h:i A', strtotime($daily_schedule[2]));
									$d_sched = $d_sched_in .' - '.$d_sched_out;
								}
								

								if($dtr['break'] != ""){
									$break = explode('-', $dtr['break']);
									$break_in = date('h:i A', strtotime($break[0]));
									
									if(count($break) > 1){
										$break_out = date('h:i A', strtotime($break[1]));
										$total_break = $break_in .' - '. $break_out . ' <br><small><b>' . compute_time_difference($break[0], $break[1]) . '</b></small>';
									}else{
										$total_break = $break_in .' - <b><i>On going</i></b>';
									}
									
								}else{
									$total_break = $dtr['break'];
								}

								$leave_checker = $this->request_leave_model->get_one_by_where(['user_id' => $dtr['user_id'], 'date' => $dtr['date'], 'status' => 'approved', 'leave_count' => '0.5']);

								if($dtr['time_out'] != ""){
									if($dtr['break'] != null){
										$get_break = explode('-', $dtr['break']);
										$get_exp_break_in = date('H:i:s', strtotime($get_break[0]));
                                    	$get_exp_break_out = date('H:i:s', strtotime($get_break[1]));
									}else{
										$get_break = [NULL, NULL];
										$get_exp_break_in = NULL;
										$get_exp_break_out = NULL;
									}

                                    $schedule = explode('-', $dtr['schedule_time']);
                                    $get_schedule_in = date('H:i:s', strtotime($schedule[0]));
                                    $get_schedule_out = date('H:i:s', strtotime($schedule[1]));
									$sched_in = new DateTime($get_schedule_in);
									$sched_out = new DateTime($get_schedule_out);
									$get_schedule_hours = $sched_in->diff($sched_out);
									$schedule_minutes = $get_schedule_hours->h * 60;

                                    $compute_hour_total = compute_total_worked_hours($dtr['date'], $get_in, $get_out, $get_exp_break_in, $get_exp_break_out, $get_schedule_in, $get_schedule_out, $dtr['schedule_workbase'], $schedule_minutes);
									$total_hour = '<b><i>' . compute_time_difference($dtr['time_in'], $dtr['time_out'], $get_break[0], $get_break[1]) . '</i></b><br>';
									$time_out = date('h:i A', strtotime($dtr['time_out']));

									if($compute_hour_total['total_over_time'] == 0 && $compute_hour_total['total_under_time'] != '0'){
                                        $ut_ot_class = 'text-danger';
                                        $ut_ot_text = '-'.$compute_hour_total['total_under_time'];
                                    }elseif($compute_hour_total['total_over_time'] != '0' && $compute_hour_total['total_under_time'] == 0){
                                        $ut_ot_class = 'text-success';
                                        $ut_ot_text = '+'.$compute_hour_total['total_over_time'];
                                    }elseif($compute_hour_total['total_over_time'] == '0' && $compute_hour_total['total_under_time'] == '0'){
                                        $ut_ot_class = 'text-success';
                                        $ut_ot_text = '0:0';
                                    }else{
                                        $ut_ot_class = 'text-info';
                                        $ut_ot_text = 'Invalid';
                                    }

                                    $compute_hour_total = '<div>
                                                                <span class="t-12px">Total: '.$compute_hour_total["total_renderred_time"].'</span>
                                                                <b class="t-12px '.$ut_ot_class.'"> '.$ut_ot_text.'mins </b>
                                                            <div>';
								}else{
									$total_hour = "";
									$time_out = "<b><i>On going</i></b>";
									$compute_hour_total = 'On going';
								}

								if($dtr['overtime']){
									$ot = explode('-', $dtr['overtime']);
									$ot_hour = date('h:i A', strtotime($ot[0])) .' - '. date('h:i A', strtotime($ot[1]));
								}else{
									$ot_hour = '<span class="opacity-25">No Overtime</span>';
								}

								if($dtr['time_in_work_base'] != NULL || $dtr['break_out_work_base'] != NULL){
									if($dtr['time_in_work_base'] == $dtr['break_out_work_base']){
										$workbase = $dtr['time_in_work_base'];
									}else{
										$workbase = $dtr['time_in_work_base'] .'/'. $dtr['break_out_work_base'];
									}
									
								}else{
									$workbase = $dtr['work_base'];
								}
								
							?>
                            <tr id="<?= $dtr['id'] ?>">
                                <td class="date"><?= date("M d, Y (D)", strtotime($dtr['date'])) ?></td>
                                <td class="w500-hdie"><?= $d_sched ?></td>
                                <td><?= date('h:i A', strtotime($dtr['time_in']))  ." - ". $time_out . " <br class='d-none mobile-show'><br>" . $compute_hour_total ?></td>
                                <td class="w500-hide"><?= $total_break ?></td>
                                <td class="eod-report-view-btn truncate-row pointer w500-hide" data-user-id="<?= $employee_info['id'] ?>" data-eod-date="<?= $dtr['date'] ?>"><?= $dtr['end_of_day'] ?></td>
                                <td class="w500-hide"><?= $workbase ?></td>
                                <td class="w500-hide"><?= $ot_hour ?></td>
                            </tr>
                        <?php endforeach ?>
				    </tbody>
				</table>
			</div>
		</div>
	</div>
</div>

<?= $this->load->view('../../employee/views/modal/eod-report-modal') ?>

