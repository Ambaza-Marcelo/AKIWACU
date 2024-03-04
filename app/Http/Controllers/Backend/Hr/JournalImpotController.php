<?php

namespace App\Http\Controllers\Backend\Hr;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Admin;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\HrPaiement;
use App\Models\HrCompany;
use App\Exports\Hr\JournalIreExport;
use Carbon\Carbon;
use Validator;
use Excel;
use PDF;

class JournalImpotController extends Controller
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

    public function index($company_id)
    {
        //
        if (is_null($this->user) || !$this->user->can('hr_journal_paie.view')) {
            abort(403, 'Pardon!! vous n\'avez pas l\'autorisation de visualiser les paies! Mufise ico mubaza hamagara kuri 130 canke 122');
        }

        $journal_impots = HrPaiement::where('employe_id','!=','')->where('company_id',$company_id)->get();

        return view('backend.pages.hr.journal_impot.index',compact('journal_impots'));
        
    }

    public function selectByCompany()
    {
        if (is_null($this->user) || !$this->user->can('hr_employe.view')) {
            abort(403, 'Pardon!! vous n\'avez pas l\'autorisation de visualiser les employés! Mufise ico mubaza hamagara kuri 130 canke 122');
        }

        $companies = HrCompany::all();
        return view('backend.pages.hr.journal_impot.select_by_company',compact('companies'));
    }

    public function exportToExcel(Request $request)
    {
        return Excel::download(new JournalIreExport, 'journal_IRE.xlsx');
    }
}
