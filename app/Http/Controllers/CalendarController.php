<?php

namespace App\Http\Controllers;

use App\Models\Calendar;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class CalendarController extends Controller
{
    /* 
    * index view
    */
    public function index()
    {
        $now            = Carbon::now();
        $carbon_dates   = $this->CarbonDates($now, null, [], null, null);

        $current_month  = $carbon_dates['current_month'];
        $current_year   = $carbon_dates['current_year'];
        $last_day_month = $carbon_dates['last_day_month'];

        $days_arr       = $carbon_dates['days_arr'];
        
        return view('calendar.index', compact(['current_month', 'current_year', 'last_day_month', 'days_arr']));
    }


    /* 
    * Storing to DB
    */
    public function store(Request $request)
    {
        $this->validate($request, [

            'event_title'   => [
                                'nullable',
                                'regex:/^[a-zA-Z0-9 .\-]+$/i'
                            ],

            'from'          => [
                                'required',
                                'date',
                                'date_format:Y-m-d',
                            ],

            'to'            => [
                                'required',
                                'date',
                                'date_format:Y-m-d',
                                'after:from'
                            ],

            'day'           => [

                                'required',
                                'min:2'
                            ]

        ],
        [
            'event_title.regex'  => 'Plese input letters and numbers',
            'day.min'           => 'Please select atleast 2 days',
        ]);


        $event_title    = $request->event_title;
        $from           = $request->from;
        $to             = $request->to;
        $days           = $request->day;
        $start          = date('d', strtotime($from));
        $end            = date('d', strtotime($to));


        $now            = new Carbon($from);
        
        $carbon_dates   = $this->CarbonDates($now, $event_title, $days, $start, $end);
        
        $current_month  = $carbon_dates['current_month'];
        $current_year   = $carbon_dates['current_year'];
        $last_day_month = $carbon_dates['last_day_month'];

        $days_arr       = $carbon_dates['days_arr'];
        $ins_arr        = [];

        foreach ($days_arr as $value) {

            if (null != $value['event']) {
                
                array_push($ins_arr, [
                    'event_title'   => $event_title,
                    'date'          => $value['date'],  
                ]);
            }
        }
        

        Calendar::insert($ins_arr);

        
        $view = view('calendar.render-table', compact(['current_month', 'current_year', 'last_day_month', 'days_arr']))->render();

        return Response::json($view);
            
    }


    /* 
    * A function for determining the day of a date
    * and to match the event title to a date
    */
    public function CarbonDates($now, $event_title, $days, $start, $end)
    {
        $current_month  = $now->format('M');
        $_current_month = $now->format('m');
        $current_year   = $now->format('Y');
        $last_day_month = $now->endOfMonth()->format('d');

        $days_arr       = [];

        for ($i = 1; $i <= $last_day_month; $i++) {
            $dt         = Carbon::create($current_year, $_current_month, $i, 0);
            $day        = $dt->format('D');
            $dt_format  = $i . ' ' . $day;
            

            $disp_event = (in_array($day, $days) && ($start <= $i) && ($end >= $i))  ? $event_title : null;
            
            array_push($days_arr, [
                'dt'    => $dt_format,
                'date'  => $dt->toDateString(),
                'event' => $disp_event,
            ]);

        }

        return [
            'current_month'     => $current_month,
            'current_year'      => $current_year,
            'last_day_month'    => $last_day_month,
            'days_arr'          => $days_arr,
        ];
    }
}
