<?php

if (!defined('BASEPATH')) { exit('No direct script access allowed'); }

class Ajax_employee_profile_view extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model([
            'events_model',
            'dtr_model',
            'user_schedule_model',
        ]);
    }

    function get_week_day_date($day_of_week) {
        // Get the current week (assuming the calendar is for this week)
        $current_date = new DateTime();
        $current_date->modify('this week ' . $day_of_week);
        return $current_date->format('Y-m-d'); // Format as YYYY-MM-DD for FullCalendar
    }

    public function load_events() {
        $id = $this->input->post('id');
        $dtrs = $this->dtr_model->get_all_my_dtr_desc($id, 06, date('Y'));
        $schedule = $this->user_schedule_model->get_one_by_where(['user_id' => $id]);

        $start_date = new DateTime('first day of this month');
        $end_date = new DateTime('last day of this month');

        $days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
        $events = [];

        for ($date = clone $start_date; $date <= $end_date; $date->modify('+1 day')) {
            $day_of_week = strtolower($date->format('l'));
            
            if (in_array($day_of_week, $days) && !empty($schedule[$day_of_week])) {

                list($start_time, $end_time) = explode('-', $schedule[$day_of_week]);
                $break_sched = explode('-', $schedule[$day_of_week]);
                $sched_arr = [date('h:iA', strtotime($break_sched[0])), date('h:iA', strtotime($break_sched[1]))];
                $combine_sched = implode('-', $sched_arr);
                $events[] = [
                    'title' => $combine_sched,
                    'start' => $date->format('Y-m-d') . 'T' . $start_time,
                    'end' => $date->format('Y-m-d') . 'T' . $end_time,
                    'allDay' => true,
                    'extendedProps' => array(
                        'workbase' => $schedule[$day_of_week . '_workbase']
                    )
                ];
            }
        }
        
        echo json_encode($events);
    }

    public function add_event() {
        $event_data = array(
            'title' => $this->input->post('title'),
            'start' => $this->input->post('start'),
            'end' => $this->input->post('end')
        );
        $this->events_model->add_event($event_data);
        echo json_encode(['status' => 'success']);
    }

    public function delete_event() {
        $event_id = $this->input->post('id');
        $this->events_model->delete_event($event_id); // Delete event from the database
        echo json_encode(['status' => 'success']);
    }



}