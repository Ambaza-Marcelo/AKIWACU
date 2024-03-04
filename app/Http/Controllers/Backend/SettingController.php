<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use \App\Models\Setting;

class SettingController extends Controller
{
    //
     public function index()
    {
    	$settings = DB::table('settings')->orderBy('created_at','desc')->get();
    	return view('backend.pages.setting.index',compact('settings'));
    }
    public function create()
    {
    	return view('backend.pages.setting.create');
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
            'logo' => 'required|mimes:jpeg,jpg,png,svg|max:2048',

        ]);

        $storagepath = $request->file('logo')->store('public/logo');
        $fileName = basename($storagepath);

        $data = $request->all();
        $data['logo'] = $fileName;

        Setting::create($data);

        return redirect()->route('admin.settings.index')->with('success', 'setting has been created.');
    }



    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Setting  $setting
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
        $setting = Setting::findOrFail($id);
        return view('backend.pages.setting.edit', compact('setting'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Setting  $setting
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

        $setting = Setting::findOrFail($id);

        $data = $request->all();

        if($request->hasFile('logo')){
            $file_path = "public/logo/".$setting->logo;
            Storage::delete($file_path);

            $storagepath = $request->file('logo')->store('public/logo');
            $fileName = basename($storagepath);
            $data['logo'] = $fileName;

        }

        $setting->fill($data);
        $setting->save();

        return redirect()->route('admin.settings.index');
    }

    public function postDbBackUp () {
    $now =  Carbon::now()->format("Y-m-d-H-m-i").'-backup.sql';
    try {
        Artisan::call('db:mssms',
            [
                '--database' => 'mysql',
                '--destination' => 'G:\Musumba',
                '--destinationPath' =>$now,
                '--compression' => 'gzip'
            ]
        );
    }
    catch(\Exception $e) {
        return Response::json([
            'success' => false,
            'errors' => ""
        ], 400);
    }
    return Response::json([
        'success' => true,
        'message' => 'success'
    ]);

}

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Setting  $setting
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
        $setting = Setting::findOrFail($id);
        $file_path = "public/logo/".$setting->logo;
            Storage::delete($file_path);
        $setting->delete();
        return redirect()->back();
    }


    public function documentation(){
        return view('backend.pages.documentation');
    }
}
