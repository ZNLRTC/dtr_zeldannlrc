<?php

/*

 * Page Name: Holidays

 * Author: Jushua FF

 * Date: 03.03.2023

 */

?>

<div class="w-100 d-flex">

	<?= $this->load->view("partials/sidebar", $user_type_name) ?>

	<div class="w-100 toggled" id="main">

		<?= $this->load->view("partials/header") ?>

		<div id="main-div">

			<div class="table-search-row">

				<h3 class="p-0 m-0">Holiday List</h3>

				<input type="text" class="search-bar" placeholder="&#xF002;" id="holiday-list-search">

			</div>

			<div id="body-row">

				<table id="holiday-list" class="table table-striped w-100">

				    <thead>

				        <tr class="bg-lblue align-middle text-white">

				            <th title="Sort by name">Name</th>

				            <th title="Sort by email" class="mobile-hide">Date</th>

				            <th title="Sort by type" class="mobile-hide">Type</th>

							<th></th>

				        </tr>

				    </thead>

				    <tbody>

				        <?php 

				            foreach($holiday_lists as $h_list):

								switch ($h_list['type']){
									case 'regular':
										$holiday = 'Regular Holiday';
									break;
									case 'special':
										$holiday = 'Special Non-working Holiday';
									break;
									default:
										$holiday = 'Special Working Holiday';
									break;
								}

								//replace default year to current year
								$holiday_array = explode('-', $h_list['date']);
								$holiday_new_date_array = [date('Y'), $holiday_array[1], $holiday_array[2]];
								$holiday_new_date = implode('-', $holiday_new_date_array);

			                    echo '

			                        <tr tr-id="'.$h_list['id'].'">

			                            <td class="name">'.$h_list['name'].'<br><small class="d-none mobile-show"><b>'. date( "M d, Y (D)",strtotime( $h_list['date'])) .' | '. $holiday .'</b></small></td>

			                            <td class="date mobile-hide">'.date( "M d, Y (D)",strtotime($holiday_new_date)).'</td>

			                            <td class="type mobile-hide">'. $holiday .'</td>

										<td>'.$holiday_new_date.'</td>

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

