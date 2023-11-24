<?php

namespace App\Http\Controllers;

use App\Http\Resources\AppointmentCalendarResource;
use App\Models\Appointment;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CalendarController extends Controller
{
   public function index(Request $request) {
      return Inertia::render('Calendar');
   }

   public function getEvents(Request $request) {
       $month = $request->query('month') ?? date('m');
       $year = $request->query('year') ?? date('Y');

       $appointments = Appointment::query()
           ->whereRaw('MONTH(start) = ? and YEAR(start) = ?', [$month, $year])
           ->with(['creator', 'patients', 'note'])
           ->get();

      return response()->json([
          'data' =>  AppointmentCalendarResource::collection($appointments)
      ]);
   }
}
