<?php

namespace App\Http\Controllers\Backend\Hr;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Admin;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class ReportController extends Controller
{
    //
    public $user;

    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $this->user = Auth::guard('admin')->user();
            return $next($request);
        });
    }

    public function choose()
    {
        if (is_null($this->user) || !$this->user->can('hr_prime.view')) {
            abort(403, 'Pardon!! vous n\'avez pas l\'autorisation de visualiser les primes! Mufise ico mubaza hamagara kuri 130 canke 122');
        }   

        return view('backend.pages.hr.report.choose');
    }
}
