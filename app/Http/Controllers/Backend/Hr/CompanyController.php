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
use Illuminate\Support\Facades\Storage;
use App\Models\HrCompany;

class CompanyController extends Controller
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

    public function index()
    {
        $companies = DB::table('hr_companies')->orderBy('created_at','desc')->get();
        return view('backend.pages.hr.company.index',compact('companies'));
    }

    
    
    public function selectCompany()
    {
        if (is_null($this->user) || !$this->user->can('hr_employe.view')) {
            abort(403, 'Pardon!! vous n\'avez pas l\'autorisation de visualiser les employés! Mufise ico mubaza hamagara kuri 130 canke 122');
        }

        $companies = HrCompany::all();
        return view('backend.pages.hr.employe.select_company',compact('companies'));
    }

    public function create()
    {
        return view('backend.pages.hr.company.create');
    }

     public function store(Request $request)
    {
        //
        $request->validate([
            'name' => 'required',
            'nif' => 'required',
            'rc' => 'required',
            'commune' => 'required',
            'zone' => 'required',
            'quartier' => 'required',
            'rue' => 'required',

        ]);

        $storagepath = $request->file('logo')->store('public/logo');
        $fileName = basename($storagepath);

        $data = $request->all();
        $data['logo'] = $fileName;

        HrCompany::create($data);

        return redirect()->route('admin.hr-companies.index')->with('success', 'company has been created.');
    }



    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\HrCompany  $company
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
        $company = HrCompany::findOrFail($id);
        return view('backend.pages.hr.company.edit', compact('company'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\HrCompany  $company
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
        $request->validate([
            'name' => 'required',
            'nif' => 'required',
            'rc' => 'required',
            'commune' => 'required',
            'zone' => 'required',
            'quartier' => 'required',
            'rue' => 'required'

        ]);

        $company = HrCompany::findOrFail($id);

        $data = $request->all();

        if($request->hasFile('logo')){
            $file_path = "public/logo/".$company->logo;
            Storage::delete($file_path);

            $storagepath = $request->file('logo')->store('public/logo');
            $fileName = basename($storagepath);
            $data['logo'] = $fileName;

        }

        $company->fill($data);
        $company->save();

        return redirect()->route('admin.hr-companies.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Company  $company
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
        $company = HrCompany::findOrFail($id);
        $file_path = "public/logo/".$company->logo;
            Storage::delete($file_path);
        $company->delete();
        return redirect()->back();
    }
}
