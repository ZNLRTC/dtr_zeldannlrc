<?php

/*

 * Page Name: Custom Holidays

 * Author: Jushua FF

 * Date: 03.03.2023

 */

$user_type_name = $this->users_type_model->get_one_by_where(['id'=>$user_type]);

?>

<div class="w-100 d-flex">

	<?= $this->load->view("partials/sidebar", $user_type_name) ?>

	<div class="w-100 toggled" id="main">

		<?= $this->load->view("partials/header") ?>

		<div id="main-div">

			<div class="table-search-row">

				<h3 class="p-0 m-0">Custom Holidays</h3>

				<input type="text" class="search-bar" placeholder="&#xF002;" id="custom-holiday-list-search">

			</div>

			<div id="body-row">

				<?php $h_list = $this->holidays_model->get_all_by_sort_date(); ?>

				<table id="custom-holiday-list" class="table table-striped w-100">

				    <thead>

				        <tr class="bg-lblue align-middle text-white">

				            <th title="Sort by name">Name</th>

				            <th title="Sort by email" class="mobile-hide">Date</th>

				            <th title="Sort by type" class="w550-hide">Type</th>

				            <th></th>

				            <th></th>

				        </tr>

				    </thead>

				    <tbody>

				        <?php 

				            foreach($h_list as $h_list):

			                    echo '

			                        <tr tr-id="'.$h_list['id'].'">

			                            <td class="name">'.$h_list['name'].' <br> <small class="d-none mobile-show"><b>'. date( "M d, Y (D)",strtotime( $h_list['date'])) .'</b></small></td>

			                            <td class="date mobile-hide">'.date( "M d, Y (D)",strtotime( $h_list['date'])).'</td>

			                            <td class="type w550-hide">';

			                            	if($h_list['type'] == "regular"):

                                                echo "Regular Holiday";

                                            elseif($h_list['type'] == "special"):

                                                echo "Special Non-working Holiday";

                                            else:

                                                echo "Special Working Holiday";

                                            endif;

			                            echo '</td>

			                            <td class="text-center">

			                            	<button class="btn edit update-holiday w500-100" title="Update Holiday" h-id="'.$h_list['id'].'" data-toggle="modal" data-target="#holiday-cru-modal"><i class="fas fa-edit"></i> <span class="mobile-hide">Update</span></button>

			                            	<button class="btn delete delete-holiday w500-100" title="Delete Holiday" h-id="'.$h_list['id'].'" data-toggle="modal" data-target="#delete-holiday-modal"><i class="fas fa-trash"></i> <span class="mobile-hide">Delete</span></button>

			                            </td>

										<td>
											'. $h_list["date"] .'
										</td>

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

<?= $this->load->view("modal/delete-holiday-modal") ?>