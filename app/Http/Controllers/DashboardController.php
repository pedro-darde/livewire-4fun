<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Patient;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
        return Inertia::render('Dashboard', [
            'todayAppointments' => Appointment::todayAppointments()->with(['patients'])->get(),
            'patients' => Patient::whereHas('appointments')->with(['appointments'])->get(),
            'totalPatients' => Patient::count(),
        ]);
    }
}
