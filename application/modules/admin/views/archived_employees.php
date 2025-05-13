<?php

/*

 * Page Name: Archived Employees

 * Author: Jushua FF

 * Date: 17.01.2022

 */

$user_type_name = $this->users_type_model->get_one_by_where(['id'=>$user_type]);

?>

<div class="w-100 d-flex">

	<?= $this->load->view("partials/sidebar", $user_type_name) ?>

	<div class="w-100 toggled" id="main">

		<?= $this->load->view("partials/header") ?>

		<div id="main-div">

			<div class="table-search-row">

				<h3 class="p-0 m-0">Archived Employees</h3>

				<input type="text" class="search-bar" placeholder="&#xF002;" id="employee-list-search">

			</div>

			<div id="body-row">

				<?php 

					$emp_list = ($user_type_name['user_type'] == "admin") ? $this->users_model->get_all_desc_by_id($archive=1) : $this->users_model->get_all_desc_qc_users($archive=1);

				?>

				<table id="employee-list" class="table table-striped w-100">

				    <thead>

				        <tr class="bg-lblue align-middle text-white">

				            <th title="Sort by name">Name</th>

				            <th title="Sort by email" class="mobile-hide">Email</th>

				            <th title="Sort by mobile number" class="tablet-hide">Mobile Number</th>

				            <th class="text-center mobile-hide" title="Sort by user type">User Type</th>

				            <th></th>

				        </tr>

				    </thead>

				    <tbody>

				        <?php 

				            foreach($emp_list as $emp_list):

				                $user_type = $this->users_type_model->get_one_by_where(['id'=>$emp_list["user_type"]]);

				                if($emp_list['id'] !== $id):

				                    echo '

				                        <tr id="emp-'.$emp_list['id'].'">

				                            <td class="d-flex align-items-center">

				                                <div class="emp-profile">

				                                    <img class="profile_pic" src="';

				                                        if($emp_list['profile_pic']):

				                                            echo base_url("assets_module/user_profile/{$emp_list['profile_pic']}");

				                                        else:

				                                            echo base_url("assets/img/".($emp_list['gender']=='Male' ? "male": "female")."-default.jpg");

				                                        endif;

				                                    echo '">

				                                </div>

				                                <div>

				                                <div class="name">'.$emp_list['name'].'</div>

				                                <span class="username w-100">@'.$emp_list['username'].'</span>

				                                </div>

				                            </td>

				                            <td class="email mobile-hide">'.$emp_list['email'].'</td>

				                            <td class="mobile_number tablet-hide">'.$emp_list['mobile_number'].'</td>

				                            <td class="text-center mobile-hide">'.$user_type['user_type'].'</td>

				                            <td class="text-center">

				                                <button class="btn activate activate-user arc-act-user-btn" user-id="'.$emp_list['id'].'" data-toggle="modal" data-target="#archive-user-modal" title="Archive employee" data-action="reactivate" data-emp-name="'.$emp_list['name'].'"><i class="fas fa-archive"></i> Reactivate</button>

				                            </td>

				                        </tr>

				                    ';

				                endif;

				            endforeach;

				        ?>

				    </tbody>

				</table>

			</div>

		</div>

	</div>

</div>



<?= $this->load->view('modal/archive-user-modal'); ?>

<?= $this->load->view('modal/salary-grade-employee-modal'); ?>

