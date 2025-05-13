<?php
    $user_type_name = $this->users_type_model->get_one_by_where(['id'=>$user_type]);
    $user_type_name['user_id'] = $id;
?>

<div class="w-100 d-flex">
	<?= $this->load->view("partials/sidebar", $user_type_name) ?>
	<div class="w-100 toggled" id="main">
		<?= $this->load->view("partials/header") ?>
		<div id="main-div">
			<div class="table-search-row">
				<div class="col-lg-6 col-md-12 col-sm-6 d-flex justify-content-between">
					<h3 class="p-0 m-0 me-5"><?= $employee_info['name'] ?>'s DTR</h3>
				</div>
                <div class="col-lg-6 col-md-12 col-sm-6 d-flex justify-content-end">
                    <?php if($employee_info['dtr_month']): ?>
						<button type="button" class="btn btn-sblue dropdown-toggle py-2 me-1 d-none d-sm-block" data-bs-toggle="dropdown" aria-expanded="false">
							Select Date Range
						</button>
					<?php endif ?>
                    <ul class="dropdown-menu me-1">
                        <?php foreach($employee_info['dtr_month'] as $month): ?>
                            <?php 
                                $get_date = explode(' ', $month);	
                            ?>
                            <li><a class="dropdown-item <?= ($employee_info['month_year'][0] == $get_date[0] && $employee_info['month_year'][1] == $get_date[2]) ? 'active' : ')' ?>" href="<?= base_url() . 'employee/view_emp?i='.$employee_info['id'].'&m=' . $get_date[0] . '&y=' . $get_date[2]  ?>"><?= $get_date[1] .' '. $get_date[2] ?></a></li>
                        <?php endforeach ?>
                    </ul>
                    <div>
					    <input id="active-dtr-table-search" class="form-control" type="search" placeholder="Search">
                    </div>
				</div>
			</div>
            <div class="px-3">
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

                                if($dtr['time_out'] != null){
                                    if($dtr['break'] != null){
                                        $get_break = explode('-', $dtr['break']);
                                        $get_exp_break_in = date('H:i:s', strtotime($get_break[0]));
                                        $get_exp_break_out = date('H:i:s', strtotime($get_break[1]));
                                    }else{
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
                                    $leave_checker = $this->request_leave_model->get_one_by_where(['user_id' => $dtr['user_id'], 'date' => $dtr['date'], 'status' => 'approved', 'leave_count' => '0.5']);

                                    $compute_hour_total = compute_total_worked_hours($dtr['date'], $get_in, $get_out, $get_exp_break_in, $get_exp_break_out, $get_schedule_in, $get_schedule_out, $dtr['schedule_workbase'], $schedule_minutes);

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

                                    $compute_hour_total = '<div>
                                                                <span class="t-12px">Total: '.$compute_hour_total["total_renderred_time"].'</span>
                                                                <b class="t-12px '.$ut_ot_class.'"> '.$ut_ot_text.'mins </b>
                                                            <div>';
                                }else{
                                    $compute_hour_total = 'On going';
                                }
                                
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
                                

                                if($dtr['break'] != "" || $dtr['break'] != null){
                                    $break = explode('-', $dtr['break']);
                                    $break_in = date('h:i A', strtotime($break[0]));
                                    
                                    if(count($break) > 1){
                                        $break_out = date('h:i A', strtotime($break[1]));
                                        $total_break = $break_in .' - '. $break_out . ' <br><small><b>' . compute_time_difference($break[0], $break[1]) . '</b></small>';
                                    }else{
                                        $total_break = $break_in .' - <b><i>On going</i></b>';
                                    }
                                    
                                }else{
                                    $total_break = '<span class="opacity-25">No Record</span>';
                                }

                                if($dtr['time_out'] != ""){
                                    if($dtr['break'] != NULL){
                                        $break = explode('-', $dtr['break']);
                                    }else{
                                        $break = ['12:00', '12:00'];
                                    }
                                    
                                    $total_hour = '<br><small>' . compute_all_time_difference($dtr['time_in'], $dtr['time_out'], $break[0], $break[1]) . '<small>';
                                    $time_out = date('h:i A', strtotime($dtr['time_out']));
                                }else{
                                    $total_hour = "";
                                    $time_out = "<b><i>On going</i></b>";
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
                                <td class="w500-hdie"><?= $d_sched .' | <b>'. $dtr['schedule_workbase'] . '</b> ' ?></td>
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