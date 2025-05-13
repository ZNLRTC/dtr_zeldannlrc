<?php 
    if($profile_pic != NULL){
        $profile_photo = base_url() . 'assets_module/user_profile/' . $profile_pic;
    }else{
        switch($gender){
            case 'Male':
                $profile_photo = base_url() . 'assets/img/male-default.jpg';
            break;
            
            case 'Female':
                $profile_photo = base_url() . 'assets/img/female-default.jpg';
            break;

            default: 
                $profile_photo = base_url() . 'assets/user_profile/znlrtc-logo.png';
        }
    }

    function split_time($time){
        $split = explode('-', $time);
        $format1 = date('h:i A', strtotime($split[0]));
        $format2 = date('h:i A', strtotime($split[1]));
        $format = $format1 .' - '. $format2;

        return $format;
    }

?>

<div id="main-observer-view-employee" class="my-5">
    <div class="container">
        <div class="container-fluid">
            <div class="row">
                <a href="<?= base_url('observer') ?>"><h5><u><i class="fa-solid fa-arrow-left-long"></i> Go Back</u></h5></a>

                <div class="col-md-4 d-flex align-items-center justify-content-center">
                    <div class="profile-container">
                        <img class="w-100" src="<?= $profile_photo ?>" alt="<?= $name . ' Profile' ?>">
                    </div>
                </div>

                <div class="col-md-8">
                    <h1 class="mb-0 pb-0"><?= $name ?></h1>
                    <h4 class="text-primary p-0"><?= ucwords($user_type_info['user_type']) ?></h4>
                    <hr>

                    <div class="row">
                        <div class="col-md-6"><b>Email:</b></div>
                        <div class="col-md-6"><?= $email != NULL ? '<a href="mailto:'.$email.'">'.$email.'</a>' : '<span class="opacity-25">No Record</span>' ?></div>
                        <div class="col-md-6"><b>Mobile Number:</b></div>
                        <div class="col-md-6"><?= $mobile_number != NULL ? '<a href="tel:'.$mobile_number.'">'.$mobile_number.'</a>' : '<span class="opacity-25">No Record</span>' ?></div>
                        <div class="col-md-6"><b>Branch:</b></div>
                        <div class="col-md-6"><?= $branch != NULL ? $branch : '<span class="opacity-25">No Record</span>' ?></div>
                        <div class="col-md-6"><b>Gender:</b></div>
                        <div class="col-md-6"><?= $gender != NULL ? $gender : '<span class="opacity-25">No Record</span>' ?></div>
                    </div>
                </div>

                <?php if($user_type != 16):  ?>

                    <div class="col-md-12 bg-light rounded mt-5">
                        <div class="row">
                            <div class="col-md-5">
                                <h4 class="mb-0 pb-0 pt-3">Fixed Schedule:</h4>
                                <p>These are the fixed schedules of the employee for each week.</p>

                                <table class="table table-striped">
                                    <thead class="table-dark">
                                        <th>Week Day</th>
                                        <th>Time</th>
                                        <th>Workbase</th>
                                    </thead>

                                    <tbody>
                                        <tr>
                                            <td>Monday</td>
                                            <td><?= split_time($fixed_schedule['monday']) ?></td>
                                            <td><?= $fixed_schedule['monday_workbase'] == 'WFH' ? 'Work From Home' : 'Office' ?></td>
                                        </tr>

                                        <tr>
                                            <td>Tuesday</td>
                                            <td><?= split_time($fixed_schedule['tuesday']) ?></td>
                                            <td><?= $fixed_schedule['tuesday_workbase'] == 'WFH' ? 'Work From Home' : 'Office' ?></td>
                                        </tr>

                                        <tr>
                                            <td>Wednesday</td>
                                            <td><?= split_time($fixed_schedule['wednesday']) ?></td>
                                            <td><?= $fixed_schedule['wednesday_workbase'] == 'WFH' ? 'Work From Home' : 'Office' ?></td>
                                        </tr>

                                        <tr>
                                            <td>Thursday</td>
                                            <td><?= split_time($fixed_schedule['thursday']) ?></td>
                                            <td><?= $fixed_schedule['thursday_workbase'] == 'WFH' ? 'Work From Home' : 'Office' ?></td>
                                        </tr>

                                        <tr>
                                            <td>Friday</td>
                                            <td><?= split_time($fixed_schedule['friday']) ?></td>
                                            <td><?= $fixed_schedule['friday_workbase'] == 'WFH' ? 'Work From Home' : 'Office' ?></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                            <div class="col-md-6 offset-md-1">
                                <h4 class="mb-0 pb-0 pt-3">Temporary Schedule:</h4>
                                <p>These are the schedules requested by the employee that differ from their fixed schedule.</p>

                                <table class="table table-striped">
                                    <thead class="table-dark">
                                        <th>Date</th>
                                        <th>Schedule From</th>
                                        <th>Schedule To</th>
                                    </thead>

                                    <tbody>
                                        <?php if(!empty($temp_schedule)): ?>
                                            <?php foreach($temp_schedule as $ts): ?>
                                                <tr>
                                                    <td><?= date( "M d, Y (D)", strtotime( $ts['date'] )) ?></td>
                                                    <td><?= split_time($ts['from_time']) . ' | ' . $ts['from_workbase'] ?></td>
                                                    <td><?= split_time($ts['in-out']) . ' | ' . $ts['workbase'] ?></td>
                                                </tr>
                                            <?php endforeach ?>
                                        <?php else: ?>
                                            <tr>
                                                <td colspan = "4" class="text-center"><span class="opacity-25">No Record</span></td>
                                            </tr>
                                        <?php endif ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-12 bg-light rounded mt-3 mb-5 pb-3">
                        <h4 class="mb-0 pb-0 pt-3">Leaves for the month of <?= date('F') ?></h4>
                        <p>These are the employee's filed leaves for the current month.</p>
                        
                        <div class="table-container">
                            <table class="table table-striped">
                                <thead class="table-dark">
                                    <th class="col-2">Date</th>
                                    <th class="col-2">Leave Type</th>
                                    <th class="col-6">Reason</th>
                                    <th class="col-2">Status</th>
                                </thead>

                                <tbody>

                                    <?php foreach($leaves as $l): ?>
                                        <?php 
                                            switch($l['leave_type']){
                                                case 'vacation':
                                                    $l_type = '<small class="alert alert-success px-2 py-1 rounded-pill">'.ucwords($l["leave_type"]).'</small>';
                                                break;

                                                case 'sick': 
                                                    $l_type = '<small class="alert alert-danger px-2 py-1 rounded-pill">'.ucwords($l["leave_type"]).'</small>';
                                                break;

                                                case 'birthday': 
                                                    $l_type = '<small class="alert alert-info px-2 py-1 rounded-pill">'.ucwords($l["leave_type"]).'</small>';
                                                break;

                                                case 'bereavement': 
                                                    $l_type = '<small class="alert alert-warning px-2 py-1 rounded-pill">'.ucwords($l["leave_type"]).'</small>';
                                                break;

                                                default: 
                                                    $l_type = '<small class="alert alert-secondary px-2 py-1 rounded-pill">'.ucwords($l["leave_type"]).'</small>';
                                                break;
                                            }

                                            switch($l['status']){
                                                case 'approved':
                                                    $status = '<small class="alert alert-success px-2 py-1 rounded-pill">Approved</small>';
                                                break;

                                                case 'denied':
                                                    $status = '<small class="alert alert-danger px-2 py-1 rounded-pill">Denied</small>';
                                                break;

                                                case 'cancelled':
                                                    $status = '<small class="alert alert-danger px-2 py-1 rounded-pill">Cancelled</small>';
                                                break;

                                                case 'retracted':
                                                    $status = '<small class="alert alert-warning px-2 py-1 rounded-pill">Retracted</small>';
                                                break;

                                                case 'pending':
                                                    $status = '<small class="text-danger"><i class="fa-solid fa-spinner fa-spin"></i> Pending</small>';
                                                break;

                                                case 'retraction':
                                                    $status = '<small class="text-warning"><i class="fa-solid fa-spinner fa-spin"></i>For Retraction</small>';
                                                break;

                                                default:
                                                    $status = '<small>For Review</small>';
                                                break;
                                            }    
                                        ?>
                                        <tr>
                                            <td><?= date( "M d, Y (D)", strtotime( $l['date'] )) ?></td>
                                            <td><?= $l_type ?></td>
                                            <td><?= $l['details'] ?></td>
                                            <td><?= $status ?></td>
                                        </tr>
                                    <?php endforeach ?>
                                </tbody>
                            </table>
                        </div>
                        
                    </div>
                
                <?php endif ?>
            </div>
        </div>
    </div>
</div>