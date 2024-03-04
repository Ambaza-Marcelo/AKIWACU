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
use App\Exports\Hr\JournalCotisationExport;
use Carbon\Carbon;
use Excel;
use Validator;
use PDF;

class JournalCotisationController extends Controller
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

        $journal_cotisations = HrPaiement::where('employe_id','!=','')->where('company_id',$company_id)->get();

        return view('backend.pages.hr.journal_cotisation.index',compact('journal_cotisations'));
        
    }

    public function selectByCompany()
    {
        if (is_null($this->user) || !$this->user->can('hr_employe.view')) {
            abort(403, 'Pardon!! vous n\'avez pas l\'autorisation de visualiser les employés! Mufise ico mubaza hamagara kuri 130 canke 122');
        }

        $companies = HrCompany::all();
        return view('backend.pages.hr.journal_cotisation.select_by_company',compact('companies'));
    }

    public function exportToExcel(Request $request)
    {
        return Excel::download(new JournalCotisationExport, 'journal_cotisation.xlsx');
    }


}
