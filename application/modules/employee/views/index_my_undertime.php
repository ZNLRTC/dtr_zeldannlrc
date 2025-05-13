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
				<div class="col-sm-4"><h3 class="p-0 m-0">Undertime Records</h3></div>
                <div class="col-sm-4">
                    <div class="text-center me-3">
                        <p class="mb-0 pb-0">Your total undertime is <b id="total-undertime-text" class="text-danger "><?= $total_undertime ?></b></p>
                    </div>
                </div>

                <div class="col-sm-4 row">
                    <div class="d-flex justify-content-end align-items-center">
                        <?php if($viewed_by == 'supervisor'): ?>
                            <button class="btn btn-success py-2 me-1 add-undertime-btn w500-hide"><i class="fas fa-plus"></i> Add</button>

                            <button type="button" class="btn btn-sblue dropdown-toggle py-2 d-none d-sm-block me-2" data-bs-toggle="dropdown" data-target="emp-range" aria-expanded="false">
                                Select Employee
                            </button>
                            <ul class="dropdown-menu" id="emp-range">
                                <li data-url-emp="all" class="li-emp-range dropdown-item pointer">--Clear filter--</li>
                                <?php foreach($employees as $e): ?>
                                    <li data-url-emp="<?= $e['id'] ?>" class="li-emp-range dropdown-item pointer <?= $viewer == $e['id'] ? 'active' : '' ?>"><?= $e['name'] ?></li>
                                <?php endforeach ?>
                            </ul>
                        <?php endif ?>
                        
                        <button type="button" class="btn btn-sblue dropdown-toggle py-2 d-none d-sm-block me-2" data-bs-toggle="dropdown" data-target="#date-range" aria-expanded="false">
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

                        <!-- <div>
                            <input id="pending-leave-list-search" type="search" class="form-control" placeholder="Search">
                        </div> -->
                        
                    </div>
                </div>
			</div>
			
			<div id="body-row">
                <table id="undertime-list-table" class="table table-striped w-100">
                    <thead>
                        <tr class="bg-lblue align-middle text-white">
                            <?php if($viewed_by == 'supervisor'): ?>
                                <th>Name</th>
                                <th class="w500-hide">Commit By</th>
                            <?php endif  ?>
                            <th >Date</th>
                            <th >Time</th>
                            <th class="w500-hide">Compensation</th>
                            <th class="w500-hide">Salary Deduction</th>
                            <?php if($viewed_by == 'supervisor'): ?>
                                <th class="w500-hide">Actions</th>
                            <?php endif ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($undertime_logs as $ut): 
                            $sign = $ut['time'][0];
                            $utang_substr = mb_substr($ut['time'], 1);
                            $exp_utang = explode(':', $utang_substr);
                            $hour = intval($exp_utang[0]);
                            $min = intval($exp_utang[1]);

                            $commit_by = $this->users_model->get_row($ut['updated_by']);

                        ?>

                           <tr id="ut-<?= $ut['id'] ?>">
                                <?php if($viewed_by == 'supervisor'): ?>
                                    <td><?= $ut['emp_info']['name'] ?></td>
                                    <td class="w500-hide"><?= $commit_by['name'] ?></td>  
                                <?php endif  ?>
                                <td class="col-2"><?= date( "M d, Y (D)",strtotime( $ut['date'])) ?></td>   
                                <td class="<?= $viewed_by == 'supervisor' ? 'col-2' : 'col-4' ?>"><div class="text-<?= $sign == '-' ? 'danger' : 'success' ?>"><?= $sign .''. $hour .' hours, '. $min . ' minutes' ?></div></td>   
                                <td class="col-1 w500-hide"> 
                                    <?php if($viewed_by == 'employee'): ?>
                                        <?= $ut['compensated'] == 1 ? '<i class="fas fa-check-square text-success"></i>' : '<i class="fas fa-times-circle text-danger"></i>' ?>
                                    <?php else: ?>
                                        <label id="" class="switch">
                                            <input value="<?= $ut['id'] ?>" name="is-compensate-checker" type="checkbox" <?= $ut['compensated'] == 1 ? 'checked' : '' ?> >
                                            <span class="slider round"></span>
                                        </label>
                                    <?php endif ?>
                                </td>  

                                <td class="col-1 w500-hide"> 
                                    <?php if($viewed_by == 'employee'): ?>
                                        <?= $ut['salary_deduction'] == 1 ? '<i class="fas fa-check-square text-success"></i>' : '<i class="fas fa-times-circle text-danger"></i>' ?>
                                    <?php else: ?>
                                        <label id="" class="switch">
                                            <input value="<?= $ut['id'] ?>" name="is-salary-deduction-checker" type="checkbox" <?= $ut['salary_deduction'] == 1 ? 'checked' : '' ?> >
                                            <span class="slider round"></span>
                                        </label>
                                    <?php endif ?>
                                </td>  
                                <?php if($viewed_by == 'supervisor'): ?>
                                    <td class="col-2 w500-hide">
                                        <div>
                                            <span class="alert alert-danger px-2 py-1 rounded-pill delete-undertime-btn mb-0 pointer" data-undertime-id="<?= $ut['id'] ?>"><i class="fas fa-trash-alt"></i> Delete</span>
                                        </div>
                                        
                                    </td>     
                                <?php endif ?>  
                           </tr>
                       <?php endforeach ?>
                    </tbody>
                </table>
    				
			</div>
		</div>
	</div>
</div>

<?php $this->load->view('modal/add-undertime-modal') ?>
<?php $this->load->view('modal/delete-undertime-modal') ?>
