    <?php
if (!defined('BASEPATH')) { exit('No direct script access allowed'); }

class Ajax_test_undertime extends CI_Controller {

    public function __construct(){
        parent::__construct();
    }


    function index() {

        // $ti = $this->input->get('ti');
        // $bi = $this->input->get('bi');
        // $bo = $this->input->get('bo');
        // $to = $this->input->get('to');
        // $si = $this->input->get('si');
        // $so = $this->input->get('so');

        $ti = '14:33';
        $bi = '18:29';
        $bo = '19:05';
        $to = '00:03';
        $si = '13:00';
        $so = '22:00';
        
        $time_in = new DateTime($ti);
        $break_in = new DateTime($bi);
        $break_out = new DateTime($bo);
        $time_out = new DateTime($to);
        $schedule_in = new DateTime($si);
        $schedule_out = new DateTime($so);

        if ($time_out < $time_in) {
            $time_out->modify('+1 day');
        }

        // Finalize time_in and time_out
        if($time_in <= $schedule_in){
            $final_time_in = $schedule_in;
        }else{
            $final_time_in = $time_in;
        }

        $schedule_out->modify('+1 hour');

        $final_time_out = $time_out >= $schedule_out ? $schedule_out : $time_out;

        $final_break = $break_out->diff($break_in);
        $final_break_minutes = ($final_break->h * 60) + $final_break->i;
        $final_break_minutes = $final_break_minutes <= 60 ? 60 : $final_break_minutes;

        $final_time = $final_time_out->diff($final_time_in);
        $final_time_minutes = ($final_time->h * 60) + $final_time->i;
        $renderred_time = $final_time_minutes - $final_break_minutes;
        $required_work_hours = 480;

        $under_over_time = $required_work_hours - $renderred_time;

        $under_over_time_hour = intdiv($under_over_time, 60);
        $under_over_time_min = $under_over_time % 60;
        $under_over_time_combined = $under_over_time_hour .':'. $under_over_time_min;

        $response = array(
            '<br>final_time_in' => $final_time_in,
            '<br>final_time_out' => $final_time_out,
            '<br>final_time' => $final_time,
            '<br>final_break' => $final_break_minutes,
            '<br>final_renderred_time' => $renderred_time
        );

        if ($renderred_time == $required_work_hours) {
            $response[] = [
                '<br>computed_min' => $under_over_time_combined,
                '<br>status' => 'exact-time'];
        }elseif ($renderred_time < $required_work_hours) {
            $response[] = [
                '<br>computed_min' => $under_over_time_combined,
                '<br>status' => 'under-time'];
        } else {
            $response[] = [
                '<br>computed_min' => $under_over_time_combined,
                '<br>status' => 'over-time'];
        }

        echo json_encode($response);
    }


}