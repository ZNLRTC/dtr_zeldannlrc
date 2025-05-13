<?php
    $user_type_name = $this->users_type_model->get_one_by_where(['id'=>$user_type]);
    $user_type_name['user_id'] = $id;

    function split_schedule($timeRange) {
        if($timeRange){
            $i = explode('-', $timeRange);
            return date('h:i A', strtotime($i[0])) .'-'. date('h:i A', strtotime($i[1]));
        }else{
            return '<span class="opacity-25">No Record</span>';
        }
        
    }
?>

<div class="w-100 d-flex">
	<?= $this->load->view("partials/sidebar", $user_type_name) ?>
	<div class="w-100 toggled" id="main">
		<?= $this->load->view("partials/header") ?>
		<div id="main-div">
			<div class="table-search-row">
				<div class="col-lg-6 col-md-12 col-sm-6 d-flex justify-content-between">
					<h3 class="p-0 m-0 me-5">Active DTR Beta</h3>
				</div>
                <div class="col-lg-6 col-md-12 col-sm-6 d-flex justify-content-end">
                    <div>
					    <input id="active-dtr-table-search" class="form-control" type="search" placeholder="Search">
                    </div>
				</div>
			</div>

            <table id = "active-dtr-table" class="table">
                <thead>
                    <tr class="bg-lblue align-middle text-white">
                        <th>Name</th>
                        <th>Date</th>
                        <th>Schedule</th>
                        <th>Time-in</th>
                        <th>Break-in</th>
                        <th>Break-out</th>
                        <th>Time-out</th>
                        <th></th>
                    </tr>
                </thead>

                <tbody>
                    <?php foreach($employees as $dtr): ?>
                        <?php if($dtr['id'] != 1): ?>
                            <?php if(isset($dtr['active_dtr'])): ?>
                                <?php  
                                    $breaks = explode('-', $dtr['active_dtr']['break']); 
                                    $bg = '';

                                    if($dtr['active_dtr']['schedule_workbase'] != 'WFH/Office'){
                                        if($dtr['active_dtr']['schedule_workbase'] != $dtr['active_dtr']['time_in_work_base']){
                                            $bg = 'bg-lred';
                                        }
                                        if($dtr['active_dtr']['break_out_work_base'] != ''){
                                            if($dtr['active_dtr']['schedule_workbase'] != $dtr['active_dtr']['break_out_work_base']){
                                                $bg = 'bg-lred';
                                            }
                                        }
                                        
                                    }


                                ?>
                                <tr class="bg-light <?= $bg ?>" data-emp-id="<?= $dtr['id'] ?>" data-month="<?= date('m') ?>" data-year="<?= date('Y') ?>">
                                    <td><i title="<?= $dtr['active_dtr']['time_out'] != NULL ? 'Timed out' : 'Active' ?>" class="fas fa-circle <?= $dtr['active_dtr']['time_out'] != NULL ? 'text-secondary' : 'text-success' ?>"></i> <?= $dtr['name'] ?></td>
                                    <td><?= date( "M d, Y (D)",strtotime( $dtr['active_dtr']['date'])) ?></td>
                                    <td><?= split_schedule($dtr['active_dtr']['schedule_time']) .' | '. $dtr['active_dtr']['schedule_workbase'] ?></td>
                                    <td><?= date('h:i A', strtotime($dtr['active_dtr']['time_in'])) .' | '. $dtr['active_dtr']['time_in_work_base'] ?></td>
                                    <td><?= $dtr['active_dtr']['break'] ? date('h:i A', strtotime($breaks[0])) : '<span class="opacity-25">No Record</span>' ?></td>
                                    <td><?= $dtr['active_dtr']['break'] ? isset($breaks[1]) ? date('h:i A', strtotime($breaks[1])) .' | '. $dtr['active_dtr']['break_out_work_base']  : '<span class="opacity-25">No Record</span>' : '<span class="opacity-25">No Record</span>' ?></td>
                                    <td><?= $dtr['active_dtr']['time_out'] != NULL ? date('h:i A', strtotime($dtr['active_dtr']['time_out'])) : '<span class="opacity-25">No Record</span>' ?></td>
                                    <td>
                                        <button class="btn view view-dtr-btn"><i class="fa-solid fa-eye"></i> Dtr</butotn>
                                        <button class="btn edit view-leaves-btn"><i class="fa-solid fa-eye"></i> Leaves</button>
                                    </td>
                                </tr>
                            <?php else: ?>
                                <tr class="opacity-25" data-emp-id="<?= $dtr['id'] ?>" data-month="<?= date('m') ?>" data-year="<?= date('Y') ?>">
                                    <td><i title="<?= $dtr['on_leave_today'] == 1 ? $dtr['on_leave_details'] : 'Waiting to Login' ?>" class="fas fa-circle <?= $dtr['on_leave_today'] == 1 ? 'text-danger' : 'text-warning' ?>"></i> <?= $dtr['name'] ?></td>
                                    <td><?= date( "M d, Y (D)") ?></td>
                                    <td><?= split_schedule($dtr['sched_time']) .' | '. $dtr['sched_workbase'] ?></td>
                                    <td>No Record</td>
                                    <td>No Record</td>
                                    <td>No Record</td>
                                    <td>No Record</td>
                                    <td>
                                        <button class="btn view view-dtr-btn"><i class="fa-solid fa-eye"></i> Dtr</butotn>
                                        <button class="btn edit view-leaves-btn"><i class="fa-solid fa-eye"></i> Leaves</button>
                                    </td>
                                </tr>
                            <?php endif ?>
                        <?php endif ?>
                    <?php endforeach ?>
                </tbody>
            </table>
			
		</div>
	</div>
</div>