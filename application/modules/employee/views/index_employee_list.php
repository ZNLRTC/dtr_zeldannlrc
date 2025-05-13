<?php
    $user_type_name = $this->users_type_model->get_one_by_where(['id'=>$user_type]);
    $user_type_name['user_id'] = $id;
?>

<div class="w-100 d-flex">
	<?= $this->load->view("partials/sidebar", $user_type_name) ?>
	<div class="w-100 toggled" id="main">
		<?= $this->load->view("partials/header") ?>
		<div id="main-div">
			<div class="table-search-row">
				<div class="col-lg-4 col-md-12 col-sm-12">
					<h3 class="p-0 m-0 me-5">Employee List</h3>
				</div>
                <div class="col-lg-8 col-md-12 col-sm-6 d-flex justify-content-end">
                    <div>
					    <input id="employee-leave-list-search" class="form-control mobile-hide" type="search" placeholder="Search">
                    </div>
				</div>
			</div>
            <div class="px-3">
				<table id="employee-leave-list" class="table table-striped">
					<thead>
						<tr class="bg-lblue align-middle text-white">
							<th>Employee Details</th>
						</tr>
					</thead>
					<tbody>
						<?php foreach($employees as $emp): ?>
							<tr data-emp-id="<?= $emp['id'] ?>" data-month="<?= date('m') ?>" data-year="<?= date('Y') ?>">
								<td class="d-flex align-items-center justify-content-between">
									<div class="col-4"><?= ucwords($emp['name']) ?></div>
									<div class="col-3 mobile-hide"><?= ucwords($emp['department']) ?></div>
									<div class="col-5 text-right">
										<button class="btn view view-dtr-btn d-inline-block"><span class="d-none mobile-show"><i class="fas fa-info-circle"></i></span> <span class="mobile-hide">Dtr</span></butotn>
										<button class="btn edit view-leaves-btn d-inline-block"><span class="d-none mobile-show"><i class="fas fa-user-minus"></i></span> <span class="mobile-hide">Leaves</span></button>
										<a class="btn cancel edit-profile d-inline-block" user-id="<?= $emp['id'] ?>" user-type="<?= $emp['user_type'] ?>" href="view_profile?id=<?= $emp['id'] ?>"><span class="d-none mobile-show"><i class="fas fa-clock"></i></span> <span class="mobile-hide">Sched</span></a>
										<a href="<?= base_url() ?>employee/my_undertime?emp_id=<?= $emp['id'] ?>" class="btn cancel edit-profile d-inline-block"><span class="d-none mobile-show"><i class="fas fa-clock"></i></span> <span class="mobile-hide">UT</span></a>
									</div>
								</td>
							</tr>
						<?php endforeach ?>
					</tbody>
				</table>
            </div>
			
		</div>
	</div>
</div>

<?= $this->load->view('partials/modal/employee-cru-modal') ?>