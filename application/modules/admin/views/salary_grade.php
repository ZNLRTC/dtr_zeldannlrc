<?php
/*
 * Page Name: Salary Grade
 * Author: Jushua FF
 * Date: 01.26.2023
 */
$user_type_name = $this->users_type_model->get_one_by_where(['id'=>$user_type]);
?>
<div class="w-100 d-flex">
	<?= $this->load->view("partials/sidebar", $user_type_name) ?>
	<div class="w-100 toggled" id="main">
		<?= $this->load->view("partials/header") ?>
		<div id="main-div">
			<div class="table-search-row">
				<h3 class="p-0 m-0">Salary Grade List</h3>
				<input type="text" class="search-bar" placeholder="&#xF002;" id="salary-grade-list-search">
			</div>
			<div id="body-row">
				<?php $sg_list = $this->salary_grade_model->get_all(); ?>
				<table id="salary-grade-list" class="table table-striped">
				    <thead>
				        <tr class="bg-lblue align-middle text-white">
				            <th title="Sort by name">Grade Number</th>
				            <th title="Sort by email">Hourly Rate</th>
				            <th></th>
				        </tr>
				    </thead>
				    <tbody>
				        <?php 
				            foreach($sg_list as $sg_list):
			                    echo '
			                        <tr tr-id="'.$sg_list['id'].'">
			                            <td class="grade-number">'.$sg_list['grade_number'].'</td>
			                            <td class="hourly-rate">PHP '.$sg_list['hourly_rate'].'</td>
			                            <td class="d-flex">
			                                <button class="btn edit edit-salary-grade" sg-id="'.$sg_list['id'].'" data-toggle="modal" data-target="#salary-grade-cru-modal" title="Edit grade info"><i class="fas fa-edit"></i> Edit</button>
			                                <button class="btn view" sg-id="'.$sg_list['id'].'" data-toggle="modal" data-target="#salary-grade-employee-modal" title="View employees"><i class="fas fa-eye"></i> Employee</button>';
			                                $check = $this->users_model->get_all_by_where(["salary_grade"=>$sg_list['id']]);
			                                if(!$check):
			                                	echo '<button class="btn delete delete-salary-grade" sg-id="'.$sg_list['id'].'" data-toggle="modal" data-target="#delete-salary-modal" title="Delete Salary Grade"><i class="fas fa-trash"></i> Delete</button>';
			                                endif;
			                            echo '</td>
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

<?= $this->load->view('modal/salary-grade-employee-modal'); ?>
<?= $this->load->view('modal/delete-salary-modal'); ?>
