<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\FoodItem;
use App\Models\Drink;
use App\Models\BarristItem;
use App\Models\BartenderItem;
use App\Models\BookingSalle;
use App\Models\BookingService;

class WelcomeController extends Controller
{
    //

    public function list(){

    	return view('choose');
    }

    public function food(Request $request){

        if ($request->ajax()) {
            return FoodItem::select("name as value", "id")
                    ->where('name', 'LIKE', '%'. $request->get('search'). '%')->where('name','NOT LIKE','%STAFF%')->where('selling_price','>',0)
                    ->get();
        }
        
    	$restaurations = FoodItem::where('name','NOT LIKE','%STAFF%')->where('selling_price','>',0)->orderBy('name')->get();

    	return view('food',compact('restaurations'));
    }

    public function drink(Request $request){

        if ($request->ajax()) {
            return Drink::select("name as value", "id")
                    ->where('name', 'LIKE', '%'. $request->get('search'). '%')->where('name','NOT LIKE','%STAFF%')->where('selling_price','>',0)
                    ->get();
        }

    	$drinks = Drink::where('name','NOT LIKE','%STAFF%')->where('selling_price','>',0)->orderBy('name')->get();

    	return view('drink',compact('drinks'));
    }

    public function search(Request $request){

        $key = $request->query('name');
        $type = $request->query('type');

        if ($type == "DRINK") {
            $drinks = Drink::where('name', 'LIKE', '%'. $key. '%')->where('name','NOT LIKE','%STAFF%')->where('selling_price','>',0)
                    ->get();
            return view('drink',compact('drinks'));
        }elseif ($type == "BARRISTA") {
            $barrists = BarristItem::where('name', 'LIKE', '%'. $key. '%')
                    ->get();
            return view('barrista',compact('barrists'));
        }elseif ($type == 'FOOD') {
            $restaurations = FoodItem::where('name', 'LIKE', '%'. $key. '%')->where('name','NOT LIKE','%STAFF%')->where('selling_price','>',0)
                    ->get();
            return view('food',compact('restaurations'));
        }elseif ($type == "SERVICE") {
            $services = BookingService::where('name', 'LIKE', '%'. $key. '%')
                    ->get();
            return view('eden',compact('services'));
        }elseif ($type == "SALLE") {
            $salles = BookingSalle::where('name', 'LIKE', '%'. $key. '%')
                    ->get();
            return view('salle',compact('salles'));
        }elseif ($type == "BARTENDER") {
            $bartenders = BartenderItem::where('name', 'LIKE', '%'. $key. '%')
                    ->get();
            return view('bartender',compact('bartenders'));
        }
    
    }

    public function barrista(Request $request){

        if ($request->ajax()) {
            return BarristItem::select("name as value", "id")
                    ->where('name', 'LIKE', '%'. $request->get('search'). '%')
                    ->get();
        }
    	$barrists = BarristItem::orderBy('name')->get();

    	return view('barrista',compact('barrists'));
    }

    public function bartender(Request $request){

        if ($request->ajax()) {
            return BartenderItem::select("name as value", "id")
                    ->where('name', 'LIKE', '%'. $request->get('search'). '%')
                    ->get();
        }
        $bartenders = BartenderItem::orderBy('name')->get();

        return view('bartender',compact('bartenders'));
    }

    public function eden(Request $request){
        if ($request->ajax()) {
            return BookingService::select("name as value", "id")
                    ->where('name', 'LIKE', '%'. $request->get('search'). '%')
                    ->get();
        }
    	$services = BookingService::orderBy('name')->get();

    	return view('eden',compact('services'));
    }

    public function salle(Request $request){
        if ($request->ajax()) {
            return BookingSalle::select("name as value", "id")
                    ->where('name', 'LIKE', '%'. $request->get('search'). '%')
                    ->get();
        }
    	$salles = BookingSalle::orderBy('name')->get();

    	return view('salle',compact('salles'));
    }
}
