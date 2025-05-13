<?php

/*

 * Page Name: Active DTR

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

				<h3 class="p-0 m-0">Active DTR</h3>

				<div>

					<input type="search" class="search-bar" placeholder="&#xF002;" id="adtr-list-search">

				</div>

			</div>

			<div id="body-row">

				<table id="adtr-list" class="table table-striped w-100">

				    <thead>

				        <tr class="bg-lblue align-middle text-white">

				            <th>Name</th>

				            <th class="mobile-hide">Date</th>

							<th class="mobile-hide">Schedule</th>

				            <th title="Sort by time-in" class="mobile-hide">Time-in</th>

							<th class="mobile-hide">Break-in</th>

				            <th class="mobile-hide">Break-out</th>

				            <th></th>

				        </tr>

				    </thead>

				    <tbody>

				        <?php 

				            foreach($dtrs as $dtr):

				            	$shift = ($dtr['shift_reason'])?$dtr['shift_reason']:"no";
								$schedule = explode('-', $dtr['current_schedule']['in-out']);
								$current_sched = date('h:i a', strtotime($schedule[0])) .' - '. date('h:i a', strtotime($schedule[1])) .' | '. ucwords($dtr['current_schedule']['workbase']);
								$time_in_work_base = $dtr['time_in_work_base'] != NULL ? '('. $dtr['time_in_work_base'] .')' : '<span class="opacity-25">No Record</span>';
								
								$break = explode('-', $dtr['break']);
								$break_in = ($dtr['break'] != NULL && isset($break[0])) ? date('h:i a', strtotime($break[0])) : '<span class="opacity-25">No Record</span>';
								$bo_work_base = ($dtr['break_out_work_base'] != NULL) ? '('.$dtr['break_out_work_base'].')' : '<span class="opacity-25">No Record</span>';
								$break_out = ($dtr['break'] != NULL && isset($break[1])) ? date('h:i a', strtotime($break[1])) . ' ' . $bo_work_base : '<span class="opacity-25">No Record</span>';
								$bg = '';
								
								if($dtr['current_schedule']['workbase'] != 'WFH/Office'){
									if($dtr['current_schedule']['workbase'] != $dtr['time_in_work_base']){
										$bg = 'bg-lred';
									}
									if($dtr['break_out_work_base'] != ''){
										if($dtr['current_schedule']['workbase'] != $dtr['break_out_work_base']){
											$bg = 'bg-lred';
										}
									}
									
								}
								
								// echo '<pre>';
								// print_r($break);
								// exit;

			                    echo '

			                        <tr tr-id="'.$dtr['id'].'" class="'.$bg.'">

			                            <td class="date">'.$dtr['name'].' <br><small><b>'.date( "M d, Y (D)",strtotime( $dtr['date'])).' | '.date( "h:i a",strtotime( $dtr['time_in'])).' | '.$dtr['work_base'].'</b></small></td>

			                            <td class="date mobile-hide">'.date( "M d, Y (D)",strtotime( $dtr['date'])).'</td>

										<td class="mobile-hide">'.$current_sched.'</td>

			                            <td class="time-in mobile-hide">'.date( "h:i a", strtotime( $dtr['time_in'])).' '. $time_in_work_base. '</td>

										<td class="shift mobile-hide">'.$break_in.'</td>

			                            <td class="work-base mobile-hide">'.$break_out.'</td>

			                            <td class="action-column">

			                            	<button class="btn cancel" dtr-id="'.$dtr['id'].'" title="cancel" data-toggle="modal" data-target="#cancel-ongoing-dtr-modal"><i class="fas fa-times-circle"></i> <span class="mobile-hide">Cancel</span></button>

			                            </td>

			                        </tr>

			                    ';

				            endforeach;

				        ?>

				    </tbody>

				</table>

			</div>

		</div>

	</div>

</div>


<?= $this->load->view('modal/ot-status-modal'); ?>

