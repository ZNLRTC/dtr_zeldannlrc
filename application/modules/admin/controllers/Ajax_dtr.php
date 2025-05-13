<?php
if (!defined('BASEPATH')) { exit('No direct script access allowed'); }

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\IOFactory;

class Ajax_dtr extends CI_Controller {
    public function __construct(){
        parent::__construct();
        $this->load->model([
            'users_model',
            'dtr_model',
            'request_ot_model',
            'salary_grade_model',
            'holidays_model', 
            'request_leave_model',
            'user_schedule_model'
        ]);
        //require_once APPPATH . 'third_party/PhpSpreadsheet/autoload.php';
    }

    public function download_dtr_validation(){
        if(!$this->input->is_ajax_request()){ show_404(); }
        $this->form_validation
             ->set_rules("start-date","start-date","required")
             ->set_rules("end-date","end-date","required|callback_validate_end_date")
             ->set_message("required","required");

        if($this->form_validation->run() == FALSE){
            $response = [
                'status' => 'form-incomplete',
                'errors' => $this->form_validation->error_array()
            ];
        }else{
            $this->session->set_userdata("start-date", $_POST['start-date']);
            $this->session->set_userdata("end-date", $_POST['end-date']);

            if(isset($_POST['user_id'])){
                $this->session->set_userdata("employee-id", $_POST['user_id']);
            }

            $response = [
                'status'  => 'success',
                'message' => 'Generating PDF'
            ];
        }
        echo json_encode($response); 
    }

    public function download_dtr(){
        $spreadsheet = new Spreadsheet();
        if($this->session->has_userdata('start-date') && $this->session->has_userdata('end-date')):
            $spreadsheet->getProperties()
                ->setCreator("zeldannlrc.com")
                ->setLastModifiedBy("Jushua F.F.")
                ->setTitle("Employee DTR List")
                ->setSubject("Employee DTR List")
                ->setDescription("Excel file that displays the detailed report of Zeldan Nordic Language Training employees Daily Time record, overtimes and leaves")
                ->setKeywords("ZNLRC dtr list")
                ->setCategory("Excel");

            $start = date( "Ymd",strtotime($this->session->userdata('start-date')));
            $end   = date( "Ymd",strtotime($this->session->userdata('end-date') ." + 1 day"));

            $account_session = $this->session->userdata('account_session');
            $admin_info      = $this->users_model->get_one_by_where(['id'=>$account_session['id']]);

            $user    = ($admin_info["user_type"] == "1") ? $this->users_model->get_all_by_where(['user_type !=' => "1", "user_type !=" => "5", 'archive' => "0"]) : $this->users_model->get_all_by_where(['user_type !=' => "1", "user_type !=" => "5", 'archive' => "0", "branch" => "Quezon City"]);
            $row_num = 3;

            $columnWidths = ['A' => 15, 'B' => 26, 'C' => 13, 'D' => 13, 'E' => 13, 'F' => 13, 'G' => 26, 'H' => 20, 'I' => 13, 'J' => 13];
            foreach ($columnWidths as $column => $width) { $spreadsheet->setActiveSheetIndex(0)->getColumnDimension($column)->setWidth($width); }

            $spreadsheet->setActiveSheetIndex(0) 
                ->setCellValue('A1', 'Employee DTR List')
                ->setCellValue('G1', '(From '.date( "M d, Y",strtotime($start)).' to '.date( "M d, Y",strtotime($end." - 1 day")).')')
                ->mergeCells('A1:F1')->mergeCells('G1:J1')
                ->getStyle('G1:J1')->getAlignment()->setHorizontal('right');
            $spreadsheet->setActiveSheetIndex(0) 
                ->getStyle('A1:J1')->getFont()->setBold(true)
                ->getColor()->setARGB('FFFFFF');
            $spreadsheet->setActiveSheetIndex(0) 
                ->getStyle('A1:J1')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                ->getStartColor()->setARGB('004f82');

            foreach($user as $user):
                $total_break_hours = 0;

                //Checks if user has existing DTR's on the specified timeline
                $check_user = $this->dtr_model->get_all_by_where(['user_id' => $user['id']]);

                if($check_user):
                    $sched_display = $user['schedule'];
                    if(stripos($user['schedule'],"-")):
                        $sched         = explode("-",$user['schedule']);
                        $sched_display = ucfirst($sched[0]).": ".date("h:i a",strtotime($sched[1]))." - ".date("h:i a",strtotime($sched[2]));
                    endif;

                    $spreadsheet->setActiveSheetIndex(0) 
                        ->setCellValue('A'.$row_num, $user['name'])
                        ->setCellValue('G'.$row_num, 'Schedule: '.$sched_display)
                        ->mergeCells('A'.$row_num.':F'.$row_num)->mergeCells('G'.$row_num.':J'.$row_num)
                        ->getStyle('G'.$row_num.':J'.$row_num)->getAlignment()->setHorizontal('right');
                    $spreadsheet->setActiveSheetIndex(0) 
                        ->getStyle('A'.$row_num.':J'.$row_num)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                        ->getStartColor()->setARGB('0c72ba');

                    $spreadsheet->setActiveSheetIndex(0)
                        ->getStyle('A'.$row_num.':J'.$row_num)
                        ->getFont()->setBold(true)
                        ->getColor()->setARGB('FFFFFF');

                    $row_num++;
                    $spreadsheet->setActiveSheetIndex(0)
                        ->setCellValue('A'.$row_num, 'Date(m/d/y)')
                        ->setCellValue('B'.$row_num, 'Schedule')
                        ->setCellValue('C'.$row_num, 'Time-in')
                        ->setCellValue('D'.$row_num, 'Break')
                        ->setCellValue('F'.$row_num, 'Time-out')
                        ->setCellValue('G'.$row_num, 'Holiday')
                        ->setCellValue('H'.$row_num, 'Overtime')
                        ->setCellValue('I'.$row_num, 'Place')
                        ->setCellValue('J'.$row_num, 'Hours')
                        ->mergeCells('D'.$row_num.':E'.$row_num)
                        ->getStyle('A'.$row_num.':J'.$row_num)
                        ->getFont()->setBold(true);
                    $spreadsheet->setActiveSheetIndex(0)->getStyle('B:I')->getAlignment()->setHorizontal('center');
                    $spreadsheet->setActiveSheetIndex(0)->getStyle('J')->getAlignment()->setHorizontal('right');

                    $period = new DatePeriod(
                        new DateTime(date( "Ymd",strtotime($start))),
                        new DateInterval('P1D'),
                        new DateTime(date( "Ymd",strtotime($end)))
                    );

                    foreach ($period as $key => $value):
                        $total_hours_per_row = 0;
                        $break_time_per_row  = 0;
                        $row_num++;
                        $sat_sun = ($value->format('D') == "Sat" || $value->format('D') == "Sun") ? "9bf09b" : "";
                        $date    = $value->format('Y-m-d');

                        if($sat_sun):
                            $spreadsheet->setActiveSheetIndex(0)
                            ->getStyle('A'.$row_num.':J'.$row_num)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                            ->getStartColor()->setARGB($sat_sun);
                        endif;

                        $spreadsheet->setActiveSheetIndex(0)->mergeCells('D'.$row_num.':E'.$row_num);

                        $h_list        = $this->holidays_model->get_all();
                        $date_compare  = date("Y-m-d",strtotime($date));
                        $count_compare = 0;
                        $holiday       = "";

                        //Some dates have multiple Holiday needing the use of foreach
                        foreach($h_list as $h_list):
                            if($h_list['date'] == $date):
                                $count_compare++;
                                if($h_list['type'] == "regular"): $holiday = "Regular Holiday";
                                elseif($h_list['type'] == "special"): $holiday = "Special Non-working Holiday";
                                else: $holiday = "Special Working Holiday";
                                endif;
                                break;
                            endif;
                        endforeach;

                        $lr         = $this->request_leave_model->get_one_by_where(["user_id"=>$user['id'], "date"=>$date]);
                        //To cater old data prior to system revisions
                        $leave      = ($lr && $lr['status'] !== "denied") ? "EB9C5C" : null;
                        $check_date = $this->dtr_model->get_one_by_where(["user_id"=>$user['id'], "date"=>$date]);

                        $spreadsheet->setActiveSheetIndex(0)->setCellValue('A'.$row_num,$date);

                        if($leave):
                            $spreadsheet->setActiveSheetIndex(0)
                            ->mergeCells('B'.$row_num.':J'.$row_num)
                            ->setCellValue('B'.$row_num,ucfirst($lr['leave_type'])." Leave | ".ucfirst($lr['details']))
                            ->getStyle('A'.$row_num.':J'.$row_num)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                            ->getStartColor()->setARGB($leave);
                        else:
                            if($check_date):
                                $break_report = "";
                                if($check_date['break']):
                                    $break    = explode("-",$check_date['break']);
                                    $total    = 0;
                                    if(count($break)>1):
                                        $hour_diff    = strtotime($break[1])-strtotime($break[0]);
                                        $hours        = date('H:i:s', $hour_diff);
                                        $hms          = explode(":", $hours);
                                        $total        = ($_SERVER['HTTP_HOST'] == "localhost") ? $hms[0] + ($hms[1]/60) - 1 : $hms[0] + ($hms[1]/60);
                                        $break_report = date("h:i a",strtotime($break[0]))."-".date("h:i a",strtotime($break[1]));
                                    else:
                                        $break_report = date("h:i a",strtotime($break[0]));
                                    endif;

                                    $total_break_hours  += $total; 
                                    $break_time_per_row += $total;
                                    
                                else:
                                    $break_report = date("h:i a",strtotime($check_date['break']));
                                endif;

                                if($check_date['time_out']){
                                    $hour_diff = strtotime($check_date['time_out'])-strtotime($check_date['time_in']);
                                    $hours     = date('H:i:s', $hour_diff);
                                    $hms       = explode(":", $hours);
                                    $total     = ($_SERVER['HTTP_HOST'] == "localhost") ? $hms[0] + ($hms[1]/60) - 1 : $hms[0] + ($hms[1]/60);

                                    $total_hours_per_row += $total;
                                }

                                $overtime       = "";
                                $overtime_color = "039487";//default blue
                                if($check_date['overtime']):
                                    $time     = explode("-",$check_date['overtime']);
                                    $overtime = date("h:i a", strtotime($time[0]))." - ".date("h:i a", strtotime($time[1]));
                                endif;

                                $hours_worked = ($total_hours_per_row)?round(($total_hours_per_row-$break_time_per_row),2): null;
                                $workbases    = ($check_date['time_in_work_base']) ? $check_date['time_in_work_base'] .'/'. $check_date['break_out_work_base'] : $check_date['work_base'];

                                //fetching fixed schedule
                                $schedule_fixed = $this->user_schedule_model->get_one_by_where(['user_id' => $user['id']]);
                                $day            = strtolower(date('l', strtotime($check_date['date'])));
                                $daily_sched    = explode('-', $schedule_fixed[$day]);
                                $sched          = date('h:i', strtotime($daily_sched[0])) .' - '.date('h:i', strtotime($daily_sched[1])) . ' (' . $schedule_fixed[$day . '_workbase'] . ')';

                                $schedule_time  = ($check_date['schedule_time'])? explode('-',$check_date['schedule_time']) : null;
                                if($schedule_time):
                                    //Change value of sched if schedule_time is set and so that it will be compatible with previous revisions
                                    $sched      = date('h:i', strtotime($schedule_time[0])) .' - '.date('h:i', strtotime($schedule_time[1]));
                                endif;

                                $spreadsheet->setActiveSheetIndex(0)
                                    ->setCellValue("B".$row_num,$sched)
                                    ->setCellValue("C".$row_num,date( "h:i a",strtotime($check_date['time_in'])))
                                    ->mergeCells('D'.$row_num.':E'.$row_num)
                                    ->setCellValue("D".$row_num,$break_report)
                                    ->setCellValue("F".$row_num,($check_date['time_out'])?date( "h:i a",strtotime($check_date['time_out'])):"")
                                    ->setCellValue("I".$row_num,$workbases)
                                    ->setCellValue("J".$row_num,($hours_worked>0)?$hours_worked:0);

                                $spreadsheet->setActiveSheetIndex(0)
                                    ->setCellValue("H".$row_num,$overtime)
                                    ->getStyle('H'.$row_num)->getFont()->getColor()->setARGB($overtime_color);
                            endif;
                            $spreadsheet->setActiveSheetIndex(0)->setCellValue("G".$row_num,$holiday);
                        endif;
                    endforeach;
                    $spreadsheet->setActiveSheetIndex(0)->mergeCells('A'.($row_num+1).':J'.($row_num+1));
                    $row_num+=2;
                endif;
            endforeach;
            $this->save($spreadsheet, 'employee_dtr_list.xlsx');
        else:
            show_404();
        endif;
    }

    protected function save($spreadsheet, $filename) {
        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        $writer->save('php://output');
    }

    public function validate_end_date(){
        if(!$this->input->is_ajax_request()){ show_404(); }
        $start = new DateTime($_POST['start-date']);
        $end   = new DateTime($_POST['end-date']);
        if($start > $end){
          $this->form_validation->set_message("validate_end_date","End date must be greater than start date");
          return false;
        }
        return true;
    }

    public function dtr_details(){
        $total_hours_per_row = 0;
        $dtr  = $this->dtr_model->get_row($_POST['dtr_id']);
        $user = $this->users_model->get_row($dtr['user_id']);
        echo '
            <div class="row input-con">
                <div class="col-md-3 offset-md-1"><label>Name: </label></div>
                <div class="col-md-7"><div class="form-control">'.$user['name'].'</div></div>
              </div>

              <div class="row input-con">
                <div class="col-md-3 offset-md-1"><label>Date: </label></div>
                <div class="col-md-7"><div class="form-control">'.date( "M d, Y (D)",strtotime($dtr['date'])).'</div></div>
              </div>

              <div class="row input-con">
                <div class="col-md-3 offset-md-1"><label>Time: </label></div>
                <div class="col-md-7"><div class="form-control">';
                    $get_log = $this->dtr_model->get_all_by_where(['user_id' => $dtr['user_id'], 'date' => $dtr['date']]);
                    foreach($get_log as $gl):
                        $hour_diff = strtotime($gl['time_out'])-strtotime($gl['time_in']);
                        $hours = date('H:i:s', $hour_diff);
                        $hms = explode(":", $hours);
                        $total = $hms[0] + ($hms[1]/60) + ($hms[2]/3600) - 1;
                        $total_hours_per_row += $total;

                        echo date( "h:i a",strtotime($gl['time_in']))." - ".date( "h:i a",strtotime($gl['time_out']))."<br>";
                    endforeach;
                echo '</div></div>
                <div class="col-md-7 offset-md-4"><div class="form-control">Total: '.$total_hours_per_row.' hrs</div></div>
              </div>
              <div class="row">
                <div class="col-md-10 offset-md-1"><hr></div>
              </div>
              <div class="row input-con">
                <div class="col-md-3 offset-md-1"><label>Shift: </label></div>
                <div class="col-md-7"><div class="form-control">';
                    $get_shift = $this->dtr_model->get_all_by_where(['user_id' => $dtr['user_id'], 'date' => $dtr['date'], 'shift_reason !=' => NULL]);
                    if($get_shift):
                        foreach($get_shift as $gs):
                            echo $gs["shift_reason"]."<br>";
                        endforeach;
                    else:
                        echo "---";
                    endif;
                echo '</div></div>
              </div>

              <div class="row input-con">';
                echo '<div class="col-md-3 offset-md-1"><label class="w-100">Overtime: </label>';
                    $otr = $this->request_ot_model->get_ot($dtr['user_id'],$dtr['date']);
                    if($otr){
                        if($otr['status'] == "pending"):
                            echo "<i class='t-green status'>Pending</i>";
                        elseif($otr['status'] == "approved"):
                            echo "<i class='t-blue status'>Approved</i>";
                        elseif($otr['status'] == "denied"):
                            echo "<i class='t-red status'>Denied</i>";
                        endif;
                    }
                echo'</div>
                <div class="col-md-7"><div class="form-control">';
                    if($otr):
                        $times = explode(" ",$otr['time']);
                        for($a=0;$a<count($times);$a++):
                            if($times[$a]):
                                $in_out = explode("-",$times[$a]);
                                echo date( "h:i a",strtotime( $in_out[0])).' - '.date( "h:i a",strtotime( $in_out[1])).'<br>';
                            endif;
                        endfor;
                    else:
                        echo "---";
                    endif;
                echo'</div></div>
              </div>
        ';
    }

}