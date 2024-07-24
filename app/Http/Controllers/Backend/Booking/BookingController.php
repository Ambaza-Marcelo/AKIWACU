<?php

namespace App\Http\Controllers\Backend\Booking;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Storage;
use App\Models\BookingBooking;
use App\Models\BookingBookingDetail;
use App\Models\BookingSalle;
use App\Models\BookingService;
use App\Models\BookingClient;
use App\Models\BookingTechnique;
use App\Models\BookingTechniqueDetail;
use App\Models\BookingTable;
use App\Models\KidnessSpace;
use App\Models\BreakFast;
use App\Models\SwimingPool;
use Carbon\Carbon;
use Validator;
use PDF;

class BookingController extends Controller
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

    public function indexSalle()
    {
        if (is_null($this->user) || !$this->user->can('booking.view')) {
            abort(403, 'Sorry !! You are Unauthorized to view any booking !');
        }

        $bookings = BookingBookingDetail::where('salle_id','!=','')->take(200)->orderBy('id','desc')->get();
        return view('backend.pages.booking.booking.index_salle', compact('bookings'));
    }

    public function indexService()
    {
        if (is_null($this->user) || !$this->user->can('booking.view')) {
            abort(403, 'Sorry !! You are Unauthorized to view any booking !');
        }

        $bookings = BookingBookingDetail::where('service_id','!=','')->take(200)->orderBy('booking_no','desc')->get();
        return view('backend.pages.booking.booking.index_service', compact('bookings'));
    }

    public function indexTable()
    {
        if (is_null($this->user) || !$this->user->can('booking.view')) {
            abort(403, 'Sorry !! You are Unauthorized to view any booking !');
        }

        $bookings = BookingBookingDetail::where('table_id','!=','')->take(200)->orderBy('id','desc')->get();
        return view('backend.pages.booking.booking.index_table', compact('bookings'));
    }

    public function indexKidnessSpace()
    {
        if (is_null($this->user) || !$this->user->can('booking.view')) {
            abort(403, 'Sorry !! You are Unauthorized to view any booking !');
        }

        $bookings = BookingBookingDetail::where('kidness_space_id','!=','')->take(200)->orderBy('id','desc')->get();
        return view('backend.pages.booking.booking.index_kidness_space', compact('bookings'));
    }

    public function indexSwimingPool()
    {
        if (is_null($this->user) || !$this->user->can('booking.view')) {
            abort(403, 'Sorry !! You are Unauthorized to view any booking !');
        }

        $bookings = BookingBookingDetail::where('swiming_pool_id','!=','')->take(200)->orderBy('id','desc')->get();
        return view('backend.pages.booking.booking.index_swiming_pool', compact('bookings'));
    }

    public function indexBreakFast()
    {
        if (is_null($this->user) || !$this->user->can('booking.view')) {
            abort(403, 'Sorry !! You are Unauthorized to view any booking !');
        }

        $bookings = BookingBookingDetail::where('breakfast_id','!=','')->take(200)->orderBy('id','desc')->get();
        return view('backend.pages.booking.booking.index_breakfast', compact('bookings'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function createSalle()
    {
        if (is_null($this->user) || !$this->user->can('booking.create')) {
            abort(403, 'Sorry !! You are Unauthorized to create any booking !');
        }

        $articles  = BookingSalle::orderBy('name','asc')->get();
        $techniques  = BookingTechnique::orderBy('name','asc')->get();
        $clients  = BookingClient::orderBy('customer_name','asc')->get();
        return view('backend.pages.booking.booking.create_salle', compact('articles','techniques','clients'));
    }

    public function createService()
    {
        if (is_null($this->user) || !$this->user->can('booking.create')) {
            abort(403, 'Sorry !! You are Unauthorized to create any booking !');
        }

        $articles  = BookingService::orderBy('name','asc')->get();
        $clients  = BookingClient::orderBy('customer_name','asc')->get();
        return view('backend.pages.booking.booking.create_service', compact('articles','clients'));
    }

    public function createTable()
    {
        if (is_null($this->user) || !$this->user->can('booking.create')) {
            abort(403, 'Sorry !! You are Unauthorized to create any booking !');
        }

        $articles  = BookingTable::orderBy('name','asc')->get();
        $clients  = BookingClient::orderBy('customer_name','asc')->get();
        return view('backend.pages.booking.booking.create_table', compact('articles','clients'));
    }

    public function createBreakFast()
    {
        if (is_null($this->user) || !$this->user->can('booking.create')) {
            abort(403, 'Sorry !! You are Unauthorized to create any booking !');
        }

        $articles  = BreakFast::orderBy('name','asc')->get();
        $techniques  = BookingTechnique::orderBy('name','asc')->get();
        $clients  = BookingClient::orderBy('customer_name','asc')->get();
        return view('backend.pages.booking.booking.create_breakfast', compact('articles','techniques','clients'));
    }

    public function createKidnessSpace()
    {
        if (is_null($this->user) || !$this->user->can('booking.create')) {
            abort(403, 'Sorry !! You are Unauthorized to create any booking !');
        }

        $articles  = KidnessSpace::orderBy('name','asc')->get();
        $techniques  = BookingTechnique::orderBy('name','asc')->get();
        $clients  = BookingClient::orderBy('customer_name','asc')->get();
        return view('backend.pages.booking.booking.create_kidness_space', compact('articles','techniques','clients'));
    }

    public function createSwimingPool()
    {
        if (is_null($this->user) || !$this->user->can('booking.create')) {
            abort(403, 'Sorry !! You are Unauthorized to create any booking !');
        }

        $articles  = SwimingPool::orderBy('name','asc')->get();
        $techniques  = BookingTechnique::orderBy('name','asc')->get();
        $clients  = BookingClient::orderBy('customer_name','asc')->get();
        return view('backend.pages.booking.booking.create_swiming_pool', compact('articles','techniques','clients'));
    }



    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function storeSalle(Request $request)
    {
        if (is_null($this->user) || !$this->user->can('booking.create')) {
            abort(403, 'Sorry !! You are Unauthorized to create any booking !');
        }

        $rules = array(
                'salle_id.*'  => 'required',
                'quantity.*'  => 'required',
                'technique_id.*'  => 'required',
                'description'  => 'required',
                'nom_referent'  => 'required',
                'telephone_referent'  => 'required',
                'courriel_referent'  => 'required',
                'type_evenement'  => 'required',
                'nombre_personnes'  => 'required',
                'date_debut'  => 'required',
                'date_fin'  => 'required',
                'booking_client_id'  => 'required',
            );

            $error = Validator::make($request->all(),$rules);

            if($error->fails()){
                return response()->json([
                    'error' => $error->errors()->all(),
                ]);
            }

            $date = $request->date;
            $booking_no = $request->booking_no;
            $booking_signature = $request->booking_signature;
            $description = $request->description;
            $statut_demandeur =$request->statut_demandeur; 
            $nom_demandeur = $request->nom_demandeur;
            $adresse_demandeur = $request->adresse_demandeur;
            $telephone_demandeur = $request->telephone_demandeur;
            $nom_referent =$request->nom_referent; 
            $telephone_referent = $request->telephone_referent;
            $quantity = $request->quantity;
            $courriel_referent = $request->courriel_referent;
            $type_evenement = $request->type_evenement;
            $nombre_personnes = $request->nombre_personnes;
            $date_debut =$request->date_debut; 
            $date_fin = $request->date_fin;
            $technique_id = $request->technique_id;
            $salle_id = $request->salle_id;
            $service_id =$request->service_id; 
            $booking_client_id =$request->booking_client_id; 
            $created_by = $this->user->name;


            $latest = BookingBooking::orderBy('id','desc')->first();
            if ($latest) {
               $booking_no = 'SAL' . (str_pad((int)$latest->id + 1, 4, '0', STR_PAD_LEFT)); 
            }else{
               $booking_no = 'SAL' . (str_pad((int)0 + 1, 4, '0', STR_PAD_LEFT));  
            }

            $order_signature = config('app.tin_number_company').Carbon::parse(Carbon::now())->format('YmdHis')."/".$booking_no;

            //create booking
            $booking = new BookingBooking();
            $booking->date = $date;
            $booking->booking_no = $booking_no;
            $booking->booking_signature = $booking_signature;
            $booking->description = $description;
            $booking->statut_demandeur = $statut_demandeur;
            $booking->nom_demandeur = $nom_demandeur;
            $booking->adresse_demandeur = $adresse_demandeur;
            $booking->telephone_demandeur = $telephone_demandeur;
            $booking->nom_referent = $nom_referent;
            $booking->telephone_referent = $telephone_referent;
            $booking->courriel_referent = $courriel_referent;
            $booking->type_evenement = $type_evenement;
            $booking->nombre_personnes = $nombre_personnes;
            $booking->date_debut = $date_debut;
            $booking->date_fin = $date_fin;
            $booking->booking_client_id = $booking_client_id;
            $booking->created_by = $created_by;
            $booking->save();
            //insert details of booking No.
            for( $count = 0; $count < count($salle_id); $count++ ){

                $selling_price = BookingSalle::where('id', $salle_id[$count])->value('selling_price');
                $total_amount_selling = $quantity[$count] * $selling_price;
                $data = array(
                    'salle_id' => $salle_id[$count],
                    'quantity' => $quantity[$count],
                    'selling_price' => $selling_price,
                    'booking_no' => $booking_no,
                    'date' => $date,
                    'description' => $description,
                    'statut_demandeur' => $statut_demandeur,
                    'nom_demandeur' => $nom_demandeur,
                    'adresse_demandeur' => $adresse_demandeur,
                    'telephone_demandeur' => $telephone_demandeur,
                    'nom_referent' => $nom_referent,
                    'telephone_referent' => $telephone_referent,
                    'courriel_referent' => $courriel_referent,
                    'type_evenement' => $type_evenement,
                    'nombre_personnes' => $nombre_personnes,
                    'date_debut' => $date_debut,
                    'date_fin' => $date_fin,
                    'booking_client_id' => $booking_client_id,
                    'total_amount_selling' => $total_amount_selling,
                    'created_by' => $created_by,
                    'booking_no' => $booking_no,
                    'booking_signature' => $booking_signature,
                    'technique_id' => $technique_id[$count],

                );
                $insert_data[] = $data;
            }

            for( $n = 0; $n < count($technique_id); $n++ ){

                $technique_data = array(
                    'booking_no' => $booking_no,
                    'booking_signature' => $booking_signature,
                    'technique_id' => $technique_id[$n],

                );
                $insert_technique_data[] = $technique_data;
        }

            BookingBookingDetail::insert($insert_data);
            BookingTechniqueDetail::insert($insert_technique_data);

        session()->flash('success', 'Booking has been sent successfuly!!');
        return redirect()->route('admin.booking-salles.index');
    }

    public function storeService(Request $request)
    {
        if (is_null($this->user) || !$this->user->can('booking.create')) {
            abort(403, 'Sorry !! You are Unauthorized to create any booking !');
        }

        $rules = array(
                'service_id.*'  => 'required',
                'quantity.*'  => 'required',
                //'technique_id.*'  => 'required',
                'description'  => 'required',
                'date_debut'  => 'required',
                'date_fin'  => 'required',
            );

            $error = Validator::make($request->all(),$rules);

            if($error->fails()){
                return response()->json([
                    'error' => $error->errors()->all(),
                ]);
            }

            $date = $request->date;
            $booking_no = $request->booking_no;
            $booking_signature = $request->booking_signature;
            $description = $request->description;
            $statut_demandeur =$request->statut_demandeur; 
            $nom_demandeur = $request->nom_demandeur;
            $adresse_demandeur = $request->adresse_demandeur;
            $telephone_demandeur = $request->telephone_demandeur;
            $nom_referent =$request->nom_referent; 
            $telephone_referent = $request->telephone_referent;
            $courriel_referent = $request->courriel_referent;
            $type_evenement = $request->type_evenement;
            $nombre_personnes = $request->nombre_personnes;
            $quantity = $request->quantity;
            $date_debut =$request->date_debut; 
            $date_fin = $request->date_fin;
            $technique_id = $request->technique_id;
            $service_id =$request->service_id; 
            $created_by = $this->user->name;


            $latest = BookingBooking::orderBy('id','desc')->first();
            if ($latest) {
               $booking_no = 'SER' . (str_pad((int)$latest->id + 1, 4, '0', STR_PAD_LEFT)); 
            }else{
               $booking_no = 'SER' . (str_pad((int)0 + 1, 4, '0', STR_PAD_LEFT));  
            }

            $order_signature = config('app.tin_number_company').Carbon::parse(Carbon::now())->format('YmdHis')."/".$booking_no;

            //create booking
            $booking = new BookingBooking();
            $booking->date = $date;
            $booking->booking_no = $booking_no;
            $booking->booking_signature = $booking_signature;
            $booking->description = $description;
            $booking->statut_demandeur = $statut_demandeur;
            $booking->nom_demandeur = $nom_demandeur;
            $booking->adresse_demandeur = $adresse_demandeur;
            $booking->telephone_demandeur = $telephone_demandeur;
            $booking->nom_referent = $nom_referent;
            $booking->telephone_referent = $telephone_referent;
            $booking->courriel_referent = $courriel_referent;
            $booking->type_evenement = $type_evenement;
            $booking->nombre_personnes = $nombre_personnes;
            $booking->date_debut = $date_debut;
            $booking->date_fin = $date_fin;
            $booking->created_by = $created_by;
            $booking->save();
            //insert details of booking No.
            for( $count = 0; $count < count($service_id); $count++ ){

                $selling_price = BookingService::where('id', $service_id[$count])->value('selling_price');
                $total_amount_selling = $quantity[$count] * $selling_price;
                $data = array(
                    'service_id' => $service_id[$count],
                    'quantity' => $quantity[$count],
                    'selling_price' => $selling_price,
                    'booking_no' => $booking_no,
                    'date' => $date,
                    'description' => $description,
                    'statut_demandeur' => $statut_demandeur,
                    'nom_demandeur' => $nom_demandeur,
                    'adresse_demandeur' => $adresse_demandeur,
                    'telephone_demandeur' => $telephone_demandeur,
                    'nom_referent' => $nom_referent,
                    'telephone_referent' => $telephone_referent,
                    'courriel_referent' => $courriel_referent,
                    'type_evenement' => $type_evenement,
                    'nombre_personnes' => $nombre_personnes,
                    'date_debut' => $date_debut,
                    'date_fin' => $date_fin,
                    'total_amount_selling' => $total_amount_selling,
                    'created_by' => $created_by,
                    'booking_signature' => $booking_signature,

                );
                $insert_data[] = $data;
            }

            BookingBookingDetail::insert($insert_data);

        session()->flash('success', 'Booking has been sent successfuly!!');
        return redirect()->route('admin.booking-services.index');
    }

    public function storeTable(Request $request)
    {
        if (is_null($this->user) || !$this->user->can('booking.create')) {
            abort(403, 'Sorry !! You are Unauthorized to create any booking !');
        }

        $rules = array(
                'table_id.*'  => 'required',
                'quantity.*'  => 'required',
                'technique_id.*'  => 'required',
                'description'  => 'required',
                'nom_referent'  => 'required',
                'telephone_referent'  => 'required',
                'courriel_referent'  => 'required',
                'type_evenement'  => 'required',
                'nombre_personnes'  => 'required',
                'date_debut'  => 'required',
                'date_fin'  => 'required',
            );

            $error = Validator::make($request->all(),$rules);

            if($error->fails()){
                return response()->json([
                    'error' => $error->errors()->all(),
                ]);
            }

            $date = $request->date;
            $booking_no = $request->booking_no;
            $booking_signature = $request->booking_signature;
            $description = $request->description;
            $statut_demandeur =$request->statut_demandeur; 
            $nom_demandeur = $request->nom_demandeur;
            $adresse_demandeur = $request->adresse_demandeur;
            $telephone_demandeur = $request->telephone_demandeur;
            $nom_referent =$request->nom_referent; 
            $telephone_referent = $request->telephone_referent;
            $courriel_referent = $request->courriel_referent;
            $type_evenement = $request->type_evenement;
            $nombre_personnes = $request->nombre_personnes;
            $date_debut =$request->date_debut; 
            $date_fin = $request->date_fin;
            $technique_id = $request->technique_id;
            $table_id =$request->table_id; 
            $created_by = $this->user->name;


            $latest = BookingBooking::orderBy('id','desc')->first();
            if ($latest) {
               $booking_no = 'TAB' . (str_pad((int)$latest->id + 1, 4, '0', STR_PAD_LEFT)); 
            }else{
               $booking_no = 'TAB' . (str_pad((int)0 + 1, 4, '0', STR_PAD_LEFT));  
            }

            $order_signature = config('app.tin_number_company').Carbon::parse(Carbon::now())->format('YmdHis')."/".$booking_no;

            //create booking
            $booking = new BookingBooking();
            $booking->date = $date;
            $booking->booking_no = $booking_no;
            $booking->booking_signature = $booking_signature;
            $booking->description = $description;
            $booking->statut_demandeur = $statut_demandeur;
            $booking->nom_demandeur = $nom_demandeur;
            $booking->adresse_demandeur = $adresse_demandeur;
            $booking->telephone_demandeur = $telephone_demandeur;
            $booking->nom_referent = $nom_referent;
            $booking->telephone_referent = $telephone_referent;
            $booking->courriel_referent = $courriel_referent;
            $booking->type_evenement = $type_evenement;
            $booking->nombre_personnes = $nombre_personnes;
            $booking->date_debut = $date_debut;
            $booking->date_fin = $date_fin;
            $booking->created_by = $created_by;
            $booking->save();
            //insert details of booking No.
            for( $count = 0; $count < count($table_id); $count++ ){
                $data = array(
                    'table_id' => $table_id[$count],
                    'quantity' => $quantity[$count],
                    'booking_no' => $booking_no,
                    'date' => $date,
                    'description' => $description,
                    'statut_demandeur' => $statut_demandeur,
                    'nom_demandeur' => $nom_demandeur,
                    'adresse_demandeur' => $adresse_demandeur,
                    'telephone_demandeur' => $telephone_demandeur,
                    'nom_referent' => $nom_referent,
                    'telephone_referent' => $telephone_referent,
                    'courriel_referent' => $courriel_referent,
                    'type_evenement' => $type_evenement,
                    'nombre_personnes' => $nombre_personnes,
                    'date_debut' => $date_debut,
                    'date_fin' => $date_fin,
                    'created_by' => $created_by,
                    'booking_signature' => $booking_signature,

                );
                $insert_data[] = $data;
            }

            BookingBookingDetail::insert($insert_data);

        session()->flash('success', 'Booking has been sent successfuly!!');
        return redirect()->route('admin.booking-tables.index');
    }

    public function storeKidnessSpace(Request $request)
    {
        if (is_null($this->user) || !$this->user->can('booking.create')) {
            abort(403, 'Sorry !! You are Unauthorized to create any booking !');
        }

        $rules = array(
                'kidness_space_id.*'  => 'required',
                'quantity.*'  => 'required',
                //'technique_id.*'  => 'required',
                'description'  => 'required',
                'date_debut'  => 'required',
                'date_fin'  => 'required',
            );

            $error = Validator::make($request->all(),$rules);

            if($error->fails()){
                return response()->json([
                    'error' => $error->errors()->all(),
                ]);
            }

            $date = $request->date;
            $booking_no = $request->booking_no;
            $booking_signature = $request->booking_signature;
            $description = $request->description;
            $statut_demandeur =$request->statut_demandeur; 
            $nom_demandeur = $request->nom_demandeur;
            $adresse_demandeur = $request->adresse_demandeur;
            $telephone_demandeur = $request->telephone_demandeur;
            $nom_referent =$request->nom_referent; 
            $telephone_referent = $request->telephone_referent;
            $courriel_referent = $request->courriel_referent;
            $type_evenement = $request->type_evenement;
            $nombre_personnes = $request->nombre_personnes;
            $quantity = $request->quantity;
            $date_debut =$request->date_debut; 
            $date_fin = $request->date_fin;
            $technique_id = $request->technique_id;
            $kidness_space_id =$request->kidness_space_id; 
            $created_by = $this->user->name;


            $latest = BookingBooking::orderBy('id','desc')->first();
            if ($latest) {
               $booking_no = 'KID' . (str_pad((int)$latest->id + 1, 4, '0', STR_PAD_LEFT)); 
            }else{
               $booking_no = 'KID' . (str_pad((int)0 + 1, 4, '0', STR_PAD_LEFT));  
            }

            $order_signature = config('app.tin_number_company').Carbon::parse(Carbon::now())->format('YmdHis')."/".$booking_no;

            //create booking
            $booking = new BookingBooking();
            $booking->date = $date;
            $booking->booking_no = $booking_no;
            $booking->booking_signature = $booking_signature;
            $booking->description = $description;
            $booking->statut_demandeur = $statut_demandeur;
            $booking->nom_demandeur = $nom_demandeur;
            $booking->adresse_demandeur = $adresse_demandeur;
            $booking->telephone_demandeur = $telephone_demandeur;
            $booking->nom_referent = $nom_referent;
            $booking->telephone_referent = $telephone_referent;
            $booking->courriel_referent = $courriel_referent;
            $booking->type_evenement = $type_evenement;
            $booking->nombre_personnes = $nombre_personnes;
            $booking->date_debut = $date_debut;
            $booking->date_fin = $date_fin;
            $booking->created_by = $created_by;
            $booking->save();
            //insert details of booking No.
            for( $count = 0; $count < count($kidness_space_id); $count++ ){

                $selling_price = KidnessSpace::where('id', $kidness_space_id[$count])->value('selling_price');
                $total_amount_selling = $quantity[$count] * $selling_price;
                $data = array(
                    'kidness_space_id' => $kidness_space_id[$count],
                    'quantity' => $quantity[$count],
                    'selling_price' => $selling_price,
                    'booking_no' => $booking_no,
                    'date' => $date,
                    'description' => $description,
                    'statut_demandeur' => $statut_demandeur,
                    'nom_demandeur' => $nom_demandeur,
                    'adresse_demandeur' => $adresse_demandeur,
                    'telephone_demandeur' => $telephone_demandeur,
                    'nom_referent' => $nom_referent,
                    'telephone_referent' => $telephone_referent,
                    'courriel_referent' => $courriel_referent,
                    'type_evenement' => $type_evenement,
                    'nombre_personnes' => $nombre_personnes,
                    'date_debut' => $date_debut,
                    'date_fin' => $date_fin,
                    'total_amount_selling' => $total_amount_selling,
                    'created_by' => $created_by,
                    'booking_signature' => $booking_signature,

                );
                $insert_data[] = $data;
            }

            BookingBookingDetail::insert($insert_data);

        session()->flash('success', 'Booking has been sent successfuly!!');
        return redirect()->route('admin.booking-kidness-space.index');
    }

    public function storeSwimingPool(Request $request)
    {
        if (is_null($this->user) || !$this->user->can('booking.create')) {
            abort(403, 'Sorry !! You are Unauthorized to create any booking !');
        }

        $rules = array(
                'swiming_pool_id.*'  => 'required',
                'quantity.*'  => 'required',
                //'technique_id.*'  => 'required',
                'description'  => 'required',
                'date_debut'  => 'required',
                'date_fin'  => 'required',
            );

            $error = Validator::make($request->all(),$rules);

            if($error->fails()){
                return response()->json([
                    'error' => $error->errors()->all(),
                ]);
            }

            $date = $request->date;
            $booking_no = $request->booking_no;
            $booking_signature = $request->booking_signature;
            $description = $request->description;
            $statut_demandeur =$request->statut_demandeur; 
            $nom_demandeur = $request->nom_demandeur;
            $adresse_demandeur = $request->adresse_demandeur;
            $telephone_demandeur = $request->telephone_demandeur;
            $nom_referent =$request->nom_referent; 
            $telephone_referent = $request->telephone_referent;
            $courriel_referent = $request->courriel_referent;
            $type_evenement = $request->type_evenement;
            $nombre_personnes = $request->nombre_personnes;
            $quantity = $request->quantity;
            $date_debut =$request->date_debut; 
            $date_fin = $request->date_fin;
            $technique_id = $request->technique_id;
            $swiming_pool_id =$request->swiming_pool_id; 
            $created_by = $this->user->name;


            $latest = BookingBooking::latest()->first();
            if ($latest) {
               $booking_no = 'PIS' . (str_pad((int)$latest->id + 1, 4, '0', STR_PAD_LEFT)); 
            }else{
               $booking_no = 'PIS' . (str_pad((int)0 + 1, 4, '0', STR_PAD_LEFT));  
            }

            $order_signature = config('app.tin_number_company').Carbon::parse(Carbon::now())->format('YmdHis')."/".$booking_no;

            //create booking
            $booking = new BookingBooking();
            $booking->date = $date;
            $booking->booking_no = $booking_no;
            $booking->booking_signature = $booking_signature;
            $booking->description = $description;
            $booking->statut_demandeur = $statut_demandeur;
            $booking->nom_demandeur = $nom_demandeur;
            $booking->adresse_demandeur = $adresse_demandeur;
            $booking->telephone_demandeur = $telephone_demandeur;
            $booking->nom_referent = $nom_referent;
            $booking->telephone_referent = $telephone_referent;
            $booking->courriel_referent = $courriel_referent;
            $booking->type_evenement = $type_evenement;
            $booking->nombre_personnes = $nombre_personnes;
            $booking->date_debut = $date_debut;
            $booking->date_fin = $date_fin;
            $booking->created_by = $created_by;
            $booking->save();
            //insert details of booking No.
            for( $count = 0; $count < count($swiming_pool_id); $count++ ){

                $selling_price = SwimingPool::where('id', $swiming_pool_id[$count])->value('selling_price');
                $total_amount_selling = $quantity[$count] * $selling_price;
                $data = array(
                    'swiming_pool_id' => $swiming_pool_id[$count],
                    'quantity' => $quantity[$count],
                    'selling_price' => $selling_price,
                    'booking_no' => $booking_no,
                    'date' => $date,
                    'description' => $description,
                    'statut_demandeur' => $statut_demandeur,
                    'nom_demandeur' => $nom_demandeur,
                    'adresse_demandeur' => $adresse_demandeur,
                    'telephone_demandeur' => $telephone_demandeur,
                    'nom_referent' => $nom_referent,
                    'telephone_referent' => $telephone_referent,
                    'courriel_referent' => $courriel_referent,
                    'type_evenement' => $type_evenement,
                    'nombre_personnes' => $nombre_personnes,
                    'date_debut' => $date_debut,
                    'date_fin' => $date_fin,
                    'total_amount_selling' => $total_amount_selling,
                    'created_by' => $created_by,
                    'booking_signature' => $booking_signature,

                );
                $insert_data[] = $data;
            }

            BookingBookingDetail::insert($insert_data);

        session()->flash('success', 'Booking has been sent successfuly!!');
        return redirect()->route('admin.booking-swiming-pool.index');
    }

    public function storeBreakFast(Request $request)
    {
        if (is_null($this->user) || !$this->user->can('booking.create')) {
            abort(403, 'Sorry !! You are Unauthorized to create any booking !');
        }

        $rules = array(
                'breakfast_id.*'  => 'required',
                'quantity.*'  => 'required',
                //'technique_id.*'  => 'required',
                'description'  => 'required',
                'date_debut'  => 'required',
                'date_fin'  => 'required',
            );

            $error = Validator::make($request->all(),$rules);

            if($error->fails()){
                return response()->json([
                    'error' => $error->errors()->all(),
                ]);
            }

            $date = $request->date;
            $booking_no = $request->booking_no;
            $booking_signature = $request->booking_signature;
            $description = $request->description;
            $statut_demandeur =$request->statut_demandeur; 
            $nom_demandeur = $request->nom_demandeur;
            $adresse_demandeur = $request->adresse_demandeur;
            $telephone_demandeur = $request->telephone_demandeur;
            $nom_referent =$request->nom_referent; 
            $telephone_referent = $request->telephone_referent;
            $courriel_referent = $request->courriel_referent;
            $type_evenement = $request->type_evenement;
            $nombre_personnes = $request->nombre_personnes;
            $quantity = $request->quantity;
            $date_debut =$request->date_debut; 
            $date_fin = $request->date_fin;
            $technique_id = $request->technique_id;
            $breakfast_id =$request->breakfast_id; 
            $created_by = $this->user->name;


            $latest = BookingBooking::orderBy('id','desc')->first();
            if ($latest) {
               $booking_no = 'BRE' . (str_pad((int)$latest->id + 1, 4, '0', STR_PAD_LEFT)); 
            }else{
               $booking_no = 'BRE' . (str_pad((int)0 + 1, 4, '0', STR_PAD_LEFT));  
            }

            $order_signature = config('app.tin_number_company').Carbon::parse(Carbon::now())->format('YmdHis')."/".$booking_no;

            //create booking
            $booking = new BookingBooking();
            $booking->date = $date;
            $booking->booking_no = $booking_no;
            $booking->booking_signature = $booking_signature;
            $booking->description = $description;
            $booking->statut_demandeur = $statut_demandeur;
            $booking->nom_demandeur = $nom_demandeur;
            $booking->adresse_demandeur = $adresse_demandeur;
            $booking->telephone_demandeur = $telephone_demandeur;
            $booking->nom_referent = $nom_referent;
            $booking->telephone_referent = $telephone_referent;
            $booking->courriel_referent = $courriel_referent;
            $booking->type_evenement = $type_evenement;
            $booking->nombre_personnes = $nombre_personnes;
            $booking->date_debut = $date_debut;
            $booking->date_fin = $date_fin;
            $booking->created_by = $created_by;
            $booking->save();
            //insert details of booking No.
            for( $count = 0; $count < count($breakfast_id); $count++ ){

                $selling_price = BreakFast::where('id', $breakfast_id[$count])->value('selling_price');
                $total_amount_selling = $quantity[$count] * $selling_price;
                $data = array(
                    'breakfast_id' => $breakfast_id[$count],
                    'quantity' => $quantity[$count],
                    'selling_price' => $selling_price,
                    'booking_no' => $booking_no,
                    'date' => $date,
                    'description' => $description,
                    'statut_demandeur' => $statut_demandeur,
                    'nom_demandeur' => $nom_demandeur,
                    'adresse_demandeur' => $adresse_demandeur,
                    'telephone_demandeur' => $telephone_demandeur,
                    'nom_referent' => $nom_referent,
                    'telephone_referent' => $telephone_referent,
                    'courriel_referent' => $courriel_referent,
                    'type_evenement' => $type_evenement,
                    'nombre_personnes' => $nombre_personnes,
                    'date_debut' => $date_debut,
                    'date_fin' => $date_fin,
                    'total_amount_selling' => $total_amount_selling,
                    'created_by' => $created_by,
                    'booking_signature' => $booking_signature,

                );
                $insert_data[] = $data;
            }

            BookingBookingDetail::insert($insert_data);

        session()->flash('success', 'Booking has been sent successfuly!!');
        return redirect()->route('admin.booking-breakfast.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($booking_no)
    {
        //
         $data = BookingBooking::where('booking_no', $booking_no)->first();
         $techniques = BookingTechniqueDetail::where('booking_no', $booking_no)->get();
         $datas = BookingBookingDetail::where('booking_no', $booking_no)->get();
         return view('backend.pages.booking.booking.show', compact('data','datas','booking_no','techniques'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (is_null($this->user) || !$this->user->can('booking.edit')) {
            abort(403, 'Sorry !! You are Unauthorized to edit any order !');
        }

        $order = BookingBooking::find($id);
        $articles  = FoodItem::where('status','RESTAURANT')->orderBy('name','asc')->get();
        $employes  = Supplier::all();
        return view('backend.pages.booking.booking.edit', compact('order','employes','articles'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        if (is_null($this->user) || !$this->user->can('booking.edit')) {
            abort(403, 'Sorry !! You are Unauthorized to edit any order !');
        }

        
    }

    public function validateBooking($booking_no)
    {
       if (is_null($this->user) || !$this->user->can('booking.validate')) {
            abort(403, 'Sorry !! You are Unauthorized to validate any booking !');
        }

        $data = BookingBookingDetail::where('booking_no',$booking_no)->first();

        if (!empty($data->salle_id)) {
            $salle_id = BookingBookingDetail::where('booking_no',$booking_no)->value('salle_id');
            BookingBooking::where('booking_no', '=', $booking_no)
                ->update(['status' => 1,'validated_by' => $this->user->name]);
            BookingBookingDetail::where('booking_no', '=', $booking_no)
                ->update(['status' => 1,'validated_by' => $this->user->name]);
            BookingSalle::where('id', '=', $salle_id)
                ->update(['status' => 1]);
        }elseif(!empty($data->service_id)){
            $service_id = BookingBookingDetail::where('booking_no',$booking_no)->value('service_id');
            BookingBooking::where('booking_no', '=', $booking_no)
                ->update(['status' => 1,'validated_by' => $this->user->name]);
            BookingBookingDetail::where('booking_no', '=', $booking_no)
                ->update(['status' => 1,'validated_by' => $this->user->name]);
            BookingService::where('id', '=', $service_id)
                ->update(['status' => 1]);
        }elseif(!empty($data->breakfast_id)){
            $breakfast_id = BookingBookingDetail::where('booking_no',$booking_no)->value('breakfast_id');
            BookingBooking::where('booking_no', '=', $booking_no)
                ->update(['status' => 1,'validated_by' => $this->user->name]);
            BookingBookingDetail::where('booking_no', '=', $booking_no)
                ->update(['status' => 1,'validated_by' => $this->user->name]);
            BookingService::where('id', '=', $breakfast_id)
                ->update(['status' => 1]);
        }elseif(!empty($data->kidness_space_id)){
            $kidness_space_id = BookingBookingDetail::where('booking_no',$booking_no)->value('kidness_space_id');
            BookingBooking::where('booking_no', '=', $booking_no)
                ->update(['status' => 1,'validated_by' => $this->user->name]);
            BookingBookingDetail::where('booking_no', '=', $booking_no)
                ->update(['status' => 1,'validated_by' => $this->user->name]);
            BookingService::where('id', '=', $kidness_space_id)
                ->update(['status' => 1]);
        }elseif(!empty($data->swiming_pool_id)){
            $swiming_pool_id = BookingBookingDetail::where('booking_no',$booking_no)->value('swiming_pool_id');
            BookingBooking::where('booking_no', '=', $booking_no)
                ->update(['status' => 1,'validated_by' => $this->user->name]);
            BookingBookingDetail::where('booking_no', '=', $booking_no)
                ->update(['status' => 1,'validated_by' => $this->user->name]);
            BookingService::where('id', '=', $swiming_pool_id)
                ->update(['status' => 1]);
        }else{
            $table_id = BookingBookingDetail::where('booking_no',$booking_no)->value('table_id');
            BookingBooking::where('booking_no', '=', $booking_no)
                ->update(['status' => 1,'validated_by' => $this->user->name]);
            BookingBookingDetail::where('booking_no', '=', $booking_no)
                ->update(['status' => 1,'validated_by' => $this->user->name]);;
        }

        session()->flash('success', 'booking has been validated !!');
        return back();
    }

    public function reject($booking_no)
    {
       if (is_null($this->user) || !$this->user->can('booking.reject')) {
            abort(403, 'Sorry !! You are Unauthorized to reject any order !');
        }

        BookingBooking::where('booking_no', '=', $booking_no)
                ->update(['status' => -1,'rejected_by' => $this->user->name]);
            BookingBookingDetail::where('booking_no', '=', $booking_no)
                ->update(['status' => -1,'rejected_by' => $this->user->name]);

        session()->flash('success', 'Booking has been rejected !!');
        return back();
    }

    public function reset($booking_no)
    {
       if (is_null($this->user) || !$this->user->can('booking.reset')) {
            abort(403, 'Sorry !! You are Unauthorized to reset any order !');
        }

        BookingBooking::where('booking_no', '=', $booking_no)
                ->update(['status' => 0,'reseted_by' => $this->user->name]);
            BookingBookingDetail::where('booking_no', '=', $booking_no)
                ->update(['status' => 0,'reseted_by' => $this->user->name]);

        session()->flash('success', 'Booking has been reseted !!');
        return back();
    }


    public function htmlPdf($booking_no)
    {
        if (is_null($this->user) || !$this->user->can('booking.create')) {
            abort(403, 'Sorry !! You are Unauthorized!');
        }

        $setting = DB::table('settings')->orderBy('created_at','desc')->first();
        $description = BookingBooking::where('booking_no', $booking_no)->value('description');
        $booking_signature = BookingBooking::where('booking_no', $booking_no)->value('booking_signature');
        $date = BookingBooking::where('booking_no', $booking_no)->value('created_at');
        $data = BookingBooking::where('booking_no', $booking_no)->first();

           $booking_no = BookingBooking::where('booking_no', $booking_no)->value('booking_no');

           $datas = BookingBookingDetail::where('booking_no', $booking_no)->get();
           $pdf = PDF::loadView('backend.pages.booking.document.demande_reservation',compact('datas','booking_no','setting','description','booking_signature','date','data'));//->setPaper('a6', 'portrait');

           Storage::put('public/reservation/'.$booking_no.'.pdf', $pdf->output());

           // download pdf file
           return $pdf->download($booking_no.'.pdf'); 
        
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($booking_no)
    {
        if (is_null($this->user) || !$this->user->can('booking.delete')) {
            abort(403, 'Sorry !! You are Unauthorized to delete any order !');
        }

        $order = BookingBooking::where('booking_no',$booking_no)->first();
        if (!is_null($order)) {
            $order->delete();
            BookingBookingDetail::where('booking_no',$booking_no)->delete();
        }

        session()->flash('success', 'Booking has been deleted !!');
        return back();
    }
}
