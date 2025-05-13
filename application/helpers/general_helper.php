<?php

defined('BASEPATH') OR exit('No direct script access allowed');



if(!function_exists('numberToColorHsl')){

    function numberToColorHsl($i, $start = 0, $end = 120) {

        $a = $i;

        $b = floor(($end - $start) * $a);

        $c = $b + $start;

        return "hsl(${c}, 100%, 55%)";

    }

}



if(!function_exists('versionAsset')) {

  function versionAsset($asset)

  {

    return base_url($asset)."?ver=".filemtime(FCPATH.$asset);

  }

}

function recaptcha_site_key(){
  return '6Ld_60spAAAAAJTDdPaicOo6OpPemy1ZDP7hqmvx';
}

function recaptcha_secret_key(){
  return '6Ld_60spAAAAAM8-f0egFXjfo5DYG_cWYgR9pzZl';
}

function get_month_and_year($first_record, $last_record = null) {
  if (empty($first_record)) {
      return false;
  }

  $start_date = new DateTime($first_record[0]['date']);

  if ($last_record) {
      $end_date = new DateTime($last_record[0]['date']);
  } else {
      $end_date = new DateTime();
  }

  $current_date = clone($end_date);
  $month_years = [];

  while ($current_date >= $start_date) {
      $month_year = $current_date->format('m F Y');
      
      // Add only if the month-year combination isn't already in the array
      if (!in_array($month_year, $month_years)) {
          $month_years[] = $month_year;
      }
      
      $current_date->modify('-1 month');
  }

  return $month_years;
}

function convert_date($date_string) {
  try {
    $date = new DateTime($date_string, new DateTimeZone('Asia/Manila'));
    $formatted_date = $date->format('M d, Y (D)');
    return $formatted_date;
  } catch (Exception $e) {
    return "Invalid Date";
  }
}

function compute_time_difference($start, $end, $break_in = null, $break_out = null){
  $format = 'H:i';

  $start_time = DateTime::createFromFormat($format, $start);
  $end_time = DateTime::createFromFormat($format, $end);

  if (!$start_time || !$end_time) {
    return "Invalid time format";
  }

  if ($end_time < $start_time) {
    $end_time->modify('+1 day');
  }

  if ($break_in !== null && $break_out !== null) {
    $break_in_time = DateTime::createFromFormat($format, $break_in);
    $break_out_time = DateTime::createFromFormat($format, $break_out);
    if (!$break_in_time || !$break_out_time) {
      return "Invalid break time format";
    }

    $break_interval = $break_in_time->diff($break_out_time);
    $break_minutes = ($break_interval->h * 60) + $break_interval->i;

    $end_time->sub(new DateInterval('PT'.$break_minutes.'M'));
  }

  $interval = $start_time->diff($end_time);
  return $interval->format('Total: %h:%i mins');
}

function compute_all_time_difference($start, $end, $break_in = null, $break_out = null) {
    $format = 'H:i';

    $start_time = DateTime::createFromFormat($format, $start);
    $end_time = DateTime::createFromFormat($format, $end);

    if (!$start_time || !$end_time) {
        return "Invalid time format";
    }

    if ($end_time < $start_time) {
        $end_time->modify('+1 day');
    }

    $break_minutes = 0;
    if ($break_in !== null && $break_out !== null) {
        $break_in_time = DateTime::createFromFormat($format, $break_in);
        $break_out_time = DateTime::createFromFormat($format, $break_out);
        if (!$break_in_time || !$break_out_time) {
            return "Invalid break time format";
        }

        $break_interval = $break_in_time->diff($break_out_time);
        $break_minutes = ($break_interval->h * 60) + $break_interval->i;

        // Ensure the break is at least 1 hour
        if ($break_minutes < 60) {
            $break_minutes = 60;
        }
    }

    $end_time->sub(new DateInterval('PT'.$break_minutes.'M'));

    $interval = $start_time->diff($end_time);

    $hours = $interval->h;
    $minutes = $interval->i;

    // Define the expected working hours per day (e.g., 8 hours)
    $expected_work_hours = 8;
    $expected_work_minutes = $expected_work_hours * 60;

    $worked_minutes = ($hours * 60) + $minutes;
    $difference_minutes = $worked_minutes - $expected_work_minutes;

    $overtime_minutes = $difference_minutes > 0 ? $difference_minutes : 0;
    $undertime_minutes = $difference_minutes < 0 ? abs($difference_minutes) : 0;

    $overtime_hours = intdiv($overtime_minutes, 60);
    $overtime_remaining_minutes = $overtime_minutes % 60;

    $undertime_hours = intdiv($undertime_minutes, 60);
    $undertime_remaining_minutes = $undertime_minutes % 60;

    $total_hours = $hours;
    $total_minutes = $minutes;

    if ($undertime_minutes > 0) {
        return sprintf(
            'Total: %02d:%02d <span class="text-danger"><b>-%d:%dmin</b></span>',
            $total_hours, $total_minutes,
            $undertime_hours, $undertime_remaining_minutes
        );
    } else {
        return sprintf(
            'Total: %02d:%02d <span class="text-success"><b>+%d:%dmin</b></span>',
            $total_hours, $total_minutes,
            $overtime_hours, $overtime_remaining_minutes
        );
    }
}


function compute_time_break_difference($total_time, $break_time){

  echo '<pre>';
  print_r($break_time);
  exit;

  $total_hr_min = explode(', ',$total_time);
  $total_hr = explode(' ', $total_hr_min[0]);
  $total_min = explode(' ', $total_hr_min[1]);
  $total_real_time = $total_hr[0] .':'. $total_min[0] .':00';

  $break_hr_min = explode(', ',$break_time);
  $break_hr = explode(' ', $break_hr_min[0]);
  $break_min = explode(' ', $break_hr_min[1]);
  $break_real_time = $break_hr[0] .':'. $break_min[0] .':00';

  $diff = $total_real_time .' - '. $break_real_time; 

  //return $total_real_time .' - '. $break_real_time; 

}

function compute_total_worked_hours($dtr_date, $get_in, $get_out, $get_exp_break_in, $get_exp_break_out, $get_schedule_in, $get_schedule_out, $workbase, $schedule_minutes) {
    $currentDate = new DateTime($dtr_date);
    $comparisonDate = new DateTime('2024-08-29');

    $time_in = new DateTime($get_in);
    $time_out = new DateTime($get_out);
    $schedule_in = new DateTime($get_schedule_in);
    $schedule_out = new DateTime($get_schedule_out);
    $min_break = $workbase == 'WFH/Office' ? 90 : 60; // 90 minutes for WFH/Office, 60 minutes for office-based
    $total_over_time = '0:0';
    $total_under_time = '0:0';

    if ($time_out < $time_in) {
        $time_out->modify('+1 day');
    }

    if ($schedule_out < $schedule_in) {
        $schedule_out->modify('+1 day');
    }

    // Finalize time_in and time_out
    $final_time_in = $time_in <= $schedule_in ? $schedule_in : $time_in;
    
    if ($get_exp_break_in == NULL && $get_exp_break_out == NULL) {
        $final_break_minutes = 0;
        $excess_break = 0;
    } else {
        $break_in = new DateTime($get_exp_break_in);
        $break_out = new DateTime($get_exp_break_out);
        $final_break = $break_out->diff($break_in);
        $final_break_minutes = ($final_break->h * 60) + $final_break->i; // Break time in minutes

        $to_bi_checker = $time_out->diff($break_in);

        // Ensure a minimum break if the break was taken as expected
        if ((($to_bi_checker->h * 60) + $to_bi_checker->i) >= $min_break) {
            $final_break_minutes = $final_break_minutes <= $min_break ? $min_break : $final_break_minutes;
        }

        // Calculate the excess break time beyond the allowed break limit
        $excess_break = $final_break_minutes > $min_break ? $final_break_minutes - $min_break : 0;
        $final_break_minutes -= $excess_break;
    }

    // Adjust schedule_out based on the date comparison
    if ($currentDate < $comparisonDate) {
        $schedule_out->modify('+1 hour');
    } else {
        $schedule_out->modify('+15 minutes');
    }

    $final_time_out = $time_out >= $schedule_out ? $schedule_out : $time_out;
    $final_time = $final_time_out->diff($final_time_in);
    $final_time_minutes = ($final_time->h * 60) + $final_time->i;

    // Calculate rendered time excluding break time
    $renderred_time = $final_time_minutes - $final_break_minutes;

    // Adjust required work hours based on the schedule
    $required_work_hours = $schedule_minutes < 480 ? $schedule_minutes : 480;

    // Calculate undertime/overtime
    $under_over_time = $renderred_time - $required_work_hours;
    $under_over_time_hour = intdiv($under_over_time, 60);
    $under_over_time_min = $under_over_time % 60;
    $under_over_time_combined = abs($under_over_time_hour) . ':' . abs($under_over_time_min);

    if($under_over_time > 0){
        $total_over_time = $under_over_time_combined;
    }else{
        $total_under_time = $under_over_time_combined;
    }

    if($excess_break > 0){
        $exp_ut = explode(':', $total_under_time);
        $ut_hr = intval($exp_ut[0]);
        $ut_min = intval($exp_ut[1]);
        $ut_total = (($ut_hr * 60) + $ut_min) + $excess_break;
        $ut_total_hr = abs(intdiv($ut_total, 60));
        $ut_total_min = abs($ut_total % 60);
        $total_under_time = $ut_total_hr .':'. $ut_total_min;

        if($total_over_time != '0:0'){
            $exp_ot = explode(':', $total_over_time);
            $ot_hr = intval($exp_ot[0]);
            $ot_min = intval($exp_ot[1]);
            $ot_total = (($ot_hr * 60) + $ot_min) - $excess_break;
            $ot_total_hr = intdiv($ot_total, 60);
            $ot_total_min = $ot_total % 60;
            $total_over_time = $ot_total_hr .':'. $ot_total_min;
        }
        
    }

    $renderred_time -= $excess_break;

    // Return the calculated data
    $data = array(
        'total_renderred_time' => sprintf('%01d:%02d', intdiv($renderred_time, 60), ($renderred_time % 60)),
        'total_over_time' => $total_over_time,
        'total_under_time' => $total_under_time,
    );

    return $data;
}

//last touch: add absolute value

function compute_undertime($undertimes_utang){
    $total_undertime = 0;
    foreach($undertimes_utang as $utang){
        $s_utang = str_split($utang['time']);

        //if($s_utang[0] == '-'){
            $utang_substr = mb_substr($utang['time'], 1);
            $exp_utang = explode(':', $utang_substr);
            $hour = intval($exp_utang[0]);
            $min = intval($exp_utang[1]);
            $to_min = ($hour * 60) + $min;
            $total_undertime += $to_min;
        //}
    }

    return intdiv($total_undertime, 60) .' hrs, '. ($total_undertime % 60) . ' mins';
}

function compute_undertime_in_minutes($undertimes_utang){
    $total_undertime = 0;
    foreach($undertimes_utang as $utang){
        //$s_utang = str_split($utang['time']);
        $utang = mb_substr($utang['time'], 1);
        $exp_utang = explode(':', $utang);
        $hour = intval($exp_utang[0]);
        $min = intval($exp_utang[1]);
        $to_min = ($hour * 60) + $min;
        $total_undertime += $to_min;
    }

    return $total_undertime;
}