<?php

/*

 * Page Name: admin

 * Author: Jushua FF

 * Date: 09.11.2022

 */

$user_type_name = $this->users_type_model->get_one_by_where(['id'=>$user_type]);

?>

<div class="w-100 d-flex">

	<?= $this->load->view("partials/sidebar", $user_type_name) ?>

	<div class="w-100 toggled" id="main">

		<?= $this->load->view("partials/header") ?>

		<div id="main-div">

			<div class="table-search-row">

				<h3 class="p-0 m-0">Employee List</h3>

				<input type="text" class="search-bar" placeholder="&#xF002;" id="employee-list-search">

			</div>

			<div id="body-row">

				<?php 

					$emp_list = ($user_type_name['user_type'] == "admin") ? $this->users_model->get_all_desc_by_id($archive=0) : $this->users_model->get_all_desc_qc_users($archive=0);

				?>

				<table id="employee-list" class="table table-striped w-100">

				    <thead>

				        <tr class="bg-lblue align-middle text-white">

				            <th title="Sort by name">Name</th>

				            <th title="Sort by email" class="mobile-hide">Email</th>

				            <th title="Sort by mobile number" class="tablet-hide">Mobile Number</th>

				            <th class="text-center mobile-hide" title="Sort by user type">User Type</th>

				            <th class="text-center mobile-hide" title="Sort by branch">Branch</th>

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

				                            <td class="text-center mobile-hide user_type">'.$user_type['user_type'].'</td>

				                            <td class="text-center mobile-hide branch">'.$emp_list['branch'].'</td>

				                            <td class="d-flex justify-content-center">

												<a href="'.base_url() .'admin/employee_dtr?user_id='.$emp_list['id'].'"><button class="btn view"><i class="fa-solid fa-eye"></i> <span class="mobile-hide">DTR</span></button></a>

                                                <a href="'.base_url() .'admin/employee_leaves?id='. $emp_list['id']. '" class="d-flex justify-content-end">
                                                <button class="btn view view-leaves-btn d-flex align-items-center">
													<i class="fas fa-sign-out left"></i> 
													<span class="mobile-hide">Leaves</span>
												</button> </a>
                                                                                           
				                                <a class="btn edit edit-profile" href="view_profile?id='.$emp_list['id'].'" title="Edit employee info" user-type="'.$emp_list['user_type'].'"><i class="fas fa-edit"></i> <span class="mobile-hide">Profile</span></a>

				                               	<button class="btn archive archive-user arc-act-user-btn" user-id="'.$emp_list['id'].'" data-toggle="modal" data-target="#archive-user-modal" title="Archive employee" data-action="archive" data-emp-name="'.$emp_list['name'].'"><i class="fas fa-archive"></i> <span class="mobile-hide">Archive</span></button>

				                            </td>

				                        </tr>

				                    ';

                                    // <button class="btn password update-password" user-id="'.$emp_list['id'].'" data-toggle="modal" data-target="#update-password-modal" title="Reset password"><i class="fas fa-key"></i> <span class="mobile-hide">Password</span></button>

									// <button class="btn edit edit-profile" user-id="'.$emp_list['id'].'" data-toggle="modal" data-target="#employee-cru-modal" title="Edit employee info" user-type="'.$emp_list['user_type'].'"><i class="fas fa-edit"></i> <span class="mobile-hide">Edit</span></button>

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



