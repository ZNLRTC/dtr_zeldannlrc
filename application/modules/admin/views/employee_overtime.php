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
				<div class="col-sm-6"><h3 class="p-0 m-0">Overtime Records</h3></div>

                <div class="col-sm-6 row">
                    <div class="d-flex justify-content-end align-items-center">

                        <button type="button" class="btn btn-sblue dropdown-toggle py-2 d-none d-sm-block me-1" data-bs-toggle="dropdown" data-target="emp-range" aria-expanded="false">
                            Select Employee
                        </button>
                        <ul class="dropdown-menu" id="emp-range">
                            <li data-url-emp="all" class="li-emp-range dropdown-item pointer">--Clear filter--</li>
                            <?php foreach($employees as $e): ?>
                                <li data-url-emp="<?= $e['id'] ?>" class="li-emp-range dropdown-item pointer <?= $viewed_emp == $e['id'] ? 'active' : '' ?>"><?= $e['name'] ?></li>
                            <?php endforeach ?>
                        </ul>

                        <button type="button" class="btn btn-sblue dropdown-toggle py-2 d-none d-sm-block" data-bs-toggle="dropdown" data-target="#date-range" aria-expanded="false">
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
                        
                    </div>
                </div>
			</div>
			
			<div id="body-row">
                <table id="undertime-list-table" class="table table-striped w-100">
                    <thead>
                        <tr class="bg-lblue align-middle text-white">
                            <th>Name</th>
                            <th class="w500-hide">Approve/Denied By</th>
                            <th class="w500-hide">Type</th>
                            <th>Date</th>
                            <th class="w500-hide">Time In</th>
                            <th class="w500-hide">Break In</th>
                            <th class="w500-hide">Break Out</th>
                            <th class="w500-hide">Time Out</th>
                            <th class="w500-hide"></th>
                            <th>Paid</th>
                            <th>Status</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php foreach($overtimes as $ot): ?>
                            <?php 
                                  $time_in = date('h:iA', strtotime($ot['time_in']));
                                  $break_in = $ot['break_in'] == null ? '<span class="opacity-25">No Record</span>' : date('h:iA', strtotime($ot['break_in']));
                                  $break_out = $ot['break_out'] == null ? '<span class="opacity-25">No Record</span>' : date('h:iA', strtotime($ot['break_out']));
                                  $time_out = $ot['time_out'] == null ? '<span class="opacity-25">No Record</span>' : date('h:iA', strtotime($ot['time_out']));

                                  $type = $ot['type'] == 'holiday' ? $ot['holiday_desc'] : ucwords($ot['shift']) .' Shift';
                            ?>
                            <tr class="<?= $ot['time_out'] == NULL ? 'bg-warning' : '' ?>">
                                <td><?= $ot['emp_name'] ?></td>
                                <td class="w500-hide"><?= $ot['updated_by'] ?></td>
                                <td class="w500-hide"><?= $type ?></td>
                                <td><?= date( "M d, Y (D)",strtotime( $ot['date'])) .' | <b>' .$ot['workbase']. '</b>'  ?></td>
                                <td class="w500-hide"><?= $time_in ?></td>
                                <td class="w500-hide"> <?= $break_in ?></td>
                                <td class="w500-hide"><?= $break_out ?></td>
                                <td class="w500-hide"><?= $time_out ?></td>
                                <td class="w500-hide">
                                    <?php if($ot['time_out'] != null): ?>
                                        <a href="view-eod-report-modal" class="view-eod-report-btn" data-ot-id="<?= $ot['id'] ?>"><u>Task</u></a></td>
                                    <?php endif ?>
                                <td>
                                    <?php if($ot['status'] == 'approved' ): ?>
                                        <label id="" class="switch">
                                            <input value="<?= $ot['id'] ?>" name="is-paid-checker" type="checkbox" <?= $ot['paid'] == 1 ? 'checked' : '' ?> >
                                            <span class="slider round"></span>
                                        </label>
                                    <?php endif ?>
                                </td>
                                <td>
                                    <?php 
                                        switch($ot['status']){
                                            case 'pending':
                                                if($ot['time_out'] == NULL){
                                                    $text = '<span class="text-white"><i class="fa-solid fa-spinner fa-spin"></i> Pending</span>';
                                                }else{
                                                    $text = '<span class="text-warning"><i class="fa-solid fa-spinner fa-spin"></i> Pending</span>';
                                                }
                                                
                                            break;

                                            case 'approved':
                                                $text = '<span class="text-success"><i class="fa-solid fa-circle-check"></i> Approved</span>';
                                            break;

                                            case 'denied':
                                                $text = '<span title="View Denied Reason" class="text-danger pointer denied-otreq-btn" data-req-id="'.$ot["id"].'"><i class="fa-solid fa-circle-xmark"></i> Denied</span>';
                                            break;
                                        }

                                        echo $text;
                                    ?>
                                </td>
                            </tr>
                        <?php endforeach ?>
                    </tbody>
                </table>
    				
			</div>
		</div>
	</div>
</div>

<?= $this->load->view('../../employee/views/modal/view-eod-report-modal') ?>


