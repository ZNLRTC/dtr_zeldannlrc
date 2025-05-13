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

<div id="main-observer">
    <div class="container">
        <div class="container-fluid d-flex align-items-start justify-content-center">
            <div id="main-div" class="w-100">
                <div class="d-flex justify-content-end">
                    <a href="<?= base_url() . 'observer/view?id=' . $id ?>" title="View Profile" class="me-2"><i class="fas fa-user"></i></a>
                    <a href="#" data-bs-target="#log-out-modal" data-bs-toggle="modal"><i title="Logout" class="fas fa-power-off"></i></a>
                </div>

                <div class="table-search-row observer-taas px-0">
                    <div class="col-lg-6 col-md-12 col-sm-6">
                        <h3 class="p-0 m-0 me-5">ZNLRTC Employee Active DTR</h3>
                        <small><?= date( "F d, Y (l)") ?></small>
                    </div>
                    <div class="col-lg-6 col-md-12 col-sm-6 d-flex justify-content-end">
                        <div>
                            <input id="observer-active-dtr-table-search" class="form-control" type="search" placeholder="Search">
                        </div>
                    </div>
                </div>

                <div class="table-container table-responsive">
                    <table id = "observer-active-dtr-table" class="table w-100">
                        <thead class="align-middle table-dark">
                            <tr>
                                <th>Name</th>
                                <th>Schedule</th>
                                <th>Time-in</th>
                                <th>Break-in</th>
                                <th>Break-out</th>
                                <th>Time-out</th>
                                <th>Actions</th>
                            </tr>
                        </thead>

                        <tbody>
                            <?php foreach($employees as $emp): ?>
                                <?php if($emp['id'] != 1): ?>
                                    <?php 
                                        switch($emp['user_type']){
                                            case '3':
                                                $alert_color = 'warning';
                                            break;

                                            case '7':
                                                $alert_color = 'success';
                                            break;
                                            
                                            case '11':
                                                $alert_color = 'info';
                                            break;
                                            
                                            case '12':
                                                $alert_color = 'danger';
                                            break;

                                            case '13':
                                                $alert_color = 'primary';
                                            break;
                                            
                                            default:
                                                $alert_color = 'secondary';
                                            break;
                                        }    
                                    ?>

                                    <?php if(isset($emp['active_dtr'])): ?>
                                        <?php  
                                            $breaks = explode('-', $emp['active_dtr']['break']); 
                                            $bg = '';

                                            if($emp['active_dtr']['schedule_workbase'] != 'WFH/Office'){
                                                if($emp['active_dtr']['schedule_workbase'] != $emp['active_dtr']['time_in_work_base']){
                                                    $bg = 'bg-lred';
                                                }
                                                if($emp['active_dtr']['break_out_work_base'] != ''){
                                                    if($emp['active_dtr']['schedule_workbase'] != $emp['active_dtr']['break_out_work_base']){
                                                        $bg = 'bg-lred';
                                                    }
                                                }
                                                
                                            }
                                        ?>
                                        <tr class="bg-light <?= $bg ?>" data-emp-id="<?= $emp['id'] ?>" data-month="<?= date('m') ?>" data-year="<?= date('Y') ?>">
                                            <td>
                                                <i title="<?= $emp['active_dtr']['time_out'] != NULL ? 'Timed out' : 'Active' ?>" class="fas fa-circle <?= $emp['active_dtr']['time_out'] != NULL ? 'text-secondary' : 'text-success' ?>"></i> 
                                                <small class="alert alert-<?= $alert_color ?> px-2 py-1 rounded-pill"><?= ucwords($emp['user_type_info']['user_type']) ?></small>
                                                <?= $emp['name'] ?>
                                            </td>
                                            <td><?= split_schedule($emp['active_dtr']['schedule_time']) .' | '. $emp['active_dtr']['schedule_workbase'] ?></td>
                                            <td><?= date('h:i A', strtotime($emp['active_dtr']['time_in'])) .' | '. $emp['active_dtr']['time_in_work_base'] ?></td>
                                            <td><?= $emp['active_dtr']['break'] ? date('h:i A', strtotime($breaks[0])) : '<span class="opacity-25">No Record</span>' ?></td>
                                            <td><?= $emp['active_dtr']['break'] ? isset($breaks[1]) ? date('h:i A', strtotime($breaks[1])) .' | '. $emp['active_dtr']['break_out_work_base']  : '<span class="opacity-25">No Record</span>' : '<span class="opacity-25">No Record</span>' ?></td>
                                            <td><?= $emp['active_dtr']['time_out'] != NULL ? date('h:i A', strtotime($emp['active_dtr']['time_out'])) : '<span class="opacity-25">No Record</span>' ?></td>
                                            <td>
                                                <a class="btn view" href="observer/view?id=<?= $emp['id'] ?>"><i class="fa-solid fa-eye"></i> Profile</a>
                                            </td>
                                        </tr>
                                    <?php else: ?>
                                        <tr class="opacity-25" data-emp-id="<?= $emp['id'] ?>" data-month="<?= date('m') ?>" data-year="<?= date('Y') ?>">
                                            <td>
                                                <i title="<?= $emp['on_leave_today'] == 1 ? $emp['on_leave_details'] : 'Waiting to Login' ?>" class="fas fa-circle <?= $emp['on_leave_today'] == 1 ? 'text-danger' : 'text-warning' ?>"></i> 
                                                <small class="alert alert-<?= $alert_color ?> px-2 py-1 rounded-pill"><?= ucwords($emp['user_type_info']['user_type']) ?></small>
                                                <?= $emp['name'] ?>
                                            </td>
                                            <td><?= split_schedule($emp['sched_time']) .' | '. $emp['sched_workbase'] ?></td>
                                            <td>No Record</td>
                                            <td>No Record</td>
                                            <td>No Record</td>
                                            <td>No Record</td>
                                            <td>
                                                <a class="btn view" href="observer/view?id=<?= $emp['id'] ?>"><i class="fa-solid fa-eye"></i> Profile</a>
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
    </div>
</div>