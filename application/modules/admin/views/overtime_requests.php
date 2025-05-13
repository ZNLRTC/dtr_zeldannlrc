<?php
/*
 * Page Name: Overtime Requests
 * Author: Jushua FF
 * Date: 01.30.2023
 */
$user_type_name = $this->users_type_model->get_one_by_where(['id'=>$user_type]);
?>
<div class="w-100 d-flex">
	<?= $this->load->view("partials/sidebar", $user_type_name) ?>
	<div class="w-100 toggled" id="main">
		<?= $this->load->view("partials/header") ?>
		<div id="main-div">
			<div class="table-search-row">
				<h3 class="p-0 m-0">Overtime Request</h3>
				<input type="text" class="search-bar" placeholder="&#xF002;" id="aotr-list-search">
			</div>
			<div id="body-row">
				<?php 
					$otr = ($user_type_name['user_type'] == "admin") ? $this->request_ot_model->get_all_ot() : $this->request_ot_model->get_all_qc_ot();
				?>
				<table id="aotr-list" class="table table-striped w-100">
				    <thead>
				        <tr class="bg-lblue align-middle text-white">
				            <th title="Sort by name">Name</th>
				            <th title="Sort by task" class="desktop-hide">Task/s</th>
				            <th title="Sort by date" class="w405-hide">Date</th>
				            <th title="Sort by status">Status</th>
				            <th></th>
				        </tr>
				    </thead>
				    <tbody>
				        <?php 
				            foreach($otr as $otr):
			                    echo '
			                        <tr tr-id="'.$otr['id'].'">
			                            <td class="name">'.$otr['name'].'</td>
			                            <td class="task desktop-hide">
			                            	<div class="task-div">';
				                            	$task = explode(PHP_EOL, $otr['task']);
				                            	for($a=0;$a<count($task);$a++){
				                            		if(strlen(trim($task[$a])) > 0){
					                            		echo $task[$a]."<br>";
					                            	}
				                            	}
				                            echo '</div>
			                            </td>
			                            <td class="date w405-hide">'.date( "M d, Y (D)",strtotime( $otr['date'])).'</td>
			                            </td>
			                            <td class="status">';
			                            	if($otr['type'] !== "Document Checking"):
			                            		echo "Regular Overtime<br>";
		                            			if($otr['status'] == "pending"):
				                            		echo "<span class='t-green t-12px'>Pending</span>";
				                            	elseif($otr['status'] == "approved"):
				                            		echo "<span class='t-blue t-12px'>Approved</span>";
				                            	elseif($otr['status'] == "denied"):
				                            		echo "<span class='t-red t-12px'>Denied</span>";
				                            	endif;
		                            		else:
		                            			$dc_time = explode("-",$otr["time"]);
		                            			echo 'Document Checking<br><span class="t-12px">'.date("h:i a",strtotime($dc_time[0]))." - ".date( "h:i a",strtotime( $dc_time[1])).'</span>';
		                            		endif;
			                            echo '</td>
			                            <td class="d-flex flex-wrap justify-content-center">';
			                            	if($otr['type'] !== "Document Checking"):
				                            	echo '<button class="update-ot-status btn edit" data-toggle="modal" data-target="#ot-status-modal" title="Update Status" rot-id="'.$otr['id'].'">
				                            		<i class="fas fa-edit"></i> Status
				                            	</button>
				                            	<button class="btn btn-blue otr-details desktop-show" data-toggle="modal" data-target="#view-otr-details-modal" title="View Details" otr-id="'.$otr['id'].'">
				                            		<i class="fas fa-eye"></i> Details
				                            	</button>';
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

<?= $this->load->view('modal/view-otr-details-modal'); ?>
