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
				<h3 class="p-0 m-0">DTR List</h3>
				<div class="d-flex align-items-center">
					<!--button class="btn btn-maroon download-dtr-list" data-toggle="modal" data-target="#download-dtr-list-filter-modal" title="Download DTR List Report"><i class="fa fa-file-pdf"></i></button-->
					<button class="btn btn-green download-dtr-list" data-toggle="modal" data-target="#download-dtr-list-filter-modal" title="Download DTR List Report"><i class="fa fa-file-excel"></i></button>
					
					<div class="btn-group pe-1">
						<button type="button" class="btn btn-sblue dropdown-toggle py-2 " data-bs-toggle="dropdown" aria-expanded="false">
						<i class="fa-solid fa-arrows-left-right-to-line"></i> <span class="mobile-hide">Select Date Range</span>
						</button>
						<ul class="dropdown-menu">
							<?php foreach($dtr_month as $month): ?>
								<?php 
									$get_date = explode(' ', $month);	
								?>
								<li><a class="dropdown-item <?= ($month_year[0] == $get_date[0] && $month_year[1] == $get_date[2]) ? 'active' : ')' ?>" href="<?= base_url() . 'admin/index_dtr_list?month=' . $get_date[0] . '&year=' . $get_date[2]  ?>"><?= $get_date[1] .' '. $get_date[2] ?></a></li>
							<?php endforeach ?>
						</ul>
					</div>
					
					<input type="text" class="search-bar mobile-hide" placeholder="&#xF002;" id="dtr-list-search">
				</div>
				<input type="text" class="search-bar" placeholder="&#xF002;" id="dtr-list-search-0">
			</div>
			<div id="body-row">
				<table id="dtr-list" class="table table-striped w-100">
				    <thead>
				        <tr class="bg-lblue align-middle text-white">
				            <th title="Sort by name">Name</th>
				            <th title="Sort by date" class="w500-hide">Date</th>
				            <th title="Sort by day time-in and time-out" class="w500-hide">Time-in - Time-out</th>
				            <th title="Sort by break-time" class="w500-hide">Break Time</th>
				            <th title="Sort by work" class="w500-hide">Workbase</th>
				            <th title="Sort by overtime" class="tablet-hide">Overtime</th>
				        </tr>
				    </thead>
				    <tbody>
				        <?php 
				        	
				        	foreach($dtrs as $dtr):
				        		$total_hours_per_row = 0;
				        		$break_time_per_row  = 0;
				        		$user = $this->users_model->get_one_by_where(['id'=>$dtr['user_id']]);
				        		$compare = $this->holidays_model->get_all_by_where(["date"=>date( "Y-m-d",strtotime( $dtr['date']))]);
				        		echo '
				        			<tr tr-id="'.$dtr['id'].'">
				        				<td class="name">';
				        					echo ((strlen($user['name']) > 20) ? substr($user['name'], 0, 17) . '...' : $user['name']) . '<br> <small> '.date( "M d, Y (D)",strtotime( $dtr['date'])).' | '. date( "h:i a",strtotime($dtr["time_in"])).' - '.date( "h:i a",strtotime($dtr["time_out"])) .'</small>';
				        				echo' </td>
				        				<td class="date lh-12px w500-hide">';
				        					//echo $dtr['date'];
				        					echo date( "M d, Y (D)",strtotime( $dtr['date']))."<br>";
				        					if(count($compare) > 0){ echo '<br>';
			                            		if(count($compare) > 1){ $count = 1;
			                            			foreach($compare as $compare){
			                            				if($count > 1){echo "<br>";}
				                            			echo '<i class="t-12px">- '.$compare['name'].'</i>';
				                            			$count++;
				                            		}
			                            		}else{ echo '<i class="t-12px">- '.$compare[0]['name'].'</i>'; }
			                            	}
				        				echo '</td>
				        				<td class="time lh-12px w500-hide">';
				        					$get_log = $this->dtr_model->get_all_by_where(['user_id' => $dtr['user_id'], 'date' => $dtr['date']]);
	                                        foreach($get_log as $gl):
	                                        	if($gl['time_out']):
		                                            $hour_diff = strtotime($gl['time_out'])-strtotime($gl['time_in']);
		                                            $hours = date('H:i:s', $hour_diff);
		                                            $hms = explode(":", $hours);
		                                            if($_SERVER['HTTP_HOST'] == "localhost"): $total = $hms[0] + ($hms[1]/60) - 1;
		                                           	else: $total = $hms[0] + ($hms[1]/60);
		                                           	endif;
		                                            $total_hours_per_row += $total;

		                                            echo date( "h:i a",strtotime($gl['time_in']))." - ".date( "h:i a",strtotime($gl['time_out']))."<br>";
		                                            if($gl['break']):
		                                            	$break = explode("-",$gl['break']);
			                                        	$b_hour_diff = strtotime($break[1])-strtotime($break[0]);
			                                            $b_hours = date('H:i:s', $b_hour_diff);
			                                            $b_hms = explode(":", $b_hours);
			                                            if($_SERVER['HTTP_HOST'] == "localhost"): $b_total = $b_hms[0] + ($b_hms[1]/60) - 1;
			                                            else: $b_total = $b_hms[0] + ($b_hms[1]/60);
			                                            endif;
			                                            
			                                            $total_hours = '<span class="t-12px w-100">Total: '.round(($total-$b_total),2).' hrs</span>' ;
													else:
														$total_hours = '<span class="t-12px w-100 text-danger">Invalid time</span>';
		                                            endif;
		                                            
				                            		echo $total_hours;
		                                        endif;
	                                        endforeach;
				        				echo '</td>
				        				<td class="break w500-hide">';
				        					$break_log = $this->dtr_model->get_all_by_where(['user_id' => $dtr['user_id'], 'date' => $dtr['date']]);
	                                        foreach($break_log as $bl):
	                                        	if($bl['break'] !== "" && $bl['break'] !== NULL):
		                                        	$break = explode("-",$bl['break']);
		                                        	$hour_diff = strtotime($break[1])-strtotime($break[0]);
		                                            $hours = date('H:i:s', $hour_diff);
		                                            $hms = explode(":", $hours);
		                                            if($_SERVER['HTTP_HOST'] == "localhost"): $total = $hms[0] + ($hms[1]/60) - 1;
		                                            else: $total = $hms[0] + ($hms[1]/60);
		                                            endif;
		                                            $break_time_per_row += $total;

		                                        	echo date( "h:i a",strtotime($break[0]))." - ".date( "h:i a",strtotime($break[1]))."<br>";
		                                        else:
		                                        	echo "<span class='opacity-25'>No break</span><br>";
		                                        endif;
	                                        endforeach;
				        				echo '</td>
				        				<td class="work lh-12px w500-hide">';
				        					echo $dtr['time_in_work_base'] .' | '. $dtr['break_out_work_base'];
		                            		if($dtr['shift_reason']){
		                            			echo '<br><u class="t-12px d-flex toggle-arrow" data-toggle="tooltip" title="'.$dtr['shift_reason'].'">Moved Shift</u>';
			                            	}
				        				echo '</td>
				        				<td class="overtime lh-12px tablet-hide">';
			                            	if($dtr['overtime']){
												$ot_exp = explode("-", $dtr['overtime']);
												echo date('h:i A', strtotime($ot_exp[0])) .' - '. date('h:i A', strtotime($ot_exp[1]));
											}else{
												echo '<span class="opacity-25">No Overtime</span>';
											}
				        				echo'</td>
				        			</tr>
				        		';
				        		$total_hours_per_row = 0;
				        	endforeach
				        ?>
				    </tbody>
				</table>
			</div>
		</div>
	</div>
</div>

<?= $this->load->view('modal/download-dtr-list-filter-modal') ?>
