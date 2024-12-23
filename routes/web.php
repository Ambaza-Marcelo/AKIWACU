<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Route::get('/', function () {
//     return view('welcome');
// });

Auth::routes();

Route::get('/', 'HomeController@redirectAdmin')->name('index');
Route::get('/home', 'HomeController@index')->name('home');
Route::get('welcome-to-eden-garden-resort','WelcomeController@list')->name('welcome');
Route::get('MENU-BOISSONS','WelcomeController@drink')->name('menu-boissons');
Route::get('SEARCH','WelcomeController@search')->name('search');
Route::get('MENU-CUISINE','WelcomeController@food')->name('menu-cuisine');
Route::get('MENU-BARRISTA','WelcomeController@barrista')->name('menu-barrista');
Route::get('MENU-EDEN-GARDEN','WelcomeController@eden')->name('menu-eden');
Route::get('SALLES-DE-CONFERENCES','WelcomeController@salle')->name('salle-conferences');
Route::get('MENU-BARTENDER','WelcomeController@bartender')->name('menu-bartender');

/**
 * Admin routes
 */
Route::group(['prefix' => 'admin'], function () {
    Route::get('/', 'Backend\DashboardController@index')->name('admin.dashboard');
    Route::resource('roles', 'Backend\RolesController', ['names' => 'admin.roles']);
    Route::resource('users', 'Backend\UsersController', ['names' => 'admin.users']);
    Route::resource('admins', 'Backend\AdminsController', ['names' => 'admin.admins']);

    //ebms_api
    Route::get('ebms_api/getLogin', 'Backend\FactureController@getLogin')->name('ebms_api.getLogin');
    Route::get('ebms_api/invoices/index', 'Backend\FactureController@index')->name('ebms_api.invoices.index');
    Route::get('ebms_api/invoices/list/all', 'Backend\FactureController@listAll')->name('ebms_api.invoices.listAll');
    Route::get('ebms_api/facture/create/{order_no}', 'Backend\FactureController@create')->name('ebms_api.invoices.create');
    Route::get('ebms_api/facture/create-by-table/{table_id}', 'Backend\FactureController@createByTable')->name('ebms_api.invoices.create-by-table');
    Route::get('ebms_api/facture/edit/{invoice_number}', 'Backend\FactureController@edit')->name('ebms_api.invoices.edit');
    Route::post('eBMS-facture-boisson/store', 'Backend\FactureController@storeDrink')->name('ebms_api-facture-boisson.store');
    Route::post('eBMS-facture-barrist/store', 'Backend\FactureController@storeBarrist')->name('ebms_api-facture-barrist.store');
    Route::post('eBMS-facture-bartender/store', 'Backend\FactureController@storeBartender')->name('ebms_api-facture-bartender.store');
    Route::post('eBMS-facture-cuisine/store', 'Backend\FactureController@storeFood')->name('ebms_api-facture-cuisine.store');
    Route::put('eBMS-facture-cuisine/update/{invoice_number}', 'Backend\FactureRestaurantController@update')->name('ebms_api-facture-cuisine.update');
    Route::post('eBMS-facture-booking/store', 'Backend\FactureController@storeBooking')->name('ebms_api-facture-booking.store');
    Route::put('eBMS-facture-boisson/update/{invoice_number}', 'Backend\FactureController@update')->name('ebms_api-facture-boisson.update');

    Route::delete('ebms_api/facture/destroy/{invoice_number}', 'Backend\FactureController@destroy')->name('ebms_api.destroy');
    Route::put('eBms/facture-boisson/valider-facture/{invoice_number}','Backend\FactureController@validerFactureBoisson')->name('admin.facture-boisson.validate');
    Route::put('eBms/facture-cuisine/valider-facture/{invoice_number}','Backend\FactureController@validerFactureCuisine')->name('admin.facture-cuisine.validate');
    Route::put('eBms/facture-barrist/valider-facture/{invoice_number}','Backend\FactureController@validerFactureBarrist')->name('admin.facture-barrist.validate');
    Route::put('eBms/facture-bartender/valider-facture/{invoice_number}','Backend\FactureController@validerFactureBartender')->name('admin.facture-bartender.validate');
    Route::put('eBms/facture-booking/valider-facture/{invoice_number}','Backend\FactureController@validerFactureBooking')->name('admin.facture-booking.validate');



    Route::get('Akiwacu/voir-facture-a-valider-cash/{invoice_number}','Backend\FactureController@voirFactureCash')->name('admin.voir-facture.cash');
    Route::get('Akiwacu/voir-facture-a-valider/{invoice_number}','Backend\FactureController@voirFactureCredit')->name('admin.voir-facture.credit');

    Route::put('eBms/facture-boisson/valider-facture-a-credit/{invoice_number}','Backend\FactureController@validerFactureBoissonCredit')->name('admin.facture-boisson.valider-credit');
    Route::put('eBms/facture-cuisine/valider-facture-a-credit/{invoice_number}','Backend\FactureController@validerFactureCuisineCredit')->name('admin.facture-cuisine.valider-credit');
    Route::put('eBms/facture-barrist/valider-facture-a-credit/{invoice_number}','Backend\FactureController@validerFactureBarristCredit')->name('admin.facture-barrist.valider-credit');
    Route::put('eBms/facture-bartender/valider-facture-a-credit/{invoice_number}','Backend\FactureController@validerFactureBartenderCredit')->name('admin.facture-bartender.valider-credit');
    Route::put('eBms/facture-booking/valider-facture-a-credit/{invoice_number}','Backend\FactureController@validerFactureBookingCredit')->name('admin.facture-booking.valider-credit');


    Route::get('EBMS/voir-facture-a-credit','Backend\FactureRestaurantController@voirFactureAcredit')->name('admin.credit-invoices.list');
    Route::get('EBMS/payer-facture-a-credit/{invoice_number}','Backend\FactureRestaurantController@voirFacturePayercredit')->name('admin.payer-facture.credit');

    Route::get('EBMS/voir-facture-credit-payes','Backend\FactureRestaurantController@creditPayes')->name('admin.credit-payes.list');

    Route::get('EBMS/exporter-en-excel-facture-credits','Backend\FactureRestaurantController@creditExportToExcel')->name('admin.exporter-en-excel-credits');
    Route::get('EBMS/exporter-en-excel-facture-recouvres','Backend\FactureRestaurantController@recouvrementExportToExcel')->name('admin.exporter-en-excel-recouvrement');

    Route::get('EBMS/voir-chiffre-affaires','Backend\FactureRestaurantController@chiffreAffaire')->name('admin.voir-chiffre-affaires');
    Route::get('EBMS/exporter-chiffre-affaire','Backend\FactureRestaurantController@exporterChiffreAffaire')->name('admin.exporter-chiffre-affaire');
    Route::get('EBMS/facture-globale/client','Backend\FactureRestaurantController@factureGlobale')->name('admin.facture-globale.client');
    Route::get('EBMS/exporter-en-excel-chiffre-affaire','Backend\FactureRestaurantController@exporterChiffreAffaireEnExcel')->name('admin.exporter-en-excel-chiffre-affaire');
    Route::get('EBMS/exporter-en-excel-credit','Backend\FactureRestaurantController@exporterCreditEnExcel')->name('admin.exporter-en-excel-credit');
    Route::get('EBMS/exporter-en-excel-cash','Backend\FactureRestaurantController@exporterCashEnExcel')->name('admin.exporter-en-excel-cash');

    Route::get('EBMS/exporter-facture-annule','Backend\FactureRestaurantController@exporterFactureAnnule')->name('admin.exporter-facture-annule');
    Route::get('EBMS/exporter-facture-encours','Backend\FactureRestaurantController@exporterFactureEncours')->name('admin.exporter-facture-encours');

    Route::put('eBms/payer-facture-a-credit/{invoice_number}','Backend\FactureController@payerCredit')->name('admin.facture-credit.payer');
    Route::put('eBms/valider-facture-paye','Backend\FactureRestaurantController@validerPaye')->name('admin.valider-facture-paye');


    Route::get('EBMS/voir-facture-a-annuler/{invoice_number}','Backend\FactureController@voirFactureAnnuler')->name('admin.voir-facture.reset');
    Route::put('EBMS/facture/annuler-facture/{invoice_number}','Backend\FactureController@annulerFacture')->name('admin.facture.reset');
    Route::put('EBMS/facture/valider-annuler-facture/{invoice_number}','Backend\FactureController@validerAnnulerFacture')->name('admin.facture.validate-reset');
    Route::get('EBMS/facture/imprimer/{invoice_number}','Backend\FactureController@facture')->name('admin.facture.imprimer');
    Route::get('EBMS/facture-brouillon/imprimer/{invoice_number}','Backend\FactureController@factureBrouillon')->name('admin.facture-brouillon.imprimer');
    Route::get('EBMS/facture/show/{invoice_number}','Backend\FactureController@show')->name('admin.facture.show');

    Route::get('ebms_api/ajouter-facture/{invoice_number}', 'Backend\FactureController@transfer')->name('ebms_api.transfer');
    Route::get('ebms_api/annuler-facture/{invoice_number}', 'Backend\FactureController@getCancelInvoice')->name('admin.facture.cancel');

    Route::get('EBMS/rapport-facture-credit','Backend\FactureRestaurantController@rapportCredit')->name('admin.rapport-facture-credit');
    Route::get('EBMS/rapport-facture-credit-paye','Backend\FactureRestaurantController@rapportCreditPaye')->name('admin.rapport-facture-credit-paye');

    Route::get('EBMS/facture-rapport-boisson','Backend\FactureController@rapportBoisson')->name('admin.facture-rapport.boisson');
    Route::get('EBMS/facture-rapport-nourriture','Backend\FactureRestaurantController@rapportNourriture')->name('admin.facture-rapport.nourriture');
    Route::get('EBMS/facture-rapport-barrist','Backend\FactureBarristController@rapportBarrist')->name('admin.facture-rapport.barrist');
    Route::get('EBMS/facture-rapport-bartender','Backend\FactureBartenderController@rapportBartender')->name('admin.facture-rapport.bartender');
    Route::get('EBMS/facture-rapport-reservation','Backend\FactureBookingController@rapportReservation')->name('admin.facture-rapport.reservation');

    Route::get('EBMS/transfert-rapport-boisson','Backend\DrinkTransferController@rapportBoisson')->name('admin.transfert-rapport.boisson');
    Route::get('EBMS/reception-rapport-boisson','Backend\DrinkReceptionController@rapportBoisson')->name('admin.reception-rapport.boisson');

    //note de credit

    Route::get('eBMS-note-de-credit/index', 'Backend\NoteCreditController@index')->name('admin.note-de-credit.index');

    Route::get('eBMS-note-de-credit/show/{invoice_number}', 'Backend\NoteCreditController@show')->name('admin.note-de-credit.show');

    Route::get('EBMS/boissons-faire-note-de-credit/{invoice_number}','Backend\NoteCreditController@noteCreditBoisson')->name('admin.boissons-note-de-credit.create');
    Route::get('EBMS/nourritures-faire-note-de-credit/{invoice_number}','Backend\NoteCreditController@noteCreditNourriture')->name('admin.nourritures-note-de-credit.create');
    Route::get('EBMS/barrista-faire-note-de-credit/{invoice_number}','Backend\NoteCreditController@noteCreditbarrista')->name('admin.barrista-note-de-credit.create');
    Route::get('EBMS/bartender-faire-note-de-credit/{invoice_number}','Backend\NoteCreditController@noteCreditBartender')->name('admin.bartender-note-de-credit.create');
    Route::get('EBMS/booking-faire-note-de-credit/{invoice_number}','Backend\NoteCreditController@noteCreditBooking')->name('admin.booking-note-de-credit.create');


    Route::get('EBMS/note-de-credit/{invoice_number}','Backend\NoteCreditController@facture')->name('admin.note-de-credit.facture');


    Route::post('eBMS-boissons-note-de-credit/store', 'Backend\NoteCreditController@storeDrink')->name('admin.boissons-note-de-credit.store');
    Route::post('eBMS-barrista-note-de-credit/store', 'Backend\NoteCreditController@storeBarrist')->name('admin.barrista-note-de-credit.store');
    Route::post('eBMS-bartender-note-de-credit/store', 'Backend\NoteCreditController@storeBartender')->name('admin.bartender-note-de-credit.store');
    Route::post('eBMS-nourritures-note-de-credit/store', 'Backend\NoteCreditController@storeFood')->name('admin.nourritures-note-de-credit.store');
    Route::post('eBMS-booking-note-de-credit/store', 'Backend\NoteCreditController@storeBooking')->name('admin.booking-note-de-credit.store');



    Route::put('eBMS-boissons-note-de-credit/valider/{invoice_number}', 'Backend\NoteCreditController@validerFactureDrink')->name('admin.boissons-note-de-credit.valider');
    Route::put('eBMS-barrista-note-de-credit/valider/{invoice_number}', 'Backend\NoteCreditController@validerFactureBarrista')->name('admin.barrista-note-de-credit.valider');
    Route::put('eBMS-bartender-note-de-credit/valider/{invoice_number}', 'Backend\NoteCreditController@validerFactureBartender')->name('admin.bartender-note-de-credit.store');
    Route::put('eBMS-nourritures-note-de-credit/valider/{invoice_number}', 'Backend\NoteCreditController@validerFactureNourriture')->name('admin.nourritures-note-de-credit.valider');
    Route::put('eBMS-booking-note-de-credit/valider/{invoice_number}', 'Backend\NoteCreditController@validerFactureBooking')->name('admin.booking-note-de-credit.valider');



    // Login Routes
    Route::get('/login', 'Backend\Auth\LoginController@showLoginForm')->name('admin.login');
    Route::post('/login/submit', 'Backend\Auth\LoginController@login')->name('admin.login.submit');

    // Logout Routes
    Route::post('/logout/submit', 'Backend\Auth\LoginController@logout')->name('admin.logout.submit');

    // Forget Password Routes
    Route::get('/password/reset', 'Backend\Auth\ForgetPasswordController@showLinkRequestForm')->name('admin.password.request');
    Route::post('/password/reset/submit', 'Backend\Auth\ForgetPasswordController@reset')->name('admin.password.update');
    
    //change language
    Route::get('/changeLang','Backend\DashboardController@changeLang')->name('changeLang');


    //addresses routes
    Route::get('addresses/index', 'Backend\AddressController@index')->name('admin.addresses.index');
    Route::get('addresses/create', 'Backend\AddressController@create')->name('admin.addresses.create');
    Route::post('addresses/store', 'Backend\AddressController@store')->name('admin.addresses.store');
    Route::get('addresses/edit/{id}', 'Backend\AddressController@edit')->name('admin.addresses.edit');
    Route::put('addresses/update/{id}', 'Backend\AddressController@update')->name('admin.addresses.update');
    Route::delete('addresses/destroy/{id}', 'Backend\AddressController@destroy')->name('admin.addresses.destroy');

    //tables routes
    Route::get('tables/index', 'Backend\TableController@index')->name('admin.tables.index');
    Route::get('tables/choose', 'Backend\TableController@choose')->name('admin.tables.choose');
    Route::get('tables/choose-type-order/{table_id}', 'Backend\TableController@chooseType')->name('admin.tables.choose-type-order');
    Route::get('tables/create', 'Backend\TableController@create')->name('admin.tables.create');
    Route::post('tables/store', 'Backend\TableController@store')->name('admin.tables.store');
    Route::get('tables/edit/{id}', 'Backend\TableController@edit')->name('admin.tables.edit');
    Route::put('tables/update/{id}', 'Backend\TableController@update')->name('admin.tables.update');
    Route::delete('tables/destroy/{id}', 'Backend\TableController@destroy')->name('admin.tables.destroy');

    //staff_members routes
    Route::get('staff_members/index', 'Backend\HomeConsumption\StaffMemberController@index')->name('admin.staff_members.index');
    Route::get('staff_members/choose', 'Backend\HomeConsumption\StaffMemberController@choose')->name('admin.staff_members.choose');
    Route::get('staff_members/choose-type-consumption/{staff_member_id}', 'Backend\HomeConsumption\StaffMemberController@chooseType')->name('admin.staff_members.choose-type-consumption');
    Route::get('staff_members/create', 'Backend\HomeConsumption\StaffMemberController@create')->name('admin.staff_members.create');
    Route::post('staff_members/store', 'Backend\HomeConsumption\StaffMemberController@store')->name('admin.staff_members.store');
    Route::get('staff_members/edit/{id}', 'Backend\HomeConsumption\StaffMemberController@edit')->name('admin.staff_members.edit');
    Route::put('staff_members/update/{id}', 'Backend\HomeConsumption\StaffMemberController@update')->name('admin.staff_members.update');
    Route::delete('staff_members/destroy/{id}', 'Backend\HomeConsumption\StaffMemberController@destroy')->name('admin.staff_members.destroy');

    Route::get('employes/index', 'Backend\EmployeController@index')->name('admin.employes.index');
    Route::get('employes/create', 'Backend\EmployeController@create')->name('admin.employes.create');
    Route::post('employes/store', 'Backend\EmployeController@store')->name('admin.employes.store');
    Route::get('employes/edit/{id}', 'Backend\EmployeController@edit')->name('admin.employes.edit');
    Route::put('employes/update/{id}', 'Backend\EmployeController@update')->name('admin.employes.update');
    Route::delete('employes/destroy/{id}', 'Backend\EmployeController@destroy')->name('admin.employes.destroy');

    Route::get('positions/index', 'Backend\PositionController@index')->name('admin.positions.index');
    Route::get('positions/create', 'Backend\PositionController@create')->name('admin.positions.create');
    Route::post('positions/store', 'Backend\PositionController@store')->name('admin.positions.store');
    Route::get('positions/edit/{id}', 'Backend\PositionController@edit')->name('admin.positions.edit');
    Route::put('positions/update/{id}', 'Backend\PositionController@update')->name('admin.positions.update');
    Route::delete('positions/destroy/{id}', 'Backend\PositionController@destroy')->name('admin.positions.destroy');

    //drinks routes
    Route::get('EBMS/drinks/index', 'Backend\DrinkController@index')->name('admin.drinks.index');
    Route::get('EBMS/drinks/create', 'Backend\DrinkController@create')->name('admin.drinks.create');

    Route::get('EBMS/drinks/autocomplete', 'Backend\DrinkController@autocomplete')->name('admin.drinks.autocomplete');

    Route::post('EBMS/drinks/store', 'Backend\DrinkController@store')->name('admin.drinks.store');
    Route::get('EBMS/drinks/edit/{id}', 'Backend\DrinkController@edit')->name('admin.drinks.edit');
    Route::put('EBMS/drinks/update/{id}', 'Backend\DrinkController@update')->name('admin.drinks.update');
    Route::delete('EBMS/drinks/destroy/{id}', 'Backend\DrinkController@destroy')->name('admin.drinks.destroy');

    //foods routes
    Route::get('EBMS/foods/index', 'Backend\FoodController@index')->name('admin.foods.index');
    Route::get('EBMS/foods/create', 'Backend\FoodController@create')->name('admin.foods.create');
    Route::post('EBMS/foods/store', 'Backend\FoodController@store')->name('admin.foods.store');
    Route::get('EBMS/foods/edit/{id}', 'Backend\FoodController@edit')->name('admin.foods.edit');
    Route::put('EBMS/foods/update/{id}', 'Backend\FoodController@update')->name('admin.foods.update');
    Route::delete('EBMS/foods/destroy/{id}', 'Backend\FoodController@destroy')->name('admin.foods.destroy');

    //services routes
    Route::get('EBMS/services/index', 'Backend\Booking\ServiceController@index')->name('admin.services.index');
    Route::get('EBMS/services/create', 'Backend\Booking\ServiceController@create')->name('admin.services.create');
    Route::post('EBMS/services/store', 'Backend\Booking\ServiceController@store')->name('admin.services.store');
    Route::get('EBMS/services/edit/{id}', 'Backend\Booking\ServiceController@edit')->name('admin.services.edit');
    Route::put('EBMS/services/update/{id}', 'Backend\Booking\ServiceController@update')->name('admin.services.update');
    Route::delete('EBMS/services/destroy/{id}', 'Backend\Booking\ServiceController@destroy')->name('admin.services.destroy');

    //salles routes
    Route::get('EBMS/salles/index', 'Backend\Booking\SalleController@index')->name('admin.salles.index');
    Route::get('EBMS/salles/create', 'Backend\Booking\SalleController@create')->name('admin.salles.create');
    Route::post('EBMS/salles/store', 'Backend\Booking\SalleController@store')->name('admin.salles.store');
    Route::get('EBMS/salles/edit/{id}', 'Backend\Booking\SalleController@edit')->name('admin.salles.edit');
    Route::put('EBMS/salles/update/{id}', 'Backend\Booking\SalleController@update')->name('admin.salles.update');
    Route::delete('EBMS/salles/destroy/{id}', 'Backend\Booking\SalleController@destroy')->name('admin.salles.destroy');

    //rooms routes
    Route::get('EBMS/rooms/index', 'Backend\Booking\RoomController@index')->name('admin.rooms.index');
    Route::get('EBMS/rooms/create', 'Backend\Booking\RoomController@create')->name('admin.rooms.create');
    Route::post('EBMS/rooms/store', 'Backend\Booking\RoomController@store')->name('admin.rooms.store');
    Route::get('EBMS/rooms/edit/{id}', 'Backend\Booking\RoomController@edit')->name('admin.rooms.edit');
    Route::put('EBMS/rooms/update/{id}', 'Backend\Booking\RoomController@update')->name('admin.rooms.update');
    Route::delete('EBMS/rooms/destroy/{id}', 'Backend\Booking\RoomController@destroy')->name('admin.rooms.destroy');

    //swiming-pools routes
    Route::get('EBMS/swiming-pools/index', 'Backend\Booking\SwimingPoolController@index')->name('admin.swiming-pools.index');
    Route::get('EBMS/swiming-pools/create', 'Backend\Booking\SwimingPoolController@create')->name('admin.swiming-pools.create');
    Route::post('EBMS/swiming-pools/store', 'Backend\Booking\SwimingPoolController@store')->name('admin.swiming-pools.store');
    Route::get('EBMS/swiming-pools/edit/{id}', 'Backend\Booking\SwimingPoolController@edit')->name('admin.swiming-pools.edit');
    Route::put('EBMS/swiming-pools/update/{id}', 'Backend\Booking\SwimingPoolController@update')->name('admin.swiming-pools.update');
    Route::delete('EBMS/swiming-pools/destroy/{id}', 'Backend\Booking\SwimingPoolController@destroy')->name('admin.swiming-pools.destroy');

    //kidness-spaces routes
    Route::get('EBMS/kidness-spaces/index', 'Backend\Booking\KidnessSpaceController@index')->name('admin.kidness-spaces.index');
    Route::get('EBMS/kidness-spaces/create', 'Backend\Booking\KidnessSpaceController@create')->name('admin.kidness-spaces.create');
    Route::post('EBMS/kidness-spaces/store', 'Backend\Booking\KidnessSpaceController@store')->name('admin.kidness-spaces.store');
    Route::get('EBMS/kidness-spaces/edit/{id}', 'Backend\Booking\KidnessSpaceController@edit')->name('admin.kidness-spaces.edit');
    Route::put('EBMS/kidness-spaces/update/{id}', 'Backend\Booking\KidnessSpaceController@update')->name('admin.kidness-spaces.update');
    Route::delete('EBMS/kidness-spaces/destroy/{id}', 'Backend\Booking\KidnessSpaceController@destroy')->name('admin.kidness-spaces.destroy');

    //break-fasts routes
    Route::get('EBMS/break-fasts/index', 'Backend\Booking\BreakFastController@index')->name('admin.break-fasts.index');
    Route::get('EBMS/break-fasts/create', 'Backend\Booking\BreakFastController@create')->name('admin.break-fasts.create');
    Route::post('EBMS/break-fasts/store', 'Backend\Booking\BreakFastController@store')->name('admin.break-fasts.store');
    Route::get('EBMS/break-fasts/edit/{id}', 'Backend\Booking\BreakFastController@edit')->name('admin.break-fasts.edit');
    Route::put('EBMS/break-fasts/update/{id}', 'Backend\Booking\BreakFastController@update')->name('admin.break-fasts.update');
    Route::delete('EBMS/break-fasts/destroy/{id}', 'Backend\Booking\BreakFastController@destroy')->name('admin.break-fasts.destroy');

    //techniques routes
    Route::get('EBMS/techniques/index', 'Backend\Booking\TechniqueController@index')->name('admin.techniques.index');
    Route::get('EBMS/techniques/create', 'Backend\Booking\TechniqueController@create')->name('admin.techniques.create');
    Route::post('EBMS/techniques/store', 'Backend\Booking\TechniqueController@store')->name('admin.techniques.store');
    Route::get('EBMS/techniques/edit/{id}', 'Backend\Booking\TechniqueController@edit')->name('admin.techniques.edit');
    Route::put('EBMS/techniques/update/{id}', 'Backend\Booking\TechniqueController@update')->name('admin.techniques.update');
    Route::delete('EBMS/techniques/destroy/{id}', 'Backend\Booking\TechniqueController@destroy')->name('admin.techniques.destroy');

    //clients routes
    Route::get('EBMS/clients/index', 'Backend\ClientController@index')->name('admin.clients.index');
    Route::get('EBMS/clients/create', 'Backend\ClientController@create')->name('admin.clients.create');
    Route::post('EBMS/clients/store', 'Backend\ClientController@store')->name('admin.clients.store');
    Route::get('EBMS/clients/edit/{id}', 'Backend\ClientController@edit')->name('admin.clients.edit');
    Route::put('EBMS/clients/update/{id}', 'Backend\ClientController@update')->name('admin.clients.update');
    Route::delete('EBMS/clients/destroy/{id}', 'Backend\ClientController@destroy')->name('admin.clients.destroy');

    Route::post('EBMS/clients/checkTIN', 'Backend\ClientController@checkTIN')->name('admin.clients.checkTIN');

    //booking-clients routes
    Route::get('EBMS/booking-clients/index', 'Backend\Booking\ClientController@index')->name('admin.booking-clients.index');
    Route::get('EBMS/booking-clients/create', 'Backend\Booking\ClientController@create')->name('admin.booking-clients.create');
    Route::post('EBMS/booking-clients/store', 'Backend\Booking\ClientController@store')->name('admin.booking-clients.store');
    Route::get('EBMS/booking-clients/edit/{id}', 'Backend\Booking\ClientController@edit')->name('admin.booking-clients.edit');
    Route::put('EBMS/booking-clients/update/{id}', 'Backend\Booking\ClientController@update')->name('admin.booking-clients.update');
    Route::delete('EBMS/booking-clients/destroy/{id}', 'Backend\Booking\ClientController@destroy')->name('admin.booking-clients.destroy');

    //bookings routes
    Route::get('EBMS/booking-salles/index', 'Backend\Booking\BookingController@indexSalle')->name('admin.booking-salles.index');
    Route::get('EBMS/booking-rooms/index', 'Backend\Booking\BookingController@indexRoom')->name('admin.booking-rooms.index');
    Route::get('EBMS/booking-salles/create', 'Backend\Booking\BookingController@createSalle')->name('admin.booking-salles.create');
    Route::get('EBMS/booking-rooms/create', 'Backend\Booking\BookingController@createRoom')->name('admin.booking-rooms.create');
    Route::post('EBMS/booking-salles/store', 'Backend\Booking\BookingController@storeSalle')->name('admin.booking-salles.store');
    Route::post('EBMS/booking-rooms/store', 'Backend\Booking\BookingController@storeRoom')->name('admin.booking-rooms.store');

    Route::get('EBMS/booking-services/index', 'Backend\Booking\BookingController@indexService')->name('admin.booking-services.index');
    Route::get('EBMS/booking-services/create', 'Backend\Booking\BookingController@createService')->name('admin.booking-services.create');
    Route::post('EBMS/booking-services/store', 'Backend\Booking\BookingController@storeService')->name('admin.booking-services.store');

    Route::get('EBMS/booking-breakfast/index', 'Backend\Booking\BookingController@indexBreakFast')->name('admin.booking-breakfast.index');
    Route::get('EBMS/booking-breakfast/create', 'Backend\Booking\BookingController@createBreakFast')->name('admin.booking-breakfast.create');
    Route::post('EBMS/booking-breakfast/store', 'Backend\Booking\BookingController@storeBreakFast')->name('admin.booking-breakfast.store');

    Route::get('EBMS/booking-swiming-pool/index', 'Backend\Booking\BookingController@indexSwimingPool')->name('admin.booking-swiming-pool.index');
    Route::get('EBMS/booking-swiming-pool/create', 'Backend\Booking\BookingController@createSwimingPool')->name('admin.booking-swiming-pool.create');
    Route::post('EBMS/booking-swiming-pool/store', 'Backend\Booking\BookingController@storeSwimingPool')->name('admin.booking-swiming-pool.store');

    Route::get('EBMS/booking-kidness-space/index', 'Backend\Booking\BookingController@indexKidnessSpace')->name('admin.booking-kidness-space.index');
    Route::get('EBMS/booking-kidness-space/create', 'Backend\Booking\BookingController@createKidnessSpace')->name('admin.booking-kidness-space.create');
    Route::post('EBMS/booking-kidness-space/store', 'Backend\Booking\BookingController@storeKidnessSpace')->name('admin.booking-kidness-space.store');



    Route::get('EBMS/booking-tables/index', 'Backend\Booking\BookingController@indexTable')->name('admin.booking-tables.index');
    Route::get('EBMS/booking-tables/create', 'Backend\Booking\BookingController@createTable')->name('admin.booking-tables.create');
    Route::post('EBMS/booking-tables/store', 'Backend\Booking\BookingController@storeTable')->name('admin.booking-tables.store');

    Route::get('EBMS/bookings/edit/{booking_no}', 'Backend\Booking\BookingController@edit')->name('admin.bookings.edit');
    Route::put('EBMS/bookings/update/{booking_no}', 'Backend\Booking\BookingController@update')->name('admin.bookings.update');
    Route::delete('EBMS/bookings/destroy/{booking_no}', 'Backend\Booking\BookingController@destroy')->name('admin.bookings.destroy');

    Route::get('EBMS/bookings/show/{booking_no}', 'Backend\Booking\BookingController@show')->name('admin.bookings.show');

    Route::get('EBMS/bookings/generatepdf/{booking_no}','Backend\Booking\BookingController@htmlPdf')->name('admin.bookings.generatepdf');
    Route::put('EBMS/bookings/validate/{booking_no}', 'Backend\Booking\BookingController@validateBooking')->name('admin.bookings.validate');
    Route::put('EBMS/bookings/reject/{booking_no}','Backend\Booking\BookingController@reject')->name('admin.bookings.reject');
    Route::put('EBMS/bookings/reset/{booking_no}','Backend\Booking\BookingController@reset')->name('admin.bookings.reset');

    //barrist-items routes
    Route::get('EBMS/barrist-items/index', 'Backend\BarristItemController@index')->name('admin.barrist-items.index');
    Route::get('EBMS/barrist-items/create', 'Backend\BarristItemController@create')->name('admin.barrist-items.create');
    Route::post('EBMS/barrist-items/store', 'Backend\BarristItemController@store')->name('admin.barrist-items.store');
    Route::get('EBMS/barrist-items/edit/{id}', 'Backend\BarristItemController@edit')->name('admin.barrist-items.edit');
    Route::put('EBMS/barrist-items/update/{id}', 'Backend\BarristItemController@update')->name('admin.barrist-items.update');
    Route::delete('EBMS/barrist-items/destroy/{id}', 'Backend\BarristItemController@destroy')->name('admin.barrist-items.destroy');

    //bartender-items routes
    Route::get('EBMS/bartender-items/index', 'Backend\BartenderItemController@index')->name('admin.bartender-items.index');
    Route::get('EBMS/bartender-items/create', 'Backend\BartenderItemController@create')->name('admin.bartender-items.create');
    Route::post('EBMS/bartender-items/store', 'Backend\BartenderItemController@store')->name('admin.bartender-items.store');
    Route::get('EBMS/bartender-items/edit/{id}', 'Backend\BartenderItemController@edit')->name('admin.bartender-items.edit');
    Route::put('EBMS/bartender-items/update/{id}', 'Backend\BartenderItemController@update')->name('admin.bartender-items.update');
    Route::delete('EBMS/bartender-items/destroy/{id}', 'Backend\BartenderItemController@destroy')->name('admin.bartender-items.destroy');

    //private-store-items routes
    Route::get('magasin-egr/private-store-items/index', 'Backend\PrivateStoreItemController@index')->name('admin.private-store-items.index');
    Route::get('magasin-egr/private-store-items/create', 'Backend\PrivateStoreItemController@create')->name('admin.private-store-items.create');
    Route::post('magasin-egr/private-store-items/store', 'Backend\PrivateStoreItemController@store')->name('admin.private-store-items.store');
    Route::get('magasin-egr/private-store-items/edit/{id}', 'Backend\PrivateStoreItemController@edit')->name('admin.private-store-items.edit');
    Route::put('magasin-egr/private-store-items/update/{id}', 'Backend\PrivateStoreItemController@update')->name('admin.private-store-items.update');
    Route::delete('magasin-egr/private-store-items/destroy/{id}', 'Backend\PrivateStoreItemController@destroy')->name('admin.private-store-items.destroy');

    Route::get('magasin-egr/private-store-items/export-to-excel', 'Backend\PrivateStoreItemController@exportToExcel')->name('admin.private-store-items.export-to-excel');
    Route::get('magasin-egr/private-store-items/export-to-pdf', 'Backend\PrivateStoreItemController@exportToPdf')->name('admin.private-store-items.export-to-pdf');

    //food-items routes
    Route::get('EBMS/food-items/index', 'Backend\FoodItemController@index')->name('admin.food-items.index');
    Route::get('EBMS/food-items/create', 'Backend\FoodItemController@create')->name('admin.food-items.create');
    Route::post('EBMS/food-items/store', 'Backend\FoodItemController@store')->name('admin.food-items.store');
    Route::get('EBMS/food-items/show/{code}', 'Backend\FoodItemController@show')->name('admin.food-items.show');
    Route::get('EBMS/food-items/fiche/{code}', 'Backend\FoodItemController@fiche')->name('admin.food-items.fiche');
    Route::get('EBMS/food-items/edit/{code}', 'Backend\FoodItemController@edit')->name('admin.food-items.edit');
    Route::put('EBMS/food-items/update/{code}', 'Backend\FoodItemController@update')->name('admin.food-items.update');
    Route::delete('EBMS/food-items/destroy/{code}', 'Backend\FoodItemController@destroy')->name('admin.food-items.destroy');
    Route::get('EBMS/food-items/fiche-technique', 'Backend\FoodItemController@ficheTechnique')->name('admin.food-items.fiche-technique');
    Route::get('EBMS/food-item-export-to-excel/fiche-technique', 'Backend\FoodItemController@exportToExcel')->name('admin.food-item-export-to-excel.fiche-technique');

    //materials routes
    Route::get('EBMS/materials/index', 'Backend\MaterialController@index')->name('admin.materials.index');
    Route::get('EBMS/materials/create', 'Backend\MaterialController@create')->name('admin.materials.create');
    Route::post('EBMS/materials/store', 'Backend\MaterialController@store')->name('admin.materials.store');
    Route::get('EBMS/materials/edit/{id}', 'Backend\MaterialController@edit')->name('admin.materials.edit');
    Route::put('EBMS/materials/update/{id}', 'Backend\MaterialController@update')->name('admin.materials.update');
    Route::delete('EBMS/materials/destroy/{id}', 'Backend\MaterialController@destroy')->name('admin.materials.destroy');

    //material-category routes
    Route::get('EBMS/material-category/index', 'Backend\MaterialCategoryController@index')->name('admin.material-category.index');
    Route::get('EBMS/material-category/create', 'Backend\MaterialCategoryController@create')->name('admin.material-category.create');
    Route::post('EBMS/material-category/store', 'Backend\MaterialCategoryController@store')->name('admin.material-category.store');
    Route::get('EBMS/material-category/edit/{id}', 'Backend\MaterialCategoryController@edit')->name('admin.material-category.edit');
    Route::put('EBMS/material-category/update/{id}', 'Backend\MaterialCategoryController@update')->name('admin.material-category.update');
    Route::delete('EBMS/material-category/destroy/{id}', 'Backend\MaterialCategoryController@destroy')->name('admin.material-category.destroy');

    //drink-category routes
    Route::get('EBMS/drink-category/index', 'Backend\DrinkCategoryController@index')->name('admin.drink-category.index');
    Route::get('EBMS/drink-category/create', 'Backend\DrinkCategoryController@create')->name('admin.drink-category.create');
    Route::post('EBMS/drink-category/store', 'Backend\DrinkCategoryController@store')->name('admin.drink-category.store');
    Route::get('EBMS/drink-category/edit/{id}', 'Backend\DrinkCategoryController@edit')->name('admin.drink-category.edit');
    Route::put('EBMS/drink-category/update/{id}', 'Backend\DrinkCategoryController@update')->name('admin.drink-category.update');
    Route::delete('EBMS/drink-category/destroy/{id}', 'Backend\DrinkCategoryController@destroy')->name('admin.drink-category.destroy');

    //food-category routes
    Route::get('EBMS/food-category/index', 'Backend\FoodCategoryController@index')->name('admin.food-category.index');
    Route::get('EBMS/food-category/create', 'Backend\FoodCategoryController@create')->name('admin.food-category.create');
    Route::post('EBMS/food-category/store', 'Backend\FoodCategoryController@store')->name('admin.food-category.store');
    Route::get('EBMS/food-category/edit/{id}', 'Backend\FoodCategoryController@edit')->name('admin.food-category.edit');
    Route::put('EBMS/food-category/update/{id}', 'Backend\FoodCategoryController@update')->name('admin.food-category.update');
    Route::delete('EBMS/food-category/destroy/{id}', 'Backend\FoodCategoryController@destroy')->name('admin.food-category.destroy');

    //drink-measurement routes
    Route::get('Akiwacu/drink-measurement/index', 'Backend\DrinkMeasurementController@index')->name('admin.drink-measurement.index');
    Route::get('Akiwacu/drink-measurement/create', 'Backend\DrinkMeasurementController@create')->name('admin.drink-measurement.create');
    Route::post('Akiwacu/drink-measurement/store', 'Backend\DrinkMeasurementController@store')->name('admin.drink-measurement.store');
    Route::get('Akiwacu/drink-measurement/edit/{id}', 'Backend\DrinkMeasurementController@edit')->name('admin.drink-measurement.edit');
    Route::put('Akiwacu/drink-measurement/update/{id}', 'Backend\DrinkMeasurementController@update')->name('admin.drink-measurement.update');
    Route::delete('Akiwacu/drink-measurement/destroy/{id}', 'Backend\DrinkMeasurementController@destroy')->name('admin.drink-measurement.destroy');

    //food-measurement routes
    Route::get('Akiwacu/food-measurement/index', 'Backend\FoodMeasurementController@index')->name('admin.food-measurement.index');
    Route::get('Akiwacu/food-measurement/create', 'Backend\FoodMeasurementController@create')->name('admin.food-measurement.create');
    Route::post('Akiwacu/food-measurement/store', 'Backend\FoodMeasurementController@store')->name('admin.food-measurement.store');
    Route::get('Akiwacu/food-measurement/edit/{id}', 'Backend\FoodMeasurementController@edit')->name('admin.food-measurement.edit');
    Route::put('Akiwacu/food-measurement/update/{id}', 'Backend\FoodMeasurementController@update')->name('admin.food-measurement.update');
    Route::delete('Akiwacu/food-measurement/destroy/{id}', 'Backend\FoodMeasurementController@destroy')->name('admin.food-measurement.destroy');

    //material-measurement routes
    Route::get('Akiwacu/material-measurement/index', 'Backend\MaterialMeasurementController@index')->name('admin.material-measurement.index');
    Route::get('Akiwacu/material-measurement/create', 'Backend\MaterialMeasurementController@create')->name('admin.material-measurement.create');
    Route::post('Akiwacu/material-measurement/store', 'Backend\MaterialMeasurementController@store')->name('admin.material-measurement.store');
    Route::get('Akiwacu/material-measurement/edit/{id}', 'Backend\MaterialMeasurementController@edit')->name('admin.material-measurement.edit');
    Route::put('Akiwacu/material-measurement/update/{id}', 'Backend\MaterialMeasurementController@update')->name('admin.material-measurement.update');
    Route::delete('Akiwacu/material-measurement/destroy/{id}', 'Backend\MaterialMeasurementController@destroy')->name('admin.material-measurement.destroy');

    //barrist stock routes
    Route::get('EBMS/barrist-production-store/index', 'Backend\BarristProductionStoreController@index')->name('admin.barrist-production-store.index');
    Route::delete('EBMS/barrist-production-store/destroy/{id}', 'Backend\BarristProductionStoreController@destroy')->name('admin.barrist-production-store.destroy');
    Route::get('EBMS/barrist-production-store-generatepdf', 'Backend\BarristProductionStoreController@toPdf')->name('admin.barrist-production-store');

    //bartender stock routes

    Route::get('EBMS/bartender-production-store/index', 'Backend\BartenderProductionStoreController@index')->name('admin.bartender-production-store.index');
    Route::delete('EBMS/bartender-production-store/destroy/{id}', 'Backend\BartenderProductionStoreController@destroy')->name('admin.bartender-production-store.destroy');
    Route::get('EBMS/bartender-production-store-generatepdf', 'Backend\BartenderProductionStoreController@toPdf')->name('admin.bartender-production-store');

    Route::get('EBMS/bartender-transformation/create', 'Backend\BartenderProductionStoreController@create')->name('admin.bartender-transformation.create');
    Route::post('EBMS/bartender-transformation/store', 'Backend\BartenderProductionStoreController@store')->name('admin.bartender-transformation.store');
    Route::get('EBMS/bartender-transformation/rapport', 'Backend\BartenderProductionStoreController@rapport')->name('admin.bartender-transformation.rapport');

    //Food stock routes

    Route::get('EBMS/food-store/index', 'Backend\FoodStoreController@index')->name('admin.food-store.index');
    Route::delete('EBMS/food-store/destroy/{id}', 'Backend\FoodStoreController@destroy')->name('admin.food-store.destroy');
    Route::get('EBMS/food-store-generatepdf', 'Backend\FoodStoreController@toPdf')->name('admin.food-store');

    Route::get('EBMS/food-transformation/create', 'Backend\FoodStoreController@create')->name('admin.food-transformation.create');
    Route::post('EBMS/food-transformation/store', 'Backend\FoodStoreController@store')->name('admin.food-transformation.store');

    //Extra Grand Stock des boissons
    Route::get('EBMS/drink-extra-big-store/index', 'Backend\DrinkExtraBigStoreController@index')->name('admin.drink-extra-big-store.index');
    Route::get('EBMS/drink-extra-big-store/create', 'Backend\DrinkExtraBigStoreController@create')->name('admin.drink-extra-big-store.create');
    Route::post('EBMS/drink-extra-big-store/store', 'Backend\DrinkExtraBigStoreController@store')->name('admin.drink-extra-big-store.store');
    Route::get('EBMS/drink-extra-big-store/show/{code}', 'Backend\DrinkExtraBigStoreController@show')->name('admin.drink-extra-big-store.show');
    Route::get('EBMS/drink-extra-big-store/edit/{code}', 'Backend\DrinkExtraBigStoreController@edit')->name('admin.drink-extra-big-store.edit');
    Route::put('EBMS/drink-extra-big-store/update/{code}', 'Backend\DrinkExtraBigStoreController@update')->name('admin.drink-extra-big-store.update');
    Route::delete('EBMS/drink-extra-big-store/destroy/{id}', 'Backend\DrinkExtraBigStoreController@destroy')->name('admin.drink-extra-big-store.destroy');

    Route::get('EBMS/drink-extra-big-store/store-status/{code}', 'Backend\DrinkExtraBigStoreController@storeStatus')->name('admin.drink-extra-big-store.storeStatus');
    //Grand Stock des boissons
    Route::get('EBMS/drink-big-store/index', 'Backend\DrinkBigStoreController@index')->name('admin.drink-big-store.index');
    Route::get('EBMS/drink-big-store/create', 'Backend\DrinkBigStoreController@create')->name('admin.drink-big-store.create');
    Route::post('EBMS/drink-big-store/store', 'Backend\DrinkBigStoreController@store')->name('admin.drink-big-store.store');
    Route::get('EBMS/drink-big-store/show/{code}', 'Backend\DrinkBigStoreController@show')->name('admin.drink-big-store.show');
    Route::get('EBMS/drink-big-store/edit/{code}', 'Backend\DrinkBigStoreController@edit')->name('admin.drink-big-store.edit');
    Route::put('EBMS/drink-big-store/update/{code}', 'Backend\DrinkBigStoreController@update')->name('admin.drink-big-store.update');
    Route::delete('EBMS/drink-big-store/destroy/{id}', 'Backend\DrinkBigStoreController@destroy')->name('admin.drink-big-store.destroy');

    Route::get('EBMS/drink-big-store/store-status/{code}', 'Backend\DrinkBigStoreController@storeStatus')->name('admin.drink-big-store.storeStatus');
    Route::get('EBMS/drink-big-store/export-to-excel/{code}', 'Backend\DrinkBigStoreController@exportToExcel')->name('admin.drink-big-store.exportToExcel');

    Route::get('EBMS/virtual-drink-big-store/export-to-excel', 'Backend\DrinkBigStoreController@VirtualExportToExcel')->name('admin.virtual-drink-big-store.exportToExcel');

    //Petit Stock des boissons
    Route::get('EBMS/drink-small-store/index', 'Backend\DrinkSmallStoreController@index')->name('admin.drink-small-store.index');
    Route::get('EBMS/drink-small-store/create', 'Backend\DrinkSmallStoreController@create')->name('admin.drink-small-store.create');
    Route::post('EBMS/drink-small-store/store', 'Backend\DrinkSmallStoreController@store')->name('admin.drink-small-store.store');
    Route::get('EBMS/drink-small-store/show/{code}', 'Backend\DrinkSmallStoreController@show')->name('admin.drink-small-store.show');
    Route::get('EBMS/drink-small-store/edit/{code}', 'Backend\DrinkSmallStoreController@edit')->name('admin.drink-small-store.edit');
    Route::put('EBMS/drink-small-store/update/{code}', 'Backend\DrinkSmallStoreController@update')->name('admin.drink-small-store.update');
    Route::delete('EBMS/drink-small-store/destroy/{id}', 'Backend\DrinkSmallStoreController@destroy')->name('admin.drink-small-store.destroy');

    Route::get('EBMS/drink-small-store/store-status/{code}', 'Backend\DrinkSmallStoreController@storeStatus')->name('admin.drink-small-store.storeStatus');
    Route::get('EBMS/drink-small-store-status/export-to-excel/{code}', 'Backend\DrinkSmallStoreController@ExportToExcel')->name('admin.drink-small-store.exportToExcel');

    Route::get('EBMS/virtual-drink-small-store-status/export-to-excel', 'Backend\DrinkSmallStoreController@virtualExportToExcel')->name('admin.virtual-drink-small-store.exportToExcel');

    //Extra Grand Stock des nourritures
    Route::get('EBMS/food-extra-big-store/index', 'Backend\FoodExtraBigStoreController@index')->name('admin.food-extra-big-store.index');
    Route::get('EBMS/food-extra-big-store/create', 'Backend\FoodExtraBigStoreController@create')->name('admin.food-extra-big-store.create');
    Route::post('EBMS/food-extra-big-store/store', 'Backend\FoodExtraBigStoreController@store')->name('admin.food-extra-big-store.store');
    Route::get('EBMS/food-extra-big-store/show/{code}', 'Backend\FoodExtraBigStoreController@show')->name('admin.food-extra-big-store.show');
    Route::get('EBMS/food-extra-big-store/edit/{code}', 'Backend\FoodExtraBigStoreController@edit')->name('admin.food-extra-big-store.edit');
    Route::put('EBMS/food-extra-big-store/update/{code}', 'Backend\FoodExtraBigStoreController@update')->name('admin.food-extra-big-store.update');
    Route::delete('EBMS/food-extra-big-store/destroy/{id}', 'Backend\FoodExtraBigStoreController@destroy')->name('admin.food-extra-big-store.destroy');

    Route::get('EBMS/food-extra-big-store/store-status/{code}', 'Backend\FoodExtraBigStoreController@storeStatus')->name('admin.food-extra-big-store.storeStatus');

    //Grand Stock des nourritures
    Route::get('EBMS/food-big-store/index', 'Backend\FoodBigStoreController@index')->name('admin.food-big-store.index');
    Route::get('EBMS/food-big-store/create', 'Backend\FoodBigStoreController@create')->name('admin.food-big-store.create');
    Route::post('EBMS/food-big-store/store', 'Backend\FoodBigStoreController@store')->name('admin.food-big-store.store');
    Route::get('EBMS/food-big-store/show/{code}', 'Backend\FoodBigStoreController@show')->name('admin.food-big-store.show');
    Route::get('EBMS/food-big-store/edit/{code}', 'Backend\FoodBigStoreController@edit')->name('admin.food-big-store.edit');
    Route::put('EBMS/food-big-store/update/{code}', 'Backend\FoodBigStoreController@update')->name('admin.food-big-store.update');
    Route::delete('EBMS/food-big-store/destroy/{id}', 'Backend\FoodBigStoreController@destroy')->name('admin.food-big-store.destroy');

    Route::get('EBMS/food-big-store/store-status/{code}', 'Backend\FoodBigStoreController@storeStatus')->name('admin.food-big-store.storeStatus');
    Route::get('EBMS/food-big-store-status/export-to-exce/{code}', 'Backend\FoodBigStoreController@ExportToExcel')->name('admin.food-big-store.exportToExcel');

    //Petit Stock des nourritures
    Route::get('EBMS/food-small-store/index', 'Backend\FoodSmallStoreController@index')->name('admin.food-small-store.index');
    Route::get('EBMS/food-small-store/create', 'Backend\FoodSmallStoreController@create')->name('admin.food-small-store.create');
    Route::post('EBMS/food-small-store/store', 'Backend\FoodSmallStoreController@store')->name('admin.food-small-store.store');
    Route::get('EBMS/food-small-store/show/{code}', 'Backend\FoodSmallStoreController@show')->name('admin.food-small-store.show');
    Route::get('EBMS/food-small-store/edit/{code}', 'Backend\FoodSmallStoreController@edit')->name('admin.food-small-store.edit');
    Route::put('EBMS/food-small-store/update/{code}', 'Backend\FoodSmallStoreController@update')->name('admin.food-small-store.update');
    Route::delete('EBMS/food-small-store/destroy/{id}', 'Backend\FoodSmallStoreController@destroy')->name('admin.food-small-store.destroy');

    Route::get('EBMS/food-small-store/store-status/{code}', 'Backend\FoodSmallStoreController@storeStatus')->name('admin.food-small-store.storeStatus');
    Route::get('EBMS/food-small-store-status/export-to-exce/{code}', 'Backend\FoodSmallStoreController@ExportToExcel')->name('admin.food-small-store.exportToExcel');

    //Extra Grand Stock des materiels
    Route::get('EBMS/material-extra-big-store/index', 'Backend\MaterialExtraBigStoreController@index')->name('admin.material-extra-big-store.index');
    Route::get('EBMS/material-extra-big-store/create', 'Backend\MaterialExtraBigStoreController@create')->name('admin.material-extra-big-store.create');
    Route::post('EBMS/material-extra-big-store/store', 'Backend\MaterialExtraBigStoreController@store')->name('admin.material-extra-big-store.store');
    Route::get('EBMS/material-extra-big-store/show/{code}', 'Backend\MaterialExtraBigStoreController@show')->name('admin.material-extra-big-store.show');
    Route::get('EBMS/material-extra-big-store/edit/{code}', 'Backend\MaterialExtraBigStoreController@edit')->name('admin.material-extra-big-store.edit');
    Route::put('EBMS/material-extra-big-store/update/{code}', 'Backend\MaterialExtraBigStoreController@update')->name('admin.material-extra-big-store.update');
    Route::delete('EBMS/material-extra-big-store/destroy/{id}', 'Backend\MaterialExtraBigStoreController@destroy')->name('admin.material-extra-big-store.destroy');

    Route::get('EBMS/material-extra-big-store/store-status/{code}', 'Backend\MaterialExtraBigStoreController@storeStatus')->name('admin.material-extra-big-store.storeStatus');

    //Grand Stock des materiels
    Route::get('EBMS/material-big-store/index', 'Backend\MaterialBigStoreController@index')->name('admin.material-big-store.index');
    Route::get('EBMS/material-big-store/create', 'Backend\MaterialBigStoreController@create')->name('admin.material-big-store.create');
    Route::post('EBMS/material-big-store/store', 'Backend\MaterialBigStoreController@store')->name('admin.material-big-store.store');
    Route::get('EBMS/material-big-store/show/{code}', 'Backend\MaterialBigStoreController@show')->name('admin.material-big-store.show');
    Route::get('EBMS/material-big-store/edit/{code}', 'Backend\MaterialBigStoreController@edit')->name('admin.material-big-store.edit');
    Route::put('EBMS/material-big-store/update/{code}', 'Backend\MaterialBigStoreController@update')->name('admin.material-big-store.update');
    Route::delete('EBMS/material-big-store/destroy/{id}', 'Backend\MaterialBigStoreController@destroy')->name('admin.material-big-store.destroy');

    Route::get('EBMS/material-big-store/store-status/{code}', 'Backend\MaterialBigStoreController@storeStatus')->name('admin.material-big-store.storeStatus');
    Route::get('EBMS/material-big-store/export-to-exce/{code}', 'Backend\MaterialBigStoreController@ExportToExcel')->name('admin.material-big-store.exportToExcel');

    //Petit Stock des materiels
    Route::get('EBMS/material-small-store/index', 'Backend\MaterialSmallStoreController@index')->name('admin.material-small-store.index');
    Route::get('EBMS/material-small-store/create', 'Backend\MaterialSmallStoreController@create')->name('admin.material-small-store.create');
    Route::post('EBMS/material-small-store/store', 'Backend\MaterialSmallStoreController@store')->name('admin.material-small-store.store');
    Route::get('EBMS/material-small-store/show/{code}', 'Backend\MaterialSmallStoreController@show')->name('admin.material-small-store.show');
    Route::get('EBMS/material-small-store/edit/{code}', 'Backend\MaterialSmallStoreController@edit')->name('admin.material-small-store.edit');
    Route::put('EBMS/material-small-store/update/{code}', 'Backend\MaterialSmallStoreController@update')->name('admin.material-small-store.update');
    Route::delete('EBMS/material-small-store/destroy/{id}', 'Backend\MaterialSmallStoreController@destroy')->name('admin.material-small-store.destroy');

    Route::get('EBMS/material-small-store/store-status/{code}', 'Backend\MaterialSmallStoreController@storeStatus')->name('admin.material-small-store.storeStatus');

    //invoice kitchens routes
    Route::get('EBMS/invoice-kitchen/index', 'Backend\FactureRestaurantController@index')->name('admin.invoice-kitchens.index');
    Route::get('EBMS/invoice-kitchen/create/{order_no}', 'Backend\FactureRestaurantController@create')->name('admin.invoice-kitchens.create');
    Route::get('EBMS/invoice-kitchen/create-by-table/{table_id}', 'Backend\FactureRestaurantController@createByTable')->name('admin.invoice-kitchens.create-by-table');
    Route::get('EBMS/invoice-kitchen/edit/{invoice_number}', 'Backend\FactureRestaurantController@edit')->name('admin.invoice-kitchens.edit');
    Route::get('EBMS/invoice-kitchen/show/{invoice_number}','Backend\FactureRestaurantController@show')->name('admin.invoice-kitchens.show');


    //invoice barrist routes
    Route::get('EBMS/barrist-invoices/index', 'Backend\FactureBarristController@index')->name('admin.barrist-invoices.index');
    Route::get('EBMS/barrist-invoices/create/{order_no}', 'Backend\FactureBarristController@create')->name('admin.barrist-invoices.create');
    Route::get('EBMS/barrist-invoices/create-by-table/{table_id}', 'Backend\FactureBarristController@createByTable')->name('admin.barrist-invoices.create-by-table');
    Route::get('EBMS/barrist-invoices/edit/{invoice_number}', 'Backend\FactureBarristController@edit')->name('admin.barrist-invoices.edit');
    Route::delete('EBMS/barrist-invoices/destroy/{invoice_number}', 'Backend\FactureBarristController@destroy')->name('admin.barrist-invoices.destroy');
    Route::get('EBMS/barrist-invoices/show/{invoice_number}','Backend\FactureBarristController@show')->name('admin.barrist-invoices.show');

    //invoice bartender routes
    Route::get('EBMS/bartender-invoices/index', 'Backend\FactureBartenderController@index')->name('admin.bartender-invoices.index');
    Route::get('EBMS/bartender-invoices/create/{order_no}', 'Backend\FactureBartenderController@create')->name('admin.bartender-invoices.create');
    Route::get('EBMS/bartender-invoices/create-by-table/{table_id}', 'Backend\FactureBartenderController@createByTable')->name('admin.bartender-invoices.create-by-table');
    Route::get('EBMS/bartender-invoices/edit/{invoice_number}', 'Backend\FactureBartenderController@edit')->name('admin.bartender-invoices.edit');
    Route::delete('EBMS/bartender-invoices/destroy/{invoice_number}', 'Backend\FactureBartenderController@destroy')->name('admin.bartender-invoices.destroy');
    Route::get('EBMS/bartender-invoices/show/{invoice_number}','Backend\FactureBartenderController@show')->name('admin.bartender-invoices.show');

    //invoice booking routes
    Route::get('EBMS/booking-invoices/index', 'Backend\FactureBookingController@index')->name('admin.booking-invoices.index');
    Route::get('EBMS/booking-salle-invoices/index', 'Backend\FactureBookingController@indexSalle')->name('admin.booking-salle-invoices.index');
    Route::get('EBMS/booking-room-invoices/index', 'Backend\FactureBookingController@indexRoom')->name('admin.booking-room-invoices.index');
    Route::get('EBMS/booking-breakfast-invoices/index', 'Backend\FactureBookingController@indexBreakFast')->name('admin.booking-breakfast-invoices.index');
    Route::get('EBMS/booking-service-invoices/index', 'Backend\FactureBookingController@indexService')->name('admin.booking-service-invoices.index');
    Route::get('EBMS/booking-swiming-pool-invoices/index', 'Backend\FactureBookingController@indexSwimingPool')->name('admin.booking-swiming-pool-invoices.index');
    Route::get('EBMS/booking-kidness-space-invoices/index', 'Backend\FactureBookingController@indexKidnessSpace')->name('admin.booking-kidness-space-invoices.index');
    Route::get('EBMS/booking-invoices/choose', 'Backend\FactureBookingController@choose')->name('admin.booking-invoices.choose');
    Route::get('EBMS/booking-invoices/create/{order_no}', 'Backend\FactureBookingController@create')->name('admin.booking-invoices.create');
    Route::get('EBMS/booking-invoices/edit/{invoice_number}', 'Backend\FactureBookingController@edit')->name('admin.booking-invoices.edit');
    Route::delete('EBMS/booking-invoices/destroy/{invoice_number}', 'Backend\FactureBookingController@destroy')->name('admin.booking-invoices.destroy');
    Route::get('EBMS/booking-invoices/show/{invoice_number}','Backend\FactureBookingController@show')->name('admin.booking-invoices.show');


    //suppliers routes
    Route::get('EBMS/suppliers/index', 'Backend\SupplierController@index')->name('admin.suppliers.index');
    Route::get('EBMS/suppliers/create', 'Backend\SupplierController@create')->name('admin.suppliers.create');
    Route::post('EBMS/suppliers/store', 'Backend\SupplierController@store')->name('admin.suppliers.store');
    Route::get('EBMS/suppliers/edit/{id}', 'Backend\SupplierController@edit')->name('admin.suppliers.edit');
    Route::put('EBMS/suppliers/update/{id}', 'Backend\SupplierController@update')->name('admin.suppliers.update');
    Route::delete('EBMS/suppliers/destroy/{id}', 'Backend\SupplierController@destroy')->name('admin.suppliers.destroy');

    //accompagnements routes
    Route::get('EBMS/accompagnements/index', 'Backend\AccompagnementController@index')->name('admin.accompagnements.index');
    Route::get('EBMS/accompagnements/create', 'Backend\AccompagnementController@create')->name('admin.accompagnements.create');
    Route::post('EBMS/accompagnements/store', 'Backend\AccompagnementController@store')->name('admin.accompagnements.store');
    Route::get('EBMS/accompagnements/edit/{id}', 'Backend\AccompagnementController@edit')->name('admin.accompagnements.edit');
    Route::put('EBMS/accompagnements/update/{id}', 'Backend\AccompagnementController@update')->name('admin.accompagnements.update');
    Route::delete('EBMS/accompagnements/destroy/{id}', 'Backend\AccompagnementController@destroy')->name('admin.accompagnements.destroy');

    //ingredients routes
    Route::get('EBMS/ingredients/index', 'Backend\IngredientController@index')->name('admin.ingredients.index');
    Route::get('EBMS/ingredients/create', 'Backend\IngredientController@create')->name('admin.ingredients.create');
    Route::post('EBMS/ingredients/store', 'Backend\IngredientController@store')->name('admin.ingredients.store');
    Route::get('EBMS/ingredients/edit/{id}', 'Backend\IngredientController@edit')->name('admin.ingredients.edit');
    Route::put('EBMS/ingredients/update/{id}', 'Backend\IngredientController@update')->name('admin.ingredients.update');
    Route::delete('EBMS/ingredients/destroy/{id}', 'Backend\IngredientController@destroy')->name('admin.ingredients.destroy');

    //order-kitchen routes
     Route::get('EBMS/order-kitchen/listAll', 'Backend\OrderKitchenController@listAll')->name('admin.order_kitchens.listAll');


    Route::get('EBMS/order-kitchen/index/{table_id}', 'Backend\OrderKitchenController@index')->name('admin.order_kitchens.index');
    Route::get('EBMS/order-kitchen/create/{table_id}', 'Backend\OrderKitchenController@create')->name('admin.order_kitchens.create');
    Route::post('EBMS/order-kitchen/store', 'Backend\OrderKitchenController@store')->name('admin.order_kitchens.store');
    Route::get('EBMS/order-kitchen/edit/{order_no}', 'Backend\OrderKitchenController@edit')->name('admin.order_kitchens.edit');
    Route::put('EBMS/order-kitchen/update/{order_no}', 'Backend\OrderKitchenController@update')->name('admin.order_kitchens.update');
    Route::delete('EBMS/order-kitchen/destroy/{order_no}', 'Backend\OrderKitchenController@destroy')->name('admin.order_kitchens.destroy');

    Route::get('EBMS/order-kitchen/show/{order_no}', 'Backend\OrderKitchenController@show')->name('admin.order_kitchens.show');
    Route::get('EBMS/order-kitchen/voir-commande-a-rejeter/{order_no}', 'Backend\OrderKitchenController@voirCommandeRejeter')->name('admin.order_kitchens.voir-commande-a-rejeter');

    Route::get('EBMS/order-kitchen/generatepdf/{order_no}','Backend\OrderKitchenController@htmlPdf')->name('admin.order_kitchens.generatepdf');
    Route::put('EBMS/order-kitchen/validate/{order_no}', 'Backend\OrderKitchenController@validateCommand')->name('admin.order_kitchens.validate');
    Route::put('EBMS/order-kitchen/reject/{order_no}','Backend\OrderKitchenController@reject')->name('admin.order_kitchens.reject');
    Route::put('EBMS/order-kitchen/reset/{order_no}','Backend\OrderKitchenController@reset')->name('admin.order_kitchens.reset');

    Route::get('EBMS/food-orders/export-to-excel', 'Backend\OrderKitchenController@exportToExcel')->name('admin.food-orders.export-to-excel');

    //order-drink routes
    Route::get('EBMS/order-drink/listAll', 'Backend\OrderDrinkController@listAll')->name('admin.order_drinks.listAll');

    Route::get('EBMS/order-drink/index/{table_id}', 'Backend\OrderDrinkController@index')->name('admin.order_drinks.index');
    Route::get('EBMS/order-drink/create/{table_id}', 'Backend\OrderDrinkController@create')->name('admin.order_drinks.create');
    Route::get('EBMS/order-report/choose', 'Backend\OrderDrinkController@choose')->name('admin.order-report.choose');
    Route::post('EBMS/order-drink/store', 'Backend\OrderDrinkController@store')->name('admin.order_drinks.store');
    Route::get('EBMS/order-drink/edit/{order_no}', 'Backend\OrderDrinkController@edit')->name('admin.order_drinks.edit');
    Route::put('EBMS/order-drink/update/{order_no}', 'Backend\OrderDrinkController@update')->name('admin.order_drinks.update');
    Route::delete('EBMS/order-drink/destroy/{order_no}', 'Backend\OrderDrinkController@destroy')->name('admin.order_drinks.destroy');

    Route::get('EBMS/order-drink/show/{order_no}', 'Backend\OrderDrinkController@show')->name('admin.order_drinks.show');
    Route::get('EBMS/order-drink/voir-commande-a-rejeter/{order_no}', 'Backend\OrderDrinkController@voirCommandeRejeter')->name('admin.order_drinks.voir-commande-a-rejeter');

    Route::get('EBMS/order-drink/generatepdf/{order_no}','Backend\OrderDrinkController@htmlPdf')->name('admin.order_drinks.generatepdf');
    Route::put('EBMS/order-drink/validate/{order_no}', 'Backend\OrderDrinkController@validateCommand')->name('admin.order_drinks.validate');
    Route::put('EBMS/order-drink/reject/{order_no}','Backend\OrderDrinkController@reject')->name('admin.order_drinks.reject');
    Route::put('EBMS/order-drink/reset/{order_no}','Backend\OrderDrinkController@reset')->name('admin.order_drinks.reset');

    Route::get('EBMS/drink-orders/export-to-excel', 'Backend\OrderDrinkController@exportToExcel')->name('admin.drink-orders.export-to-excel');

    //barrist-orders routes
    Route::get('EBMS/barrist-orders/listAll', 'Backend\BarristOrderController@listAll')->name('admin.barrist-orders.listAll');

    Route::get('EBMS/barrist-orders/index/{table_id}', 'Backend\BarristOrderController@index')->name('admin.barrist-orders.index');
    Route::get('EBMS/barrist-orders/create/{table_id}', 'Backend\BarristOrderController@create')->name('admin.barrist-orders.create');
    Route::post('EBMS/barrist-orders/store', 'Backend\BarristOrderController@store')->name('admin.barrist-orders.store');
    Route::get('EBMS/barrist-orders/edit/{order_no}', 'Backend\BarristOrderController@edit')->name('admin.barrist-orders.edit');
    Route::put('EBMS/barrist-orders/update/{order_no}', 'Backend\BarristOrderController@update')->name('admin.barrist-orders.update');
    Route::delete('EBMS/barrist-orders/destroy/{order_no}', 'Backend\BarristOrderController@destroy')->name('admin.barrist-orders.destroy');

    Route::get('EBMS/barrist-orders/show/{order_no}', 'Backend\BarristOrderController@show')->name('admin.barrist-orders.show');
    Route::get('EBMS/barrist-orders/voir-commande-a-rejeter/{order_no}', 'Backend\BarristOrderController@voirCommandeRejeter')->name('admin.barrist-orders.voir-commande-a-rejeter');

    Route::get('EBMS/barrist-orders/print', 'Backend\BarristOrderController@print')->name('admin.barrist-orders.print');

    Route::get('EBMS/barrist-orders/generatepdf/{order_no}','Backend\BarristOrderController@htmlPdf')->name('admin.barrist-orders.generatepdf');
    Route::put('EBMS/barrist-orders/validate/{order_no}', 'Backend\BarristOrderController@validateCommand')->name('admin.barrist-orders.validate');
    Route::put('EBMS/barrist-orders/reject/{order_no}','Backend\BarristOrderController@reject')->name('admin.barrist-orders.reject');
    Route::put('EBMS/barrist-orders/reset/{order_no}','Backend\BarristOrderController@reset')->name('admin.barrist-orders.reset');

    Route::get('EBMS/barrist-orders/export-to-excel', 'Backend\BarristOrderController@exportToExcel')->name('admin.barrist-orders.export-to-excel');

    //bartender-orders routes
    Route::get('EBMS/bartender-orders/listAll', 'Backend\BartenderOrderController@listAll')->name('admin.bartender-orders.listAll');

    Route::get('EBMS/bartender-orders/index/{table_id}', 'Backend\BartenderOrderController@index')->name('admin.bartender-orders.index');
    Route::get('EBMS/bartender-orders/create/{table_id}', 'Backend\BartenderOrderController@create')->name('admin.bartender-orders.create');
    Route::post('EBMS/bartender-orders/store', 'Backend\BartenderOrderController@store')->name('admin.bartender-orders.store');
    Route::get('EBMS/bartender-orders/edit/{order_no}', 'Backend\BartenderOrderController@edit')->name('admin.bartender-orders.edit');
    Route::put('EBMS/bartender-orders/update/{order_no}', 'Backend\BartenderOrderController@update')->name('admin.bartender-orders.update');
    Route::delete('EBMS/bartender-orders/destroy/{order_no}', 'Backend\BartenderOrderController@destroy')->name('admin.bartender-orders.destroy');

    Route::get('EBMS/bartender-orders/show/{order_no}', 'Backend\BartenderOrderController@show')->name('admin.bartender-orders.show');
    Route::get('EBMS/bartender-orders/voir-commande-a-rejeter/{order_no}', 'Backend\BartenderOrderController@voirCommandeRejeter')->name('admin.bartender-orders.voir-commande-a-rejeter');

    Route::get('EBMS/bartender-orders/print', 'Backend\BartenderOrderController@print')->name('admin.bartender-orders.print');

    Route::get('EBMS/bartender-orders/generatepdf/{order_no}','Backend\BartenderOrderController@htmlPdf')->name('admin.bartender-orders.generatepdf');
    Route::put('EBMS/bartender-orders/validate/{order_no}', 'Backend\BartenderOrderController@validateCommand')->name('admin.bartender-orders.validate');
    Route::put('EBMS/bartender-orders/reject/{order_no}','Backend\BartenderOrderController@reject')->name('admin.bartender-orders.reject');
    Route::put('EBMS/bartender-orders/reset/{order_no}','Backend\BartenderOrderController@reset')->name('admin.bartender-orders.reset');

    Route::get('EBMS/bartender-orders/export-to-excel', 'Backend\BartenderOrderController@exportToExcel')->name('admin.bartender-orders.export-to-excel');

    //consumption routes
    Route::get('EBMS/home-consumption/barrist-index/{staff_member_id}', 'Backend\HomeConsumption\HomeConsumptionController@indexBarrist')->name('admin.home-consumption-barrist.index');
    Route::get('EBMS/home-consumption/food-index/{staff_member_id}', 'Backend\HomeConsumption\HomeConsumptionController@indexFood')->name('admin.home-consumption-food.index');
    Route::get('EBMS/home-consumption/barrist-create/{staff_member_id}', 'Backend\HomeConsumption\HomeConsumptionController@createBarrist')->name('admin.home-consumption-barrist.create');
    Route::get('EBMS/home-consumption/food-create/{staff_member_id}', 'Backend\HomeConsumption\HomeConsumptionController@createFood')->name('admin.home-consumption-food.create');
    Route::post('EBMS/home-consumption/store', 'Backend\HomeConsumption\HomeConsumptionController@store')->name('admin.home-consumption.store');
    Route::delete('EBMS/home-consumption/destroy/{consumption_no}', 'Backend\HomeConsumption\HomeConsumptionController@destroy')->name('admin.home-consumption.destroy');

    Route::get('EBMS/home-consumption/show/{consumption_no}', 'Backend\HomeConsumption\HomeConsumptionController@show')->name('admin.home-consumption.show');
    Route::get('EBMS/home-consumption/voir-consommation-a-rejeter/{consumption_no}', 'Backend\HomeConsumption\HomeConsumptionController@voirConsommationRejeter')->name('admin.home-consumption.voir-consommation-a-rejeter');

    Route::get('EBMS/home-consumption/generatepdf/{consumption_no}','Backend\HomeConsumption\HomeConsumptionController@htmlPdf')->name('admin.home-consumption.generatepdf');
    Route::put('EBMS/home-consumption/reject/{consumption_no}','Backend\HomeConsumption\HomeConsumptionController@reject')->name('admin.home-consumption.reject');

    Route::get('EBMS/home-consumption/export-to-excel', 'Backend\HomeConsumption\HomeConsumptionController@exportToExcel')->name('admin.home-consumption.export-to-excel');

    //Extra Grand Stock des Boissons
    Route::get('EBMS/drink-extra-big-store-inventory/index', 'Backend\DrinkExtraBigStoreInventoryController@index')->name('admin.drink-extra-big-store-inventory.index');
    Route::get('EBMS/drink-extra-big-store-inventory/create/{code}', 'Backend\DrinkExtraBigStoreInventoryController@create')->name('admin.drink-extra-big-store-inventory.create');
    Route::post('EBMS/drink-extra-big-store-inventory/store', 'Backend\DrinkExtraBigStoreInventoryController@store')->name('admin.drink-extra-big-store-inventory.store');
    Route::get('EBMS/drink-extra-big-store-inventory/inventory/{id}', 'Backend\DrinkExtraBigStoreInventoryController@inventory')->name('admin.drink-extra-big-store-inventory.inventory');
    Route::get('EBMS/drink-extra-big-store-inventory/edit/{inventory_no}', 'Backend\DrinkExtraBigStoreInventoryController@edit')->name('admin.drink-extra-big-store-inventory.edit');
    Route::get('EBMS/drink-extra-big-store-inventory/show/{inventory_no}', 'Backend\DrinkExtraBigStoreInventoryController@show')->name('admin.drink-extra-big-store-inventory.show');
    Route::put('EBMS/drink-extra-big-store-inventory/update/{id}', 'Backend\DrinkExtraBigStoreInventoryController@update')->name('admin.drink-extra-big-store-inventory.update');
    Route::delete('EBMS/drink-extra-big-store-inventory/destroy/{id}', 'Backend\DrinkExtraBigStoreInventoryController@destroy')->name('admin.drink-extra-big-store-inventory.destroy');

    Route::get('EBMS/drink-extra-big-store-inventory/generatePdf/{inventory_no}','Backend\DrinkExtraBigStoreInventoryController@bon_inventaire')->name('admin.drink-extra-big-store-inventory.generatePdf');
    Route::put('EBMS/drink-extra-big-store-inventory/validate/{inventory_no}','Backend\DrinkExtraBigStoreInventoryController@validateInventory')->name('admin.drink-extra-big-store-inventory.validate');
    Route::put('EBMS/drink-extra-big-store-inventory/reject/{inventory_no}','Backend\DrinkExtraBigStoreInventoryController@rejectInventory')->name('admin.drink-extra-big-store-inventory.reject');
    Route::put('EBMS/drink-extra-big-store-inventory/reset/{inventory_no}','Backend\DrinkExtraBigStoreInventoryController@resetInventory')->name('admin.drink-extra-big-store-inventory.reset');

    //Grand Stock des Boissons
    Route::get('EBMS/drink-big-store-inventory/index', 'Backend\DrinkBigStoreInventoryController@index')->name('admin.drink-big-store-inventory.index');
    Route::get('EBMS/drink-big-store-inventory/create/{code}', 'Backend\DrinkBigStoreInventoryController@create')->name('admin.drink-big-store-inventory.create');
    Route::post('EBMS/drink-big-store-inventory/store', 'Backend\DrinkBigStoreInventoryController@store')->name('admin.drink-big-store-inventory.store');
    Route::get('EBMS/drink-big-store-inventory/inventory/{id}', 'Backend\DrinkBigStoreInventoryController@inventory')->name('admin.drink-big-store-inventory.inventory');
    Route::get('EBMS/drink-big-store-inventory/edit/{inventory_no}', 'Backend\DrinkBigStoreInventoryController@edit')->name('admin.drink-big-store-inventory.edit');
    Route::get('EBMS/drink-big-store-inventory/show/{inventory_no}', 'Backend\DrinkBigStoreInventoryController@show')->name('admin.drink-big-store-inventory.show');
    Route::put('EBMS/drink-big-store-inventory/update/{id}', 'Backend\DrinkBigStoreInventoryController@update')->name('admin.drink-big-store-inventory.update');
    Route::delete('EBMS/drink-big-store-inventory/destroy/{id}', 'Backend\DrinkBigStoreInventoryController@destroy')->name('admin.drink-big-store-inventory.destroy');

    Route::get('EBMS/drink-big-store-inventory/generatePdf/{inventory_no}','Backend\DrinkBigStoreInventoryController@bon_inventaire')->name('admin.drink-big-store-inventory.generatePdf');
    Route::put('EBMS/drink-big-store-inventory/validate/{inventory_no}','Backend\DrinkBigStoreInventoryController@validateInventory')->name('admin.drink-big-store-inventory.validate');
    Route::put('EBMS/drink-big-store-inventory/reject/{inventory_no}','Backend\DrinkBigStoreInventoryController@rejectInventory')->name('admin.drink-big-store-inventory.reject');
    Route::put('EBMS/drink-big-store-inventory/reset/{inventory_no}','Backend\DrinkBigStoreInventoryController@resetInventory')->name('admin.drink-big-store-inventory.reset');
    Route::get('EBMS/drink-big-store-inventory/export-to-excel/{inventory_no}','Backend\DrinkBigStoreInventoryController@exportToExcel')->name('admin.drink-big-store-inventory.export-to-excel');

    //Petit Stock des Boissons
    Route::get('EBMS/drink-small-store-inventory/index', 'Backend\DrinkSmallStoreInventoryController@index')->name('admin.drink-small-store-inventory.index');
    Route::get('EBMS/drink-small-store-inventory/create/{code}', 'Backend\DrinkSmallStoreInventoryController@create')->name('admin.drink-small-store-inventory.create');
    Route::post('EBMS/drink-small-store-inventory/store', 'Backend\DrinkSmallStoreInventoryController@store')->name('admin.drink-small-store-inventory.store');
    Route::get('EBMS/drink-small-store-inventory/inventory/{id}', 'Backend\DrinkSmallStoreInventoryController@inventory')->name('admin.drink-small-store-inventory.inventory');
    Route::get('EBMS/drink-small-store-inventory/edit/{inventory_no}', 'Backend\DrinkSmallStoreInventoryController@edit')->name('admin.drink-small-store-inventory.edit');
    Route::get('EBMS/drink-small-store-inventory/show/{inventory_no}', 'Backend\DrinkSmallStoreInventoryController@show')->name('admin.drink-small-store-inventory.show');
    Route::put('EBMS/drink-small-store-inventory/update/{id}', 'Backend\DrinkSmallStoreInventoryController@update')->name('admin.drink-small-store-inventory.update');
    Route::delete('EBMS/drink-small-store-inventory/destroy/{id}', 'Backend\DrinkSmallStoreInventoryController@destroy')->name('admin.drink-small-store-inventory.destroy');

    Route::get('EBMS/drink-small-store-inventory/generatePdf/{inventory_no}','Backend\DrinkSmallStoreInventoryController@bon_inventaire')->name('admin.drink-small-store-inventory.generatePdf');
    Route::put('EBMS/drink-small-store-inventory/validate/{inventory_no}','Backend\DrinkSmallStoreInventoryController@validateInventory')->name('admin.drink-small-store-inventory.validate');
    Route::put('EBMS/drink-small-store-inventory/reject/{inventory_no}','Backend\DrinkSmallStoreInventoryController@rejectInventory')->name('admin.drink-small-store-inventory.reject');
    Route::put('EBMS/drink-small-store-inventory/reset/{inventory_no}','Backend\DrinkSmallStoreInventoryController@resetInventory')->name('admin.drink-small-store-inventory.reset');
    Route::get('EBMS/drink-small-store-inventory/export-to-excel/{inventory_no}','Backend\DrinkSmallStoreInventoryController@exportToExcel')->name('admin.drink-small-store-inventory.export-to-excel');

    // Stock des Boissons PDG
    Route::get('PDG/private-drink-inventory/index', 'Backend\PrivateDrinkInventoryController@index')->name('admin.private-drink-inventory.index');
    Route::get('PDG/private-drink-inventory/create', 'Backend\PrivateDrinkInventoryController@create')->name('admin.private-drink-inventory.create');
    Route::post('PDG/private-drink-inventory/store', 'Backend\PrivateDrinkInventoryController@store')->name('admin.private-drink-inventory.store');
    Route::get('PDG/private-drink-inventory/show/{inventory_no}', 'Backend\PrivateDrinkInventoryController@show')->name('admin.private-drink-inventory.show');
    Route::delete('PDG/private-drink-inventory/destroy/{id}', 'Backend\PrivateDrinkInventoryController@destroy')->name('admin.private-drink-inventory.destroy');

    Route::get('PDG/private-drink-inventory/generatePdf/{inventory_no}','Backend\PrivateDrinkInventoryController@bon_inventaire')->name('admin.private-drink-inventory.generatePdf');
    Route::put('PDG/private-drink-inventory/validate/{inventory_no}','Backend\PrivateDrinkInventoryController@validateInventory')->name('admin.private-drink-inventory.validate');
    Route::put('PDG/private-drink-inventory/reject/{inventory_no}','Backend\PrivateDrinkInventoryController@rejectInventory')->name('admin.private-drink-inventory.reject');
    Route::put('PDG/private-drink-inventory/reset/{inventory_no}','Backend\PrivateDrinkInventoryController@resetInventory')->name('admin.private-drink-inventory.reset');
    Route::get('PDG/private-drink-inventory/export-to-excel/{inventory_no}','Backend\PrivateDrinkInventoryController@exportToExcel')->name('admin.private-drink-inventory.export-to-excel');

    //private-factures routes
    Route::get('MAGASIN-EDEN/private-factures/index', 'Backend\PrivateFactureController@index')->name('admin.private-factures.index');
    Route::get('MAGASIN-EDEN/private-factures/create', 'Backend\PrivateFactureController@create')->name('admin.private-factures.create');
    Route::post('MAGASIN-EDEN/private-factures/store', 'Backend\PrivateFactureController@store')->name('admin.private-factures.store');
    Route::get('MAGASIN-EDEN/private-factures/edit/{invoice_number}', 'Backend\PrivateFactureController@edit')->name('admin.private-factures.edit');
    Route::put('MAGASIN-EDEN/private-factures/update/{invoice_number}', 'Backend\PrivateFactureController@update')->name('admin.private-factures.update');
    Route::delete('MAGASIN-EDEN/private-factures/destroy/{invoice_number}', 'Backend\PrivateFactureController@destroy')->name('admin.private-factures.destroy');

    Route::get('MAGASIN-EDEN/private-factures/show/{invoice_number}', 'Backend\PrivateFactureController@show')->name('admin.private-factures.show');
    Route::get('MAGASIN-EDEN/private-factures/voir-facture-a-credit/{invoice_number}', 'Backend\PrivateFactureController@voirFactureCredit')->name('admin.private-factures.voir-facture-a-credit');
    Route::get('MAGASIN-EDEN/private-factures/voir-facture-a-recouvrer/{invoice_number}', 'Backend\PrivateFactureController@voirFactureRecouvrer')->name('admin.private-factures.voir-facture-a-recouvrer');
    Route::get('MAGASIN-EDEN/private-factures/voir-facture-a-annuler/{invoice_number}', 'Backend\PrivateFactureController@voirFactureAnnuler')->name('admin.private-factures.voir-facture-a-annuler');

    Route::get('MAGASIN-EDEN/private-factures/generatepdf/{invoice_number}','Backend\PrivateFactureController@facture')->name('admin.private-factures.generatepdf');
    Route::put('MAGASIN-EDEN/private-factures/validate-cash/{invoice_number}', 'Backend\PrivateFactureController@validerFacture')->name('admin.private-factures.validate-cash');
    Route::put('MAGASIN-EDEN/private-factures/validate-credit/{invoice_number}', 'Backend\PrivateFactureController@validerFactureCredit')->name('admin.private-factures.validate-credit');
    Route::put('MAGASIN-EDEN/private-factures/recouvrement/{invoice_number}', 'Backend\PrivateFactureController@recouvrement')->name('admin.private-factures.recouvrement');
    Route::put('MAGASIN-EDEN/private-factures/reset/{invoice_number}','Backend\PrivateFactureController@annulerFacture')->name('admin.private-factures.reset');
    Route::put('MAGASIN-EDEN/private-factures/payer-credit/{invoice_number}','Backend\PrivateFactureController@payerCredit')->name('admin.private-factures.payer-credit');

    Route::get('EBMS/private-factures/export-to-excel', 'Backend\PrivateFactureController@exportToExcel')->name('admin.private-factures.export-to-excel');

    Route::get('EBMS/private-store-report/index', 'Backend\PrivateStoreReportController@index')->name('admin.private-store-report.index');
    Route::get('EBMS/private-store-report/export-to-excel', 'Backend\PrivateStoreReportController@exportToExcel')->name('admin.private-store-report.export-to-excel');
    Route::get('EBMS/private-store-report/export-to-pdf', 'Backend\PrivateStoreReportController@exportToPdf')->name('admin.private-store-report.export-to-pdf');


    //Extra Grand Stock des nourritures
    Route::get('EBMS/food-extra-big-store-inventory/index', 'Backend\FoodExtraBigStoreInventoryController@index')->name('admin.food-extra-big-store-inventory.index');
    Route::get('EBMS/food-extra-big-store-inventory/create/{code}', 'Backend\FoodExtraBigStoreInventoryController@create')->name('admin.food-extra-big-store-inventory.create');
    Route::post('EBMS/food-extra-big-store-inventory/store', 'Backend\FoodExtraBigStoreInventoryController@store')->name('admin.food-extra-big-store-inventory.store');
    Route::get('EBMS/food-extra-big-store-inventory/inventory/{id}', 'Backend\FoodExtraBigStoreInventoryController@inventory')->name('admin.food-extra-big-store-inventory.inventory');
    Route::get('EBMS/food-extra-big-store-inventory/edit/{inventory_no}', 'Backend\FoodExtraBigStoreInventoryController@edit')->name('admin.food-extra-big-store-inventory.edit');
    Route::get('EBMS/food-extra-big-store-inventory/show/{inventory_no}', 'Backend\FoodExtraBigStoreInventoryController@show')->name('admin.food-extra-big-store-inventory.show');
    Route::put('EBMS/food-extra-big-store-inventory/update/{id}', 'Backend\FoodExtraBigStoreInventoryController@update')->name('admin.food-extra-big-store-inventory.update');
    Route::delete('EBMS/food-extra-big-store-inventory/destroy/{id}', 'Backend\FoodExtraBigStoreInventoryController@destroy')->name('admin.food-extra-big-store-inventory.destroy');

    Route::get('EBMS/food-extra-big-store-inventory/generatePdf/{inventory_no}','Backend\FoodExtraBigStoreInventoryController@bon_inventaire')->name('admin.food-extra-big-store-inventory.generatePdf');
    Route::put('EBMS/food-extra-big-store-inventory/validate/{inventory_no}','Backend\FoodExtraBigStoreInventoryController@validateInventory')->name('admin.food-extra-big-store-inventory.validate');
    Route::put('EBMS/food-extra-big-store-inventory/reject/{inventory_no}','Backend\FoodExtraBigStoreInventoryController@rejectInventory')->name('admin.food-extra-big-store-inventory.reject');
    Route::put('EBMS/food-extra-big-store-inventory/reset/{inventory_no}','Backend\FoodExtraBigStoreInventoryController@resetInventory')->name('admin.food-extra-big-store-inventory.reset');

    //Grand Stock des nourritures
    Route::get('EBMS/food-big-store-inventory/index', 'Backend\FoodBigStoreInventoryController@index')->name('admin.food-big-store-inventory.index');
    Route::get('EBMS/food-big-store-inventory/create/{code}', 'Backend\FoodBigStoreInventoryController@create')->name('admin.food-big-store-inventory.create');
    Route::post('EBMS/food-big-store-inventory/store', 'Backend\FoodBigStoreInventoryController@store')->name('admin.food-big-store-inventory.store');
    Route::get('EBMS/food-big-store-inventory/inventory/{id}', 'Backend\FoodBigStoreInventoryController@inventory')->name('admin.food-big-store-inventory.inventory');
    Route::get('EBMS/food-big-store-inventory/edit/{inventory_no}', 'Backend\FoodBigStoreInventoryController@edit')->name('admin.food-big-store-inventory.edit');
    Route::get('EBMS/food-big-store-inventory/show/{inventory_no}', 'Backend\FoodBigStoreInventoryController@show')->name('admin.food-big-store-inventory.show');
    Route::put('EBMS/food-big-store-inventory/update/{id}', 'Backend\FoodBigStoreInventoryController@update')->name('admin.food-big-store-inventory.update');
    Route::delete('EBMS/food-big-store-inventory/destroy/{id}', 'Backend\FoodBigStoreInventoryController@destroy')->name('admin.food-big-store-inventory.destroy');

    Route::get('EBMS/food-big-store-inventory/generatePdf/{inventory_no}','Backend\FoodBigStoreInventoryController@bon_inventaire')->name('admin.food-big-store-inventory.generatePdf');
    Route::put('EBMS/food-big-store-inventory/validate/{inventory_no}','Backend\FoodBigStoreInventoryController@validateInventory')->name('admin.food-big-store-inventory.validate');
    Route::put('EBMS/food-big-store-inventory/reject/{inventory_no}','Backend\FoodBigStoreInventoryController@rejectInventory')->name('admin.food-big-store-inventory.reject');
    Route::put('EBMS/food-big-store-inventory/reset/{inventory_no}','Backend\FoodBigStoreInventoryController@resetInventory')->name('admin.food-big-store-inventory.reset');
    Route::get('EBMS/food-big-store-inventory/export-to-excel/{inventory_no}','Backend\FoodBigStoreInventoryController@exportToExcel')->name('admin.food-big-store-inventory.export-to-excel');

    //Petit Stock des Nourritures
    Route::get('EBMS/food-small-store-inventory/index', 'Backend\FoodSmallStoreInventoryController@index')->name('admin.food-small-store-inventory.index');
    Route::get('EBMS/food-small-store-inventory/create/{code}', 'Backend\FoodSmallStoreInventoryController@create')->name('admin.food-small-store-inventory.create');
    Route::post('EBMS/food-small-store-inventory/store', 'Backend\FoodSmallStoreInventoryController@store')->name('admin.food-small-store-inventory.store');
    Route::get('EBMS/food-small-store-inventory/inventory/{id}', 'Backend\FoodSmallStoreInventoryController@inventory')->name('admin.food-small-store-inventory.inventory');
    Route::get('EBMS/food-small-store-inventory/edit/{inventory_no}', 'Backend\FoodSmallStoreInventoryController@edit')->name('admin.food-small-store-inventory.edit');
    Route::get('EBMS/food-small-store-inventory/show/{inventory_no}', 'Backend\FoodSmallStoreInventoryController@show')->name('admin.food-small-store-inventory.show');
    Route::put('EBMS/food-small-store-inventory/update/{id}', 'Backend\FoodSmallStoreInventoryController@update')->name('admin.food-small-store-inventory.update');
    Route::delete('EBMS/food-small-store-inventory/destroy/{id}', 'Backend\FoodSmallStoreInventoryController@destroy')->name('admin.food-small-store-inventory.destroy');

    Route::get('EBMS/food-small-store-inventory/generatePdf/{inventory_no}','Backend\FoodSmallStoreInventoryController@bon_inventaire')->name('admin.food-small-store-inventory.generatePdf');
    Route::put('EBMS/food-small-store-inventory/validate/{inventory_no}','Backend\FoodSmallStoreInventoryController@validateInventory')->name('admin.food-small-store-inventory.validate');
    Route::put('EBMS/food-small-store-inventory/reject/{inventory_no}','Backend\FoodSmallStoreInventoryController@rejectInventory')->name('admin.food-small-store-inventory.reject');
    Route::put('EBMS/food-small-store-inventory/reset/{inventory_no}','Backend\FoodSmallStoreInventoryController@resetInventory')->name('admin.food-small-store-inventory.reset');
    Route::get('EBMS/food-small-store-inventory/export-to-excel/{inventory_no}','Backend\FoodSmallStoreInventoryController@exportToExcel')->name('admin.food-small-store-inventory.export-to-excel');

    //Extra Grand Stock Des Materiels
    Route::get('EBMS/material-extra-big-store-inventory/index', 'Backend\MaterialExtraBigStoreInventoryController@index')->name('admin.material-extra-big-store-inventory.index');
    Route::get('EBMS/material-extra-big-store-inventory/create/{code}', 'Backend\MaterialExtraBigStoreInventoryController@create')->name('admin.material-extra-big-store-inventory.create');
    Route::post('EBMS/material-extra-big-store-inventory/store', 'Backend\MaterialExtraBigStoreInventoryController@store')->name('admin.material-extra-big-store-inventory.store');
    Route::get('EBMS/material-extra-big-store-inventory/inventory/{id}', 'Backend\MaterialExtraBigStoreInventoryController@inventory')->name('admin.material-extra-big-store-inventory.inventory');
    Route::get('EBMS/material-extra-big-store-inventory/edit/{inventory_no}', 'Backend\MaterialExtraBigStoreInventoryController@edit')->name('admin.material-extra-big-store-inventory.edit');
    Route::get('EBMS/material-extra-big-store-inventory/show/{inventory_no}', 'Backend\MaterialExtraBigStoreInventoryController@show')->name('admin.material-extra-big-store-inventory.show');
    Route::put('EBMS/material-extra-big-store-inventory/update/{id}', 'Backend\MaterialExtraBigStoreInventoryController@update')->name('admin.material-extra-big-store-inventory.update');
    Route::delete('EBMS/material-extra-big-store-inventory/destroy/{id}', 'Backend\MaterialExtraBigStoreInventoryController@destroy')->name('admin.material-extra-big-store-inventory.destroy');

    Route::get('EBMS/material-extra-big-store-inventory/generatePdf/{inventory_no}','Backend\MaterialExtraBigStoreInventoryController@bon_inventaire')->name('admin.material-extra-big-store-inventory.generatePdf');
    Route::put('EBMS/material-extra-big-store-inventory/validate/{inventory_no}','Backend\MaterialExtraBigStoreInventoryController@validateInventory')->name('admin.material-extra-big-store-inventory.validate');
    Route::put('EBMS/material-extra-big-store-inventory/reject/{inventory_no}','Backend\MaterialExtraBigStoreInventoryController@rejectInventory')->name('admin.material-extra-big-store-inventory.reject');
    Route::put('EBMS/material-extra-big-store-inventory/reset/{inventory_no}','Backend\MaterialExtraBigStoreInventoryController@resetInventory')->name('admin.material-extra-big-store-inventory.reset');

    //Grand Stock Des Materiels
    Route::get('EBMS/material-big-store-inventory/index', 'Backend\MaterialBigStoreInventoryController@index')->name('admin.material-big-store-inventory.index');
    Route::get('EBMS/material-big-store-inventory/create/{code}', 'Backend\MaterialBigStoreInventoryController@create')->name('admin.material-big-store-inventory.create');
    Route::post('EBMS/material-big-store-inventory/store', 'Backend\MaterialBigStoreInventoryController@store')->name('admin.material-big-store-inventory.store');
    Route::get('EBMS/material-big-store-inventory/inventory/{id}', 'Backend\MaterialBigStoreInventoryController@inventory')->name('admin.material-big-store-inventory.inventory');
    Route::get('EBMS/material-big-store-inventory/edit/{inventory_no}', 'Backend\MaterialBigStoreInventoryController@edit')->name('admin.material-big-store-inventory.edit');
    Route::get('EBMS/material-big-store-inventory/show/{inventory_no}', 'Backend\MaterialBigStoreInventoryController@show')->name('admin.material-big-store-inventory.show');
    Route::put('EBMS/material-big-store-inventory/update/{id}', 'Backend\MaterialBigStoreInventoryController@update')->name('admin.material-big-store-inventory.update');
    Route::delete('EBMS/material-big-store-inventory/destroy/{id}', 'Backend\MaterialBigStoreInventoryController@destroy')->name('admin.material-big-store-inventory.destroy');

    Route::get('EBMS/material-big-store-inventory/generatePdf/{inventory_no}','Backend\MaterialBigStoreInventoryController@bon_inventaire')->name('admin.material-big-store-inventory.generatePdf');
    Route::get('EBMS/material-md-store-inventory/export-to-excel/{inventory_no}','Backend\MaterialBigStoreInventoryController@exportToExcel')->name('admin.material-md-store-inventory.export-to-excel');
    Route::put('EBMS/material-big-store-inventory/validate/{inventory_no}','Backend\MaterialBigStoreInventoryController@validateInventory')->name('admin.material-big-store-inventory.validate');
    Route::put('EBMS/material-big-store-inventory/reject/{inventory_no}','Backend\MaterialBigStoreInventoryController@rejectInventory')->name('admin.material-big-store-inventory.reject');
    Route::put('EBMS/material-big-store-inventory/reset/{inventory_no}','Backend\MaterialBigStoreInventoryController@resetInventory')->name('admin.material-big-store-inventory.reset');

    //Petit Stock des materiels
    Route::get('EBMS/material-small-store-inventory/index', 'Backend\MaterialSmallStoreInventoryController@index')->name('admin.material-small-store-inventory.index');
    Route::get('EBMS/material-small-store-inventory/create/{code}', 'Backend\MaterialSmallStoreInventoryController@create')->name('admin.material-small-store-inventory.create');
    Route::post('EBMS/material-small-store-inventory/store', 'Backend\MaterialSmallStoreInventoryController@store')->name('admin.material-small-store-inventory.store');
    Route::get('EBMS/material-small-store-inventory/inventory/{id}', 'Backend\MaterialSmallStoreInventoryController@inventory')->name('admin.material-small-store-inventory.inventory');
    Route::get('EBMS/material-small-store-inventory/edit/{inventory_no}', 'Backend\MaterialSmallStoreInventoryController@edit')->name('admin.material-small-store-inventory.edit');
    Route::get('EBMS/material-small-store-inventory/show/{inventory_no}', 'Backend\MaterialSmallStoreInventoryController@show')->name('admin.material-small-store-inventory.show');
    Route::put('EBMS/material-small-store-inventory/update/{id}', 'Backend\MaterialSmallStoreInventoryController@update')->name('admin.material-small-store-inventory.update');
    Route::delete('EBMS/material-small-store-inventory/destroy/{id}', 'Backend\MaterialSmallStoreInventoryController@destroy')->name('admin.material-small-store-inventory.destroy');

    Route::get('EBMS/material-small-store-inventory/generatePdf/{inventory_no}','Backend\MaterialSmallStoreInventoryController@bon_inventaire')->name('admin.material-small-store-inventory.generatePdf');
    Route::put('EBMS/material-small-store-inventory/validate/{inventory_no}','Backend\MaterialSmallStoreInventoryController@validateInventory')->name('admin.material-small-store-inventory.validate');
    Route::put('EBMS/material-small-store-inventory/reject/{inventory_no}','Backend\MaterialSmallStoreInventoryController@rejectInventory')->name('admin.material-small-store-inventory.reject');
    Route::put('EBMS/material-small-store-inventory/reset/{inventory_no}','Backend\MaterialSmallStoreInventoryController@resetInventory')->name('admin.material-small-store-inventory.reset');

    //drink-requisitions routes
    Route::get('EBMS/drink-requisitions/index', 'Backend\DrinkRequisitionController@index')->name('admin.drink-requisitions.index');
    Route::get('EBMS/drink-requisitions/choose', 'Backend\DrinkRequisitionController@choose')->name('admin.drink-requisitions.choose');

    Route::get('EBMS/drink-big-store-requisitions/create', 'Backend\DrinkRequisitionController@createFromBig')->name('admin.drink-big-requisitions.create');
    Route::get('EBMS/drink-medium-store-requisitions/create', 'Backend\DrinkRequisitionController@create')->name('admin.drink-medium-requisitions.create');
    Route::post('EBMS/drink-requisitions/store', 'Backend\DrinkRequisitionController@store')->name('admin.drink-requisitions.store');
    Route::get('EBMS/drink-requisitions/edit/{requisition_no}', 'Backend\DrinkRequisitionController@edit')->name('admin.drink-requisitions.edit');
    Route::put('EBMS/drink-requisitions/update/{requisition_no}', 'Backend\DrinkRequisitionController@update')->name('admin.drink-requisitions.update');
    Route::delete('EBMS/drink-requisitions/destroy/{requisition_no}', 'Backend\DrinkRequisitionController@destroy')->name('admin.drink-requisitions.destroy');

    Route::get('EBMS/drink-requisitions/show/{requisition_no}', 'Backend\DrinkRequisitionController@show')->name('admin.drink-requisitions.show');

    Route::get('EBMS/drink-requisitions/generatepdf/{requisition_no}','Backend\DrinkRequisitionController@demande_requisition')->name('admin.drink-requisitions.generatepdf');
    Route::put('EBMS/drink-requisitions/validate/{requisition_no}', 'Backend\DrinkRequisitionController@validateRequisition')->name('admin.drink-requisitions.validate');
    Route::put('EBMS/drink-requisitions/reject/{requisition_no}','Backend\DrinkRequisitionController@reject')->name('admin.drink-requisitions.reject');
    Route::put('EBMS/drink-requisitions/reset/{requisition_no}','Backend\DrinkRequisitionController@reset')->name('admin.drink-requisitions.reset');
    Route::put('EBMS/drink-requisitions/confirm/{requisition_no}','Backend\DrinkRequisitionController@confirm')->name('admin.drink-requisitions.confirm');
    Route::put('EBMS/drink-requisitions/approuve/{requisition_no}','Backend\DrinkRequisitionController@approuve')->name('admin.drink-requisitions.approuve');

    //food-requisitions routes
    Route::get('EBMS/food-requisitions/index', 'Backend\FoodRequisitionController@index')->name('admin.food-requisitions.index');
    Route::get('EBMS/food-requisitions/create', 'Backend\FoodRequisitionController@create')->name('admin.food-requisitions.create');
    Route::post('EBMS/food-requisitions/store', 'Backend\FoodRequisitionController@store')->name('admin.food-requisitions.store');
    Route::get('EBMS/food-requisitions/edit/{requisition_no}', 'Backend\FoodRequisitionController@edit')->name('admin.food-requisitions.edit');
    Route::put('EBMS/food-requisitions/update/{requisition_no}', 'Backend\FoodRequisitionController@update')->name('admin.food-requisitions.update');
    Route::delete('EBMS/admin.food-requisitions/destroy/{requisition_no}', 'Backend\FoodRequisitionController@destroy')->name('admin.food-requisitions.destroy');

    Route::get('EBMS/food-requisitions/show/{requisition_no}', 'Backend\FoodRequisitionController@show')->name('admin.food-requisitions.show');

    Route::get('EBMS/food-requisitions/generatepdf/{requisition_no}','Backend\FoodRequisitionController@demande_requisition')->name('admin.food-requisitions.generatepdf');
    Route::put('EBMS/food-requisitions/validate/{requisition_no}', 'Backend\FoodRequisitionController@validateRequisition')->name('admin.food-requisitions.validate');
    Route::put('EBMS/food-requisitions/reject/{requisition_no}','Backend\FoodRequisitionController@reject')->name('admin.food-requisitions.reject');
    Route::put('EBMS/food-requisitions/reset/{requisition_no}','Backend\FoodRequisitionController@reset')->name('admin.food-requisitions.reset');
    Route::put('EBMS/food-requisitions/confirm/{requisition_no}','Backend\FoodRequisitionController@confirm')->name('admin.food-requisitions.confirm');
    Route::put('EBMS/food-requisitions/approuve/{requisition_no}','Backend\FoodRequisitionController@approuve')->name('admin.food-requisitions.approuve');

    //material-requisitions routes
    Route::get('EBMS/material-requisitions/index', 'Backend\MaterialRequisitionController@index')->name('admin.material-requisitions.index');
    Route::get('EBMS/material-requisitions/choose', 'Backend\MaterialRequisitionController@choose')->name('admin.material-requisitions.choose');
    Route::get('EBMS/material-requisitions/create', 'Backend\MaterialRequisitionController@create')->name('admin.material-requisitions.create');
    Route::get('EBMS/material-requisitions/createFromBig', 'Backend\MaterialRequisitionController@createFromBig')->name('admin.material-requisitions.createFromBig');
    Route::post('EBMS/material-requisitions/store', 'Backend\MaterialRequisitionController@store')->name('admin.material-requisitions.store');
    Route::get('EBMS/material-requisitions/edit/{requisition_no}', 'Backend\MaterialRequisitionController@edit')->name('admin.material-requisitions.edit');
    Route::put('EBMS/material-requisitions/update/{requisition_no}', 'Backend\MaterialRequisitionController@update')->name('admin.material-requisitions.update');
    Route::delete('EBMS/admin.material-requisitions/destroy/{requisition_no}', 'Backend\MaterialRequisitionController@destroy')->name('admin.material-requisitions.destroy');

    Route::get('EBMS/material-requisitions/show/{requisition_no}', 'Backend\MaterialRequisitionController@show')->name('admin.material-requisitions.show');

    Route::get('EBMS/material-requisitions/generatepdf/{requisition_no}','Backend\MaterialRequisitionController@demande_requisition')->name('admin.material-requisitions.generatepdf');
    Route::put('EBMS/material-requisitions/validate/{requisition_no}', 'Backend\MaterialRequisitionController@validateRequisition')->name('admin.material-requisitions.validate');
    Route::put('EBMS/material-requisitions/reject/{requisition_no}','Backend\MaterialRequisitionController@reject')->name('admin.material-requisitions.reject');
    Route::put('EBMS/material-requisitions/reset/{requisition_no}','Backend\MaterialRequisitionController@reset')->name('admin.material-requisitions.reset');
    Route::put('EBMS/material-requisitions/confirm/{requisition_no}','Backend\MaterialRequisitionController@confirm')->name('admin.material-requisitions.confirm');
    Route::put('EBMS/material-requisitions/approuve/{requisition_no}','Backend\MaterialRequisitionController@approuve')->name('admin.material-requisitions.approuve');

    //barrist-requisitions routes
    Route::get('EBMS/barrist-requisitions/index', 'Backend\BarristRequisitionController@index')->name('admin.barrist-requisitions.index');
    Route::get('EBMS/barrist-requisitions/choose', 'Backend\BarristRequisitionController@choose')->name('admin.barrist-requisitions.choose');
    Route::get('EBMS/barrist-requisition-food/create', 'Backend\BarristRequisitionController@createFood')->name('admin.barrist-requisition-food.create');
    Route::get('EBMS/barrist-requisition-drink/create', 'Backend\BarristRequisitionController@createDrink')->name('admin.barrist-requisition-drink.create');
    Route::post('EBMS/barrist-requisition-drink/store', 'Backend\BarristRequisitionController@storeDrink')->name('admin.barrist-requisition-drink.store');
    Route::post('EBMS/barrist-requisition-food/store', 'Backend\BarristRequisitionController@storeFood')->name('admin.barrist-requisition-food.store');
    Route::get('EBMS/barrist-requisitions/edit/{requisition_no}', 'Backend\BarristRequisitionController@edit')->name('admin.barrist-requisitions.edit');
    Route::put('EBMS/barrist-requisitions/update/{requisition_no}', 'Backend\BarristRequisitionController@update')->name('admin.barrist-requisitions.update');
    Route::delete('EBMS/admin.barrist-requisitions/destroy/{requisition_no}', 'Backend\BarristRequisitionController@destroy')->name('admin.barrist-requisitions.destroy');

    Route::get('EBMS/barrist-requisitions/show/{requisition_no}', 'Backend\BarristRequisitionController@show')->name('admin.barrist-requisitions.show');

    Route::get('EBMS/barrist-requisitions/generatepdf/{requisition_no}','Backend\BarristRequisitionController@demande_requisition')->name('admin.barrist-requisitions.generatepdf');
    Route::put('EBMS/barrist-requisitions/validate/{requisition_no}', 'Backend\BarristRequisitionController@validateRequisition')->name('admin.barrist-requisitions.validate');
    Route::put('EBMS/barrist-requisitions/reject/{requisition_no}','Backend\BarristRequisitionController@reject')->name('admin.barrist-requisitions.reject');
    Route::put('EBMS/barrist-requisitions/reset/{requisition_no}','Backend\BarristRequisitionController@reset')->name('admin.barrist-requisitions.reset');
    Route::put('EBMS/barrist-requisitions/confirm/{requisition_no}','Backend\BarristRequisitionController@confirm')->name('admin.barrist-requisitions.confirm');
    Route::put('EBMS/barrist-requisitions/approuve/{requisition_no}','Backend\BarristRequisitionController@approuve')->name('admin.barrist-requisitions.approuve');

    //drink transfer routes
    Route::get('EBMS/drink-transfers/index', 'Backend\DrinkTransferController@index')->name('admin.drink-transfers.index');
    Route::get('EBMS/drink-big-store-transfers/create/{requisition_no}', 'Backend\DrinkTransferController@createFromBig')->name('admin.drink-big-transfers.create');
    Route::get('EBMS/drink-medium-store-transfers/create/{requisition_no}', 'Backend\DrinkTransferController@create')->name('admin.drink-medium-transfers.create');
    Route::post('EBMS/drink-transfers/store', 'Backend\DrinkTransferController@store')->name('admin.drink-transfers.store');
    Route::get('EBMS/drink-transfers/edit/{transfer_no}', 'Backend\DrinkTransferController@edit')->name('admin.drink-transfers.edit');
    Route::put('EBMS/drink-transfers/update/{transfer_no}', 'Backend\DrinkTransferController@update')->name('admin.drink-transfers.update');
    Route::delete('EBMS/drink-transfers/destroy/{transfer_no}', 'Backend\DrinkTransferController@destroy')->name('admin.drink-transfers.destroy');
    Route::get('EBMS/drink-transfers/show/{transfer_no}','Backend\DrinkTransferController@show')->name('admin.drink-transfers.show');

    Route::get('EBMS/drink-transfers/bonTransfert/{transfer_no}','Backend\DrinkTransferController@bonTransfert')->name('admin.drink-transfers.bonTransfert');
    Route::put('EBMS/drink-transfers/validate/{transfer_no}', 'Backend\DrinkTransferController@validateTransfer')->name('admin.drink-transfers.validate');
    Route::put('EBMS/drink-transfers/reject/{transfer_no}','Backend\DrinkTransferController@reject')->name('admin.drink-transfers.reject');
    Route::put('EBMS/drink-transfers/reset/{transfer_no}','Backend\DrinkTransferController@reset')->name('admin.drink-transfers.reset');
    Route::put('EBMS/drink-transfers/confirm/{transfer_no}','Backend\DrinkTransferController@confirm')->name('admin.drink-transfers.confirm');
    Route::put('EBMS/drink-transfers/approuve/{transfer_no}','Backend\DrinkTransferController@approuve')->name('admin.drink-transfers.approuve');

    Route::get('EBMS/drink-transfers/export-to-excel','Backend\DrinkTransferController@exportToExcel')->name('admin.drink-transfers.export-to-excel');

    //food transfer routes
    Route::get('EBMS/food-transfers/index', 'Backend\FoodTransferController@index')->name('admin.food-transfers.index');
    Route::get('EBMS/food-transfers/create/{requisition_no}', 'Backend\FoodTransferController@create')->name('admin.food-transfers.create');
    Route::post('EBMS/food-transfers/store', 'Backend\FoodTransferController@store')->name('admin.food-transfers.store');
    Route::get('EBMS/food-transfers/edit/{transfer_no}', 'Backend\FoodTransferController@edit')->name('admin.food-transfers.edit');
    Route::put('EBMS/food-transfers/update/{transfer_no}', 'Backend\FoodTransferController@update')->name('admin.food-transfers.update');
    Route::delete('EBMS/food-transfers/destroy/{transfer_no}', 'Backend\FoodTransferController@destroy')->name('admin.food-transfers.destroy');
    Route::get('EBMS/food-transfers/show/{transfer_no}','Backend\FoodTransferController@show')->name('admin.food-transfers.show');

    Route::get('EBMS/food-transfers/bonTransfert/{transfer_no}','Backend\FoodTransferController@bonTransfert')->name('admin.food-transfers.bonTransfert');
    Route::put('EBMS/food-transfers/validate/{transfer_no}', 'Backend\FoodTransferController@validateTransfer')->name('admin.food-transfers.validate');
    Route::put('EBMS/food-transfers/reject/{transfer_no}','Backend\FoodTransferController@reject')->name('admin.food-transfers.reject');
    Route::put('EBMS/food-transfers/reset/{transfer_no}','Backend\FoodTransferController@reset')->name('admin.food-transfers.reset');
    Route::put('EBMS/food-transfers/confirm/{transfer_no}','Backend\FoodTransferController@confirm')->name('admin.food-transfers.confirm');
    Route::put('EBMS/food-transfers/approuve/{transfer_no}','Backend\FoodTransferController@approuve')->name('admin.food-transfers.approuve');
    Route::get('EBMS/food-transfers/portion/{transfer_no}', 'Backend\FoodTransferController@portion')->name('admin.food-transfers.portion');
    Route::put('EBMS/food-transfers/storePortion/{transfer_no}', 'Backend\FoodTransferController@storePortion')->name('admin.food-transfers.storePortion');
    Route::put('EBMS/food-transfers/validatePortion/{transfer_no}', 'Backend\FoodTransferController@validatePortion')->name('admin.food-transfers.validatePortion');

    //material transfer routes
    Route::get('EBMS/material-transfers/index', 'Backend\MaterialTransferController@index')->name('admin.material-transfers.index');
    Route::get('EBMS/material-transfers/create/{requisition_no}', 'Backend\MaterialTransferController@create')->name('admin.material-transfers.create');
    Route::get('EBMS/material-transfers/createFromBig/{requisition_no}', 'Backend\MaterialTransferController@createFromBig')->name('admin.material-transfers.createFromBig');
    Route::post('EBMS/material-transfers/store', 'Backend\MaterialTransferController@store')->name('admin.material-transfers.store');
    Route::post('EBMS/material-transfers/storeFromBig', 'Backend\MaterialTransferController@storeFromBig')->name('admin.material-transfers.storeFromBig');
    Route::get('EBMS/material-transfers/edit/{transfer_no}', 'Backend\MaterialTransferController@edit')->name('admin.material-transfers.edit');
    Route::put('EBMS/material-transfers/update/{transfer_no}', 'Backend\MaterialTransferController@update')->name('admin.material-transfers.update');
    Route::delete('EBMS/material-transfers/destroy/{transfer_no}', 'Backend\MaterialTransferController@destroy')->name('admin.material-transfers.destroy');
    Route::get('EBMS/material-transfers/show/{transfer_no}','Backend\MaterialTransferController@show')->name('admin.material-transfers.show');

    Route::get('EBMS/material-transfers/bonTransfert/{transfer_no}','Backend\MaterialTransferController@bonTransfert')->name('admin.material-transfers.bonTransfert');
    Route::put('EBMS/material-transfers/validate/{transfer_no}', 'Backend\MaterialTransferController@validateTransfer')->name('admin.material-transfers.validate');
    Route::put('EBMS/material-transfers/reject/{transfer_no}','Backend\MaterialTransferController@reject')->name('admin.material-transfers.reject');
    Route::put('EBMS/material-transfers/reset/{transfer_no}','Backend\MaterialTransferController@reset')->name('admin.material-transfers.reset');
    Route::put('EBMS/material-transfers/confirm/{transfer_no}','Backend\MaterialTransferController@confirm')->name('admin.material-transfers.confirm');
    Route::put('EBMS/material-transfers/approuve/{transfer_no}','Backend\MaterialTransferController@approuve')->name('admin.material-transfers.approuve');


    //barrist transfer routes
    Route::get('EBMS/barrist-transfers/index', 'Backend\BarristTransferController@index')->name('admin.barrist-transfers.index');
    Route::get('EBMS/barrist-transfer-drink/create/{requisition_no}', 'Backend\BarristTransferController@createDrink')->name('admin.barrist-transfer-drink.create');
    Route::get('EBMS/barrist-transfer-food/create/{requisition_no}', 'Backend\BarristTransferController@createFood')->name('admin.barrist-transfer-food.create');
    Route::post('EBMS/barrist-transfer-drink/store', 'Backend\BarristTransferController@storeDrink')->name('admin.barrist-transfer-drink.store');
    Route::post('EBMS/barrist-transfer-food/store', 'Backend\BarristTransferController@storeFood')->name('admin.barrist-transfer-food.store');
    Route::get('EBMS/barrist-transfers/edit/{transfer_no}', 'Backend\BarristTransferController@edit')->name('admin.barrist-transfers.edit');
    Route::put('EBMS/barrist-transfers/update/{transfer_no}', 'Backend\BarristTransferController@update')->name('admin.barrist-transfers.update');
    Route::delete('EBMS/barrist-transfers/destroy/{transfer_no}', 'Backend\BarristTransferController@destroy')->name('admin.barrist-transfers.destroy');
    Route::get('EBMS/barrist-transfers/show/{transfer_no}','Backend\BarristTransferController@show')->name('admin.barrist-transfers.show');

    Route::get('EBMS/barrist-transfers/bonTransfert/{transfer_no}','Backend\BarristTransferController@bonTransfert')->name('admin.barrist-transfers.bonTransfert');
    Route::put('EBMS/barrist-transfers/validate/{transfer_no}', 'Backend\BarristTransferController@validateTransfer')->name('admin.barrist-transfers.validate');
    Route::put('EBMS/barrist-transfers/reject/{transfer_no}','Backend\BarristTransferController@reject')->name('admin.barrist-transfers.reject');
    Route::put('EBMS/barrist-transfers/reset/{transfer_no}','Backend\BarristTransferController@reset')->name('admin.barrist-transfers.reset');
    Route::put('EBMS/barrist-transfers/confirm/{transfer_no}','Backend\BarristTransferController@confirm')->name('admin.barrist-transfers.confirm');
    Route::put('EBMS/barrist-transfer-drink/approuve/{transfer_no}','Backend\BarristTransferController@approuveDrink')->name('admin.barrist-transfer-drink.approuve');
    Route::put('EBMS/barrist-transfer-food/approuve/{transfer_no}','Backend\BarristTransferController@approuveFood')->name('admin.barrist-transfer-food.approuve');
    Route::get('EBMS/barrist-transfers/portion/{transfer_no}', 'Backend\BarristTransferController@portion')->name('admin.barrist-transfers.portion');
    Route::put('EBMS/barrist-transfers/storePortion/{transfer_no}', 'Backend\BarristTransferController@storePortion')->name('admin.barrist-transfers.storePortion');
    Route::put('EBMS/barrist-transfers/validatePortion/{transfer_no}', 'Backend\BarristTransferController@validatePortion')->name('admin.barrist-transfers.validatePortion');
    Route::get('EBMS/barrist-transformation/create', 'Backend\BarristProductionStoreController@create')->name('admin.barrist-transformation.create');
    Route::post('EBMS/barrist-transformation/store', 'Backend\BarristProductionStoreController@store')->name('admin.barrist-transformation.store');

    //material return routes
    Route::get('EBMS/material-return/index', 'Backend\MaterialReturnController@index')->name('admin.material-return.index');
    Route::get('EBMS/material-return/create/{transfer_no}', 'Backend\MaterialReturnController@create')->name('admin.material-return.create');
    Route::post('EBMS/material-return/store', 'Backend\MaterialReturnController@store')->name('admin.material-return.store');
    Route::get('EBMS/material-return/edit/{return_no}', 'Backend\MaterialReturnController@edit')->name('admin.material-return.edit');
    Route::put('EBMS/material-return/update/{return_no}', 'Backend\MaterialReturnController@update')->name('admin.material-return.update');
    Route::delete('EBMS/material-return/destroy/{return_no}', 'Backend\MaterialReturnController@destroy')->name('admin.material-return.destroy');
    Route::get('EBMS/material-return/show/{return_no}','Backend\MaterialReturnController@show')->name('admin.material-return.show');

    Route::get('EBMS/material-return/bonRetour/{return_no}','Backend\MaterialReturnController@bonRetour')->name('admin.material-return.bonRetour');
    Route::put('EBMS/material-return/validate/{return_no}', 'Backend\MaterialReturnController@validateReturn')->name('admin.material-return.validate');
    Route::put('EBMS/material-return/reject/{return_no}','Backend\MaterialReturnController@reject')->name('admin.material-return.reject');
    Route::put('EBMS/material-return/reset/{return_no}','Backend\MaterialReturnController@reset')->name('admin.material-return.reset');
    Route::put('EBMS/material-return/confirm/{return_no}','Backend\MaterialReturnController@confirm')->name('admin.material-return.confirm');
    Route::put('EBMS/material-return/approuve/{return_no}','Backend\MaterialReturnController@approuve')->name('admin.material-return.approuve');

    //material purchases routes
    Route::get('EBMS/material-purchases/index', 'Backend\MaterialPurchaseController@index')->name('admin.material-purchases.index');
    Route::get('EBMS/material-purchases/create', 'Backend\MaterialPurchaseController@create')->name('admin.material-purchases.create');
    Route::post('EBMS/material-purchases/store', 'Backend\MaterialPurchaseController@store')->name('admin.material-purchases.store');
    Route::get('EBMS/material-purchases/edit/{purchase_no}', 'Backend\MaterialPurchaseController@edit')->name('admin.material-purchases.edit');
    Route::put('EBMS/material-purchases/update/{purchase_no}', 'Backend\MaterialPurchaseController@update')->name('admin.material-purchases.update');
    Route::delete('EBMS/material-purchases/destroy/{purchase_no}', 'Backend\MaterialPurchaseController@destroy')->name('admin.material-purchases.destroy');
    Route::get('EBMS/material-purchases/show/{purchase_no}','Backend\MaterialPurchaseController@show')->name('admin.material-purchases.show');

    Route::get('EBMS/material-purchases/materialPurchase/{purchase_no}','Backend\MaterialPurchaseController@materialPurchase')->name('admin.material-purchases.materialPurchase');
    Route::put('EBMS/material-purchases/validate/{purchase_no}', 'Backend\MaterialPurchaseController@validatePurchase')->name('admin.material-purchases.validate');
    Route::put('EBMS/material-purchases/reject/{purchase_no}','Backend\MaterialPurchaseController@reject')->name('admin.material-purchases.reject');
    Route::put('EBMS/material-purchases/reset/{purchase_no}','Backend\MaterialPurchaseController@reset')->name('admin.material-purchases.reset');
    Route::put('EBMS/material-purchases/confirm/{purchase_no}','Backend\MaterialPurchaseController@confirm')->name('admin.material-purchases.confirm');
    Route::put('EBMS/material-purchases/approuve/{purchase_no}','Backend\MaterialPurchaseController@approuve')->name('admin.material-purchases.approuve');

    Route::get('EBMS/material-purchases/export-to-excel','Backend\MaterialPurchaseController@exportToExcel')->name('admin.material-purchases.export-to-excel');

    //plan-purchase-materials routes
    Route::get('EBMS/plan-purchase-choice', 'Backend\PlanPurchaseDrinkController@choice')->name('admin.plan-purchase.choice');

    Route::get('EBMS/plan-purchase-materials/index', 'Backend\PlanPurchaseMaterialController@index')->name('admin.plan-purchase-materials.index');
    Route::get('EBMS/plan-purchase-materials/create', 'Backend\PlanPurchaseMaterialController@create')->name('admin.plan-purchase-materials.create');
    Route::post('EBMS/plan-purchase-materials/store', 'Backend\PlanPurchaseMaterialController@store')->name('admin.plan-purchase-materials.store');
    Route::get('EBMS/plan-purchase-materials/edit/{plan_no}', 'Backend\PlanPurchaseMaterialController@edit')->name('admin.plan-purchase-materials.edit');
    Route::put('EBMS/plan-purchase-materials/update/{plan_no}', 'Backend\PlanPurchaseMaterialController@update')->name('admin.plan-purchase-materials.update');
    Route::delete('EBMS/plan-purchase-materials/destroy/{plan_no}', 'Backend\PlanPurchaseMaterialController@destroy')->name('admin.plan-purchase-materials.destroy');
    Route::get('EBMS/plan-purchase-materials/show/{plan_no}','Backend\PlanPurchaseMaterialController@show')->name('admin.plan-purchase-materials.show');

    Route::get('EBMS/plan-purchase-materials/fichePlan/{plan_no}','Backend\PlanPurchaseMaterialController@fichePlan')->name('admin.plan-purchase-materials.fichePlan');
    Route::put('EBMS/plan-purchase-materials/validate/{plan_no}', 'Backend\PlanPurchaseMaterialController@validatePlan')->name('admin.plan-purchase-materials.validate');
    Route::put('EBMS/plan-purchase-materials/reject/{plan_no}','Backend\PlanPurchaseMaterialController@reject')->name('admin.plan-purchase-materials.reject');
    Route::put('EBMS/plan-purchase-materials/reset/{plan_no}','Backend\PlanPurchaseMaterialController@reset')->name('admin.plan-purchase-materials.reset');
    Route::put('EBMS/plan-purchase-materials/confirm/{plan_no}','Backend\PlanPurchaseMaterialController@confirm')->name('admin.plan-purchase-materials.confirm');
    Route::put('EBMS/plan-purchase-materials/approuve/{plan_no}','Backend\PlanPurchaseMaterialController@approuve')->name('admin.plan-purchase-materials.approuve');

    Route::get('EBMS/plan-purchase-materials/export-to-excel','Backend\PlanPurchaseMaterialController@exportToExcel')->name('admin.plan-purchase-materials.export-to-excel');

    //plan-purchase-drinks routes
    Route::get('EBMS/plan-purchase-drinks/index', 'Backend\PlanPurchaseDrinkController@index')->name('admin.plan-purchase-drinks.index');
    Route::get('EBMS/plan-purchase-drinks/create', 'Backend\PlanPurchaseDrinkController@create')->name('admin.plan-purchase-drinks.create');
    Route::post('EBMS/plan-purchase-drinks/store', 'Backend\PlanPurchaseDrinkController@store')->name('admin.plan-purchase-drinks.store');
    Route::get('EBMS/plan-purchase-drinks/edit/{plan_no}', 'Backend\PlanPurchaseDrinkController@edit')->name('admin.plan-purchase-drinks.edit');
    Route::put('EBMS/plan-purchase-drinks/update/{plan_no}', 'Backend\PlanPurchaseDrinkController@update')->name('admin.plan-purchase-drinks.update');
    Route::delete('EBMS/plan-purchase-drinks/destroy/{plan_no}', 'Backend\PlanPurchaseDrinkController@destroy')->name('admin.plan-purchase-drinks.destroy');
    Route::get('EBMS/plan-purchase-drinks/show/{plan_no}','Backend\PlanPurchaseDrinkController@show')->name('admin.plan-purchase-drinks.show');

    Route::get('EBMS/plan-purchase-drinks/fichePlan/{plan_no}','Backend\PlanPurchaseDrinkController@fichePlan')->name('admin.plan-purchase-drinks.fichePlan');
    Route::put('EBMS/plan-purchase-drinks/validate/{plan_no}', 'Backend\PlanPurchaseDrinkController@validatePlan')->name('admin.plan-purchase-drinks.validate');
    Route::put('EBMS/plan-purchase-drinks/reject/{plan_no}','Backend\PlanPurchaseDrinkController@reject')->name('admin.plan-purchase-drinks.reject');
    Route::put('EBMS/plan-purchase-drinks/reset/{plan_no}','Backend\PlanPurchaseDrinkController@reset')->name('admin.plan-purchase-drinks.reset');
    Route::put('EBMS/plan-purchase-drinks/confirm/{plan_no}','Backend\PlanPurchaseDrinkController@confirm')->name('admin.plan-purchase-drinks.confirm');
    Route::put('EBMS/plan-purchase-drinks/approuve/{plan_no}','Backend\PlanPurchaseDrinkController@approuve')->name('admin.plan-purchase-drinks.approuve');

    Route::get('EBMS/plan-purchase-drinks/export-to-excel','Backend\PlanPurchaseDrinkController@exportToExcel')->name('admin.plan-purchase-drinks.export-to-excel');

    //plan-purchase-foods routes
    Route::get('EBMS/plan-purchase-foods/index', 'Backend\PlanPurchaseFoodController@index')->name('admin.plan-purchase-foods.index');
    Route::get('EBMS/plan-purchase-foods/create', 'Backend\PlanPurchaseFoodController@create')->name('admin.plan-purchase-foods.create');
    Route::post('EBMS/plan-purchase-foods/store', 'Backend\PlanPurchaseFoodController@store')->name('admin.plan-purchase-foods.store');
    Route::get('EBMS/plan-purchase-foods/edit/{plan_no}', 'Backend\PlanPurchaseFoodController@edit')->name('admin.plan-purchase-foods.edit');
    Route::put('EBMS/plan-purchase-foods/update/{plan_no}', 'Backend\PlanPurchaseFoodController@update')->name('admin.plan-purchase-foods.update');
    Route::delete('EBMS/plan-purchase-foods/destroy/{plan_no}', 'Backend\PlanPurchaseFoodController@destroy')->name('admin.plan-purchase-foods.destroy');
    Route::get('EBMS/plan-purchase-foods/show/{plan_no}','Backend\PlanPurchaseFoodController@show')->name('admin.plan-purchase-foods.show');

    Route::get('EBMS/plan-purchase-foods/fichePlan/{plan_no}','Backend\PlanPurchaseFoodController@fichePlan')->name('admin.plan-purchase-foods.fichePlan');
    Route::put('EBMS/plan-purchase-foods/validate/{plan_no}', 'Backend\PlanPurchaseFoodController@validatePlan')->name('admin.plan-purchase-foods.validate');
    Route::put('EBMS/plan-purchase-foods/reject/{plan_no}','Backend\PlanPurchaseFoodController@reject')->name('admin.plan-purchase-foods.reject');
    Route::put('EBMS/plan-purchase-foods/reset/{plan_no}','Backend\PlanPurchaseFoodController@reset')->name('admin.plan-purchase-foods.reset');
    Route::put('EBMS/plan-purchase-foods/confirm/{plan_no}','Backend\PlanPurchaseFoodController@confirm')->name('admin.plan-purchase-foods.confirm');
    Route::put('EBMS/plan-purchase-foods/approuve/{plan_no}','Backend\PlanPurchaseFoodController@approuve')->name('admin.plan-purchase-foods.approuve');

    Route::get('EBMS/plan-purchase-foods/export-to-excel','Backend\PlanPurchaseFoodController@exportToExcel')->name('admin.plan-purchase-foods.export-to-excel');

    //drink purchases routes
    Route::get('EBMS/drink-purchases/index', 'Backend\DrinkPurchaseController@index')->name('admin.drink-purchases.index');
    Route::get('EBMS/drink-purchases/create/', 'Backend\DrinkPurchaseController@create')->name('admin.drink-purchases.create');
    Route::post('EBMS/drink-purchases/store', 'Backend\DrinkPurchaseController@store')->name('admin.drink-purchases.store');
    Route::get('EBMS/drink-purchases/edit/{purchase_no}', 'Backend\DrinkPurchaseController@edit')->name('admin.drink-purchases.edit');
    Route::put('EBMS/drink-purchases/update/{purchase_no}', 'Backend\DrinkPurchaseController@update')->name('admin.drink-purchases.update');
    Route::delete('EBMS/drink-purchases/destroy/{purchase_no}', 'Backend\DrinkPurchaseController@destroy')->name('admin.drink-purchases.destroy');
    Route::get('EBMS/drink-purchases/show/{purchase_no}','Backend\DrinkPurchaseController@show')->name('admin.drink-purchases.show');

    Route::get('EBMS/drink-purchases/drinkPurchase/{purchase_no}','Backend\DrinkPurchaseController@drinkPurchase')->name('admin.drink-purchases.drinkPurchase');
    Route::put('EBMS/drink-purchases/validate/{purchase_no}', 'Backend\DrinkPurchaseController@validatePurchase')->name('admin.drink-purchases.validate');
    Route::put('EBMS/drink-purchases/reject/{purchase_no}','Backend\DrinkPurchaseController@reject')->name('admin.drink-purchases.reject');
    Route::put('EBMS/drink-purchases/reset/{purchase_no}','Backend\DrinkPurchaseController@reset')->name('admin.drink-purchases.reset');
    Route::put('EBMS/drink-purchases/confirm/{purchase_no}','Backend\DrinkPurchaseController@confirm')->name('admin.drink-purchases.confirm');
    Route::put('EBMS/drink-purchases/approuve/{purchase_no}','Backend\DrinkPurchaseController@approuve')->name('admin.drink-purchases.approuve');

    //food purchases routes
    Route::get('EBMS/food-purchases/index', 'Backend\FoodPurchaseController@index')->name('admin.food-purchases.index');
    Route::get('EBMS/food-purchases/create', 'Backend\FoodPurchaseController@create')->name('admin.food-purchases.create');
    Route::post('EBMS/food-purchases/store', 'Backend\FoodPurchaseController@store')->name('admin.food-purchases.store');
    Route::get('EBMS/food-purchases/edit/{purchase_no}', 'Backend\FoodPurchaseController@edit')->name('admin.food-purchases.edit');
    Route::put('EBMS/food-purchases/update/{purchase_no}', 'Backend\FoodPurchaseController@update')->name('admin.food-purchases.update');
    Route::delete('EBMS/food-purchases/destroy/{purchase_no}', 'Backend\FoodPurchaseController@destroy')->name('admin.food-purchases.destroy');
    Route::get('EBMS/food-purchases/show/{purchase_no}','Backend\FoodPurchaseController@show')->name('admin.food-purchases.show');

    Route::get('EBMS/food-purchases/foodPurchase/{purchase_no}','Backend\FoodPurchaseController@foodPurchase')->name('admin.food-purchases.foodPurchase');
    Route::put('EBMS/food-purchases/validate/{purchase_no}', 'Backend\FoodPurchaseController@validatePurchase')->name('admin.food-purchases.validate');
    Route::put('EBMS/food-purchases/reject/{purchase_no}','Backend\FoodPurchaseController@reject')->name('admin.food-purchases.reject');
    Route::put('EBMS/food-purchases/reset/{purchase_no}','Backend\FoodPurchaseController@reset')->name('admin.food-purchases.reset');
    Route::put('EBMS/food-purchases/confirm/{purchase_no}','Backend\FoodPurchaseController@confirm')->name('admin.food-purchases.confirm');
    Route::put('EBMS/food-purchases/approuve/{purchase_no}','Backend\FoodPurchaseController@approuve')->name('admin.food-purchases.approuve');

    //material supplier-orders routes
    Route::get('EBMS/material-supplier-orders/index', 'Backend\MaterialSupplierOrderController@index')->name('admin.material-supplier-orders.index');
    Route::get('EBMS/material-supplier-orders/create/{purchase_no}', 'Backend\MaterialSupplierOrderController@create')->name('admin.material-supplier-orders.create');
    Route::post('EBMS/material-supplier-orders/store', 'Backend\MaterialSupplierOrderController@store')->name('admin.material-supplier-orders.store');
    Route::get('EBMS/material-supplier-orders/edit/{order_no}', 'Backend\MaterialSupplierOrderController@edit')->name('admin.material-supplier-orders.edit');
    Route::put('EBMS/material-supplier-orders/update/{order_no}', 'Backend\MaterialSupplierOrderController@update')->name('admin.material-supplier-orders.update');
    Route::delete('EBMS/material-supplier-orders/destroy/{order_no}', 'Backend\MaterialSupplierOrderController@destroy')->name('admin.material-supplier-orders.destroy');
    Route::get('EBMS/material-supplier-orders/show/{order_no}','Backend\MaterialSupplierOrderController@show')->name('admin.material-supplier-orders.show');

    Route::get('EBMS/material-supplier-orders/materialSupplierOrder/{order_no}','Backend\MaterialSupplierOrderController@materialSupplierOrder')->name('admin.material-supplier-orders.materialSupplierOrder');
    Route::put('EBMS/material-supplier-orders/validate/{order_no}', 'Backend\MaterialSupplierOrderController@validateOrder')->name('admin.material-supplier-orders.validate');
    Route::put('EBMS/material-supplier-orders/reject/{order_no}','Backend\MaterialSupplierOrderController@reject')->name('admin.material-supplier-orders.reject');
    Route::put('EBMS/material-supplier-orders/reset/{order_no}','Backend\MaterialSupplierOrderController@reset')->name('admin.material-supplier-orders.reset');
    Route::put('EBMS/material-supplier-orders/confirm/{order_no}','Backend\MaterialSupplierOrderController@confirm')->name('admin.material-supplier-orders.confirm');
    Route::put('EBMS/material-supplier-orders/approuve/{order_no}','Backend\MaterialSupplierOrderController@approuve')->name('admin.material-supplier-orders.approuve');

    Route::get('EBMS/material-supplier-orders/export-to-excel','Backend\MaterialSupplierOrderController@exportToExcel')->name('admin.material-supplier-orders.export-to-excel');

    //drink supplier-orders routes
    Route::get('EBMS/drink-supplier-orders/index', 'Backend\DrinkSupplierOrderController@index')->name('admin.drink-supplier-orders.index');
    Route::get('EBMS/drink-supplier-orders/create/{purchase_no}', 'Backend\DrinkSupplierOrderController@create')->name('admin.drink-supplier-orders.create');
    Route::post('EBMS/drink-supplier-orders/store', 'Backend\DrinkSupplierOrderController@store')->name('admin.drink-supplier-orders.store');
    Route::get('EBMS/drink-supplier-orders/edit/{order_no}', 'Backend\DrinkSupplierOrderController@edit')->name('admin.drink-supplier-orders.edit');
    Route::put('EBMS/drink-supplier-orders/update/{order_no}', 'Backend\DrinkSupplierOrderController@update')->name('admin.drink-supplier-orders.update');
    Route::delete('EBMS/drink-supplier-orders/destroy/{order_no}', 'Backend\DrinkSupplierOrderController@destroy')->name('admin.drink-supplier-orders.destroy');
    Route::get('EBMS/drink-supplier-orders/show/{order_no}','Backend\DrinkSupplierOrderController@show')->name('admin.drink-supplier-orders.show');

    Route::get('EBMS/drink-supplier-orders/drinkSupplierOrder/{order_no}','Backend\DrinkSupplierOrderController@drinkSupplierOrder')->name('admin.drink-supplier-orders.drinkSupplierOrder');
    Route::put('EBMS/drink-supplier-orders/validate/{order_no}', 'Backend\DrinkSupplierOrderController@validateOrder')->name('admin.drink-supplier-orders.validate');
    Route::put('EBMS/drink-supplier-orders/reject/{order_no}','Backend\DrinkSupplierOrderController@reject')->name('admin.drink-supplier-orders.reject');
    Route::put('EBMS/drink-supplier-orders/reset/{order_no}','Backend\DrinkSupplierOrderController@reset')->name('admin.drink-supplier-orders.reset');
    Route::put('EBMS/drink-supplier-orders/confirm/{order_no}','Backend\DrinkSupplierOrderController@confirm')->name('admin.drink-supplier-orders.confirm');
    Route::put('EBMS/drink-supplier-orders/approuve/{order_no}','Backend\DrinkSupplierOrderController@approuve')->name('admin.drink-supplier-orders.approuve');

    //food supplier-orders routes
    Route::get('EBMS/food-supplier-orders/index', 'Backend\FoodSupplierOrderController@index')->name('admin.food-supplier-orders.index');
    Route::get('EBMS/food-supplier-orders/create/{purchase_no}', 'Backend\FoodSupplierOrderController@create')->name('admin.food-supplier-orders.create');
    Route::post('EBMS/food-supplier-orders/store', 'Backend\FoodSupplierOrderController@store')->name('admin.food-supplier-orders.store');
    Route::get('EBMS/food-supplier-orders/edit/{order_no}', 'Backend\FoodSupplierOrderController@edit')->name('admin.food-supplier-orders.edit');
    Route::put('EBMS/food-supplier-orders/update/{order_no}', 'Backend\FoodSupplierOrderController@update')->name('admin.food-supplier-orders.update');
    Route::delete('EBMS/food-supplier-orders/destroy/{order_no}', 'Backend\FoodSupplierOrderController@destroy')->name('admin.food-supplier-orders.destroy');
    Route::get('EBMS/food-supplier-orders/show/{order_no}','Backend\FoodSupplierOrderController@show')->name('admin.food-supplier-orders.show');

    Route::get('EBMS/food-supplier-orders/foodSupplierOrder/{order_no}','Backend\FoodSupplierOrderController@foodSupplierOrder')->name('admin.food-supplier-orders.foodSupplierOrder');
    Route::put('EBMS/food-supplier-orders/validate/{order_no}', 'Backend\FoodSupplierOrderController@validateOrder')->name('admin.food-supplier-orders.validate');
    Route::put('EBMS/food-supplier-orders/reject/{order_no}','Backend\FoodSupplierOrderController@reject')->name('admin.food-supplier-orders.reject');
    Route::put('EBMS/food-supplier-orders/reset/{order_no}','Backend\FoodSupplierOrderController@reset')->name('admin.food-supplier-orders.reset');
    Route::put('EBMS/food-supplier-orders/confirm/{order_no}','Backend\FoodSupplierOrderController@confirm')->name('admin.food-supplier-orders.confirm');
    Route::put('EBMS/food-supplier-orders/approuve/{order_no}','Backend\FoodSupplierOrderController@approuve')->name('admin.food-supplier-orders.approuve');

    //material receptions routes
    Route::get('EBMS/material-receptions/index', 'Backend\MaterialReceptionController@index')->name('admin.material-receptions.index');
    Route::get('EBMS/material-receptions/create/{order_no}', 'Backend\MaterialReceptionController@create')->name('admin.material-receptions.create');
    Route::get('EBMS/material-reception-without-order/create/{purchase_no}', 'Backend\MaterialReceptionController@createWithoutOrder')->name('admin.material-reception-without-order.create');
    Route::post('EBMS/material-receptions/store', 'Backend\MaterialReceptionController@store')->name('admin.material-receptions.store');
    Route::post('EBMS/material-reception-without-order/store', 'Backend\MaterialReceptionController@storeWithoutOrder')->name('admin.material-reception-without-order.store');
    Route::get('EBMS/material-receptions/edit/{reception_no}', 'Backend\MaterialReceptionController@edit')->name('admin.material-receptions.edit');
    Route::put('EBMS/material-receptions/update/{reception_no}', 'Backend\MaterialReceptionController@update')->name('admin.material-receptions.update');
    Route::delete('EBMS/material-receptions/destroy/{reception_no}', 'Backend\MaterialReceptionController@destroy')->name('admin.material-receptions.destroy');
    Route::get('EBMS/material-receptions/show/{reception_no}','Backend\MaterialReceptionController@show')->name('admin.material-receptions.show');

    Route::get('EBMS/material-receptions/fiche_reception/{reception_no}','Backend\MaterialReceptionController@fiche_reception')->name('admin.material-receptions.fiche_reception');
    Route::put('EBMS/material-receptions/validate/{reception_no}', 'Backend\MaterialReceptionController@validateReception')->name('admin.material-receptions.validate');
    Route::put('EBMS/material-receptions/reject/{reception_no}','Backend\MaterialReceptionController@reject')->name('admin.material-receptions.reject');
    Route::put('EBMS/material-receptions/reset/{reception_no}','Backend\MaterialReceptionController@reset')->name('admin.material-receptions.reset');
    Route::put('EBMS/material-receptions/confirm/{reception_no}','Backend\MaterialReceptionController@confirm')->name('admin.material-receptions.confirm');
    Route::put('EBMS/material-receptions/approuve/{reception_no}','Backend\MaterialReceptionController@approuve')->name('admin.material-receptions.approuve');

    Route::get('EBMS/material-receptions/export-to-excel','Backend\MaterialReceptionController@exportToExcel')->name('admin.material-receptions.export-to-excel');

    //drink receptions routes
    Route::get('EBMS/drink-receptions/index', 'Backend\DrinkReceptionController@index')->name('admin.drink-receptions.index');
    Route::get('EBMS/drink-receptions/create/{order_no}', 'Backend\DrinkReceptionController@create')->name('admin.drink-receptions.create');
    Route::get('EBMS/drink-reception-without-order/create/{purchase_no}', 'Backend\DrinkReceptionController@createWithoutOrder')->name('admin.drink-reception-without-order.create');
    Route::post('EBMS/drink-receptions/store', 'Backend\DrinkReceptionController@store')->name('admin.drink-receptions.store');
    Route::post('EBMS/drink-reception-without-order/store', 'Backend\DrinkReceptionController@storeWithoutOrder')->name('admin.drink-reception-without-order.store');
    Route::get('EBMS/drink-receptions/edit/{reception_no}', 'Backend\DrinkReceptionController@edit')->name('admin.drink-receptions.edit');
    Route::put('EBMS/drink-receptions/update/{reception_no}', 'Backend\DrinkReceptionController@update')->name('admin.drink-receptions.update');
    Route::delete('EBMS/drink-receptions/destroy/{reception_no}', 'Backend\DrinkReceptionController@destroy')->name('admin.drink-receptions.destroy');
    Route::get('EBMS/drink-receptions/show/{reception_no}','Backend\DrinkReceptionController@show')->name('admin.drink-receptions.show');

    Route::get('EBMS/drink-receptions/fiche_reception/{reception_no}','Backend\DrinkReceptionController@fiche_reception')->name('admin.drink-receptions.fiche_reception');
    Route::put('EBMS/drink-receptions/validate/{reception_no}', 'Backend\DrinkReceptionController@validateReception')->name('admin.drink-receptions.validate');
    Route::put('EBMS/drink-receptions/reject/{reception_no}','Backend\DrinkReceptionController@reject')->name('admin.drink-receptions.reject');
    Route::put('EBMS/drink-receptions/reset/{reception_no}','Backend\DrinkReceptionController@reset')->name('admin.drink-receptions.reset');
    Route::put('EBMS/drink-receptions/confirm/{reception_no}','Backend\DrinkReceptionController@confirm')->name('admin.drink-receptions.confirm');
    Route::put('EBMS/drink-receptions/approuve/{reception_no}','Backend\DrinkReceptionController@approuve')->name('admin.drink-receptions.approuve');

    Route::get('EBMS/drink-receptions/export-to-excel','Backend\DrinkReceptionController@exportToExcel')->name('admin.drink-receptions.export-to-excel');

    //food receptions routes
    Route::get('EBMS/food-receptions/index', 'Backend\FoodReceptionController@index')->name('admin.food-receptions.index');
    Route::get('EBMS/food-receptions/create/{order_no}', 'Backend\FoodReceptionController@create')->name('admin.food-receptions.create');
    Route::get('EBMS/food-reception-without-order/create/{purchase_no}', 'Backend\FoodReceptionController@createWithoutOrder')->name('admin.food-reception-without-order.create');
    Route::post('EBMS/food-receptions/store', 'Backend\FoodReceptionController@store')->name('admin.food-receptions.store');
    Route::post('EBMS/food-reception-without-order/store', 'Backend\FoodReceptionController@storeWithoutOrder')->name('admin.food-reception-without-order.store');
    Route::get('EBMS/food-receptions/edit/{reception_no}', 'Backend\FoodReceptionController@edit')->name('admin.food-receptions.edit');
    Route::put('EBMS/food-receptions/update/{reception_no}', 'Backend\FoodReceptionController@update')->name('admin.food-receptions.update');
    Route::delete('EBMS/food-receptions/destroy/{reception_no}', 'Backend\FoodReceptionController@destroy')->name('admin.food-receptions.destroy');
    Route::get('EBMS/food-receptions/show/{reception_no}','Backend\FoodReceptionController@show')->name('admin.food-receptions.show');

    Route::get('EBMS/food-receptions/fiche_reception/{reception_no}','Backend\FoodReceptionController@fiche_reception')->name('admin.food-receptions.fiche_reception');
    Route::put('EBMS/food-receptions/validate/{reception_no}', 'Backend\FoodReceptionController@validateReception')->name('admin.food-receptions.validate');
    Route::put('EBMS/food-receptions/reject/{reception_no}','Backend\FoodReceptionController@reject')->name('admin.food-receptions.reject');
    Route::put('EBMS/food-receptions/reset/{reception_no}','Backend\FoodReceptionController@reset')->name('admin.food-receptions.reset');
    Route::put('EBMS/food-receptions/confirm/{reception_no}','Backend\FoodReceptionController@confirm')->name('admin.food-receptions.confirm');
    Route::put('EBMS/food-receptions/approuve/{reception_no}','Backend\FoodReceptionController@approuve')->name('admin.food-receptions.approuve');

    Route::get('EBMS/food-receptions/export-to-excel','Backend\FoodReceptionController@exportToExcel')->name('admin.food-receptions.export-to-excel');

    //material stockins routes
    Route::get('EBMS/material-stockins/index', 'Backend\MaterialStockinController@index')->name('admin.material-stockins.index');
    Route::get('EBMS/material-stockins/choose', 'Backend\MaterialStockinController@choose')->name('admin.material-stockins.choose');
    Route::get('EBMS/material-stockins/create', 'Backend\MaterialStockinController@create')->name('admin.material-stockins.create');
    Route::get('EBMS/material-stockins/createFromBig', 'Backend\MaterialStockinController@createFromBig')->name('admin.material-stockins.createFromBig');
    Route::get('EBMS/material-stockins/createFromSmall', 'Backend\MaterialStockinController@createFromSmall')->name('admin.material-stockins.createFromSmall');
    Route::post('EBMS/material-stockins/store', 'Backend\MaterialStockinController@store')->name('admin.material-stockins.store');
    Route::post('EBMS/material-stockins/storeFromBig', 'Backend\MaterialStockinController@storeFromBig')->name('admin.material-stockins.storeFromBig');
    Route::post('EBMS/material-stockins/storeFromSmall', 'Backend\MaterialStockinController@storeFromSmall')->name('admin.material-stockins.storeFromSmall');
    Route::get('EBMS/material-stockins/edit/{stockin_no}', 'Backend\MaterialStockinController@edit')->name('admin.material-stockins.edit');
    Route::put('EBMS/material-stockins/update/{stockin_no}', 'Backend\MaterialStockinController@update')->name('admin.material-stockins.update');
    Route::delete('EBMS/material-stockins/destroy/{stockin_no}', 'Backend\MaterialStockinController@destroy')->name('admin.material-stockins.destroy');
    Route::get('EBMS/material-stockins/show/{stockin_no}','Backend\MaterialStockinController@show')->name('admin.material-stockins.show');

    Route::get('EBMS/material-stockins/bonEntree/{stockin_no}','Backend\MaterialStockinController@bonEntree')->name('admin.material-stockins.bonEntree');
    Route::put('EBMS/material-stockins/validate/{stockin_no}', 'Backend\MaterialStockinController@validateStockin')->name('admin.material-stockins.validate');
    Route::put('EBMS/material-stockins/reject/{stockin_no}','Backend\MaterialStockinController@reject')->name('admin.material-stockins.reject');
    Route::put('EBMS/material-stockins/reset/{stockin_no}','Backend\MaterialStockinController@reset')->name('admin.material-stockins.reset');
    Route::put('EBMS/material-stockins/confirm/{stockin_no}','Backend\MaterialStockinController@confirm')->name('admin.material-stockins.confirm');
    Route::put('EBMS/material-stockins/approuve/{stockin_no}','Backend\MaterialStockinController@approuve')->name('admin.material-stockins.approuve');

    //drink stockins routes
    Route::get('EBMS/drink-stockins/index', 'Backend\DrinkStockinController@index')->name('admin.drink-stockins.index');
    Route::get('EBMS/drink-stockins/create', 'Backend\DrinkStockinController@create')->name('admin.drink-stockins.create');
    Route::post('EBMS/drink-stockins/store', 'Backend\DrinkStockinController@store')->name('admin.drink-stockins.store');
    Route::get('EBMS/drink-stockins/edit/{stockin_no}', 'Backend\DrinkStockinController@edit')->name('admin.drink-stockins.edit');
    Route::put('EBMS/drink-stockins/update/{stockin_no}', 'Backend\DrinkStockinController@update')->name('admin.drink-stockins.update');
    Route::delete('EBMS/drink-stockins/destroy/{stockin_no}', 'Backend\DrinkStockinController@destroy')->name('admin.drink-stockins.destroy');
    Route::get('EBMS/drink-stockins/show/{stockin_no}','Backend\DrinkStockinController@show')->name('admin.drink-stockins.show');

    Route::get('EBMS/drink-stockins/bonEntree/{stockin_no}','Backend\DrinkStockinController@bonEntree')->name('admin.drink-stockins.bonEntree');
    Route::put('EBMS/drink-stockins/validate/{stockin_no}', 'Backend\DrinkStockinController@validateStockin')->name('admin.drink-stockins.validate');
    Route::put('EBMS/drink-stockins/reject/{stockin_no}','Backend\DrinkStockinController@reject')->name('admin.drink-stockins.reject');
    Route::put('EBMS/drink-stockins/reset/{stockin_no}','Backend\DrinkStockinController@reset')->name('admin.drink-stockins.reset');
    Route::put('EBMS/drink-stockins/confirm/{stockin_no}','Backend\DrinkStockinController@confirm')->name('admin.drink-stockins.confirm');
    Route::put('EBMS/drink-stockins/approuve/{stockin_no}','Backend\DrinkStockinController@approuve')->name('admin.drink-stockins.approuve');

    Route::get('EBMS/drink-stockins/export-to-excel','Backend\DrinkStockinController@exportToExcel')->name('admin.drink-stockins.export-to-excel');

    //private drink stockins routes
    Route::get('PDG/private-drink-stockins/index', 'Backend\PrivateDrinkStockinController@index')->name('admin.private-drink-stockins.index');
    Route::get('PDG/private-drink-stockins/create', 'Backend\PrivateDrinkStockinController@create')->name('admin.private-drink-stockins.create');
    Route::post('PDG/private-drink-stockins/store', 'Backend\PrivateDrinkStockinController@store')->name('admin.private-drink-stockins.store');
    Route::delete('PDG/private-drink-stockins/destroy/{stockin_no}', 'Backend\PrivateDrinkStockinController@destroy')->name('admin.private-drink-stockins.destroy');
    Route::get('PDG/private-drink-stockins/show/{stockin_no}','Backend\PrivateDrinkStockinController@show')->name('admin.private-drink-stockins.show');

    Route::get('PDG/private-drink-stockins/bonEntree/{stockin_no}','Backend\PrivateDrinkStockinController@bonEntree')->name('admin.private-drink-stockins.bonEntree');
    Route::put('PDG/private-drink-stockins/validate/{stockin_no}', 'Backend\PrivateDrinkStockinController@validateStockin')->name('admin.private-drink-stockins.validate');
    Route::put('PDG/private-drink-stockins/reject/{stockin_no}','Backend\PrivateDrinkStockinController@reject')->name('admin.private-drink-stockins.reject');
    Route::put('PDG/private-drink-stockins/reset/{stockin_no}','Backend\PrivateDrinkStockinController@reset')->name('admin.private-drink-stockins.reset');
    Route::put('PDG/private-drink-stockins/confirm/{stockin_no}','Backend\PrivateDrinkStockinController@confirm')->name('admin.private-drink-stockins.confirm');
    Route::put('PDG/private-drink-stockins/approuve/{stockin_no}','Backend\PrivateDrinkStockinController@approuve')->name('admin.private-drink-stockins.approuve');

    Route::get('PDG/private-drink-stockins/export-to-excel','Backend\PrivateDrinkStockinController@exportToExcel')->name('admin.private-drink-stockins.export-to-excel');

    //food stockins routes
    Route::get('EBMS/food-stockins/index', 'Backend\FoodStockinController@index')->name('admin.food-stockins.index');
    Route::get('EBMS/food-stockins/create', 'Backend\FoodStockinController@create')->name('admin.food-stockins.create');
    Route::post('EBMS/food-stockins/store', 'Backend\FoodStockinController@store')->name('admin.food-stockins.store');
    Route::get('EBMS/food-stockins/edit/{stockin_no}', 'Backend\FoodStockinController@edit')->name('admin.food-stockins.edit');
    Route::put('EBMS/food-stockins/update/{stockin_no}', 'Backend\FoodStockinController@update')->name('admin.food-stockins.update');
    Route::delete('EBMS/food-stockins/destroy/{stockin_no}', 'Backend\FoodStockinController@destroy')->name('admin.food-stockins.destroy');
    Route::get('EBMS/food-stockins/show/{stockin_no}','Backend\FoodStockinController@show')->name('admin.food-stockins.show');

    Route::get('EBMS/food-stockins/bonEntree/{stockin_no}','Backend\FoodStockinController@bonEntree')->name('admin.food-stockins.bonEntree');
    Route::put('EBMS/food-stockins/validate/{stockin_no}', 'Backend\FoodStockinController@validateStockin')->name('admin.food-stockins.validate');
    Route::put('EBMS/food-stockins/reject/{stockin_no}','Backend\FoodStockinController@reject')->name('admin.food-stockins.reject');
    Route::put('EBMS/food-stockins/reset/{stockin_no}','Backend\FoodStockinController@reset')->name('admin.food-stockins.reset');
    Route::put('EBMS/food-stockins/confirm/{stockin_no}','Backend\FoodStockinController@confirm')->name('admin.food-stockins.confirm');
    Route::put('EBMS/food-stockins/approuve/{stockin_no}','Backend\FoodStockinController@approuve')->name('admin.food-stockins.approuve');

    //material stockouts routes
    Route::get('EBMS/material-stockouts/index', 'Backend\MaterialStockoutController@index')->name('admin.material-stockouts.index');
    Route::get('EBMS/material-stockouts/choose', 'Backend\MaterialStockoutController@choose')->name('admin.material-stockouts.choose');
    Route::get('EBMS/material-stockouts/create', 'Backend\MaterialStockoutController@create')->name('admin.material-stockouts.create');
    Route::get('EBMS/material-stockouts/createFromBig', 'Backend\MaterialStockoutController@createFromBig')->name('admin.material-stockouts.createFromBig');
    Route::get('EBMS/material-stockouts/createFromSmall', 'Backend\MaterialStockoutController@createFromSmall')->name('admin.material-stockouts.createFromSmall');
    Route::post('EBMS/material-stockouts/store', 'Backend\MaterialStockoutController@store')->name('admin.material-stockouts.store');
    Route::post('EBMS/material-stockouts/storeFromBig', 'Backend\MaterialStockoutController@storeFromBig')->name('admin.material-stockouts.storeFromBig');
    Route::post('EBMS/material-stockouts/storeFromSmall', 'Backend\MaterialStockoutController@storeFromSmall')->name('admin.material-stockouts.storeFromSmall');
    Route::get('EBMS/material-stockouts/edit/{stockout_no}', 'Backend\MaterialStockoutController@edit')->name('admin.material-stockouts.edit');
    Route::put('EBMS/material-stockouts/update/{stockout_no}', 'Backend\MaterialStockoutController@update')->name('admin.material-stockouts.update');
    Route::delete('EBMS/material-stockouts/destroy/{stockout_no}', 'Backend\MaterialStockoutController@destroy')->name('admin.material-stockouts.destroy');
    Route::get('EBMS/material-stockouts/show/{stockout_no}','Backend\MaterialStockoutController@show')->name('admin.material-stockouts.show');

    Route::get('EBMS/material-stockouts/bonSortie/{stockout_no}','Backend\MaterialStockoutController@bonSortie')->name('admin.material-stockouts.bonSortie');
    Route::put('EBMS/material-stockouts/validate/{stockout_no}', 'Backend\MaterialStockoutController@validateStockout')->name('admin.material-stockouts.validate');
    Route::put('EBMS/material-stockouts/reject/{stockout_no}','Backend\MaterialStockoutController@reject')->name('admin.material-stockouts.reject');
    Route::put('EBMS/material-stockouts/reset/{stockout_no}','Backend\MaterialStockoutController@reset')->name('admin.material-stockouts.reset');
    Route::put('EBMS/material-stockouts/confirm/{stockout_no}','Backend\MaterialStockoutController@confirm')->name('admin.material-stockouts.confirm');
    Route::put('EBMS/material-stockouts/approuve/{stockout_no}','Backend\MaterialStockoutController@approuve')->name('admin.material-stockouts.approuve');

    //drink stockouts routes
    Route::get('EBMS/drink-stockouts/index', 'Backend\DrinkStockoutController@index')->name('admin.drink-stockouts.index');
    Route::get('EBMS/drink-stockouts/create', 'Backend\DrinkStockoutController@create')->name('admin.drink-stockouts.create');
    Route::post('EBMS/drink-stockouts/store', 'Backend\DrinkStockoutController@store')->name('admin.drink-stockouts.store');
    Route::get('EBMS/drink-stockouts/edit/{stockout_no}', 'Backend\DrinkStockoutController@edit')->name('admin.drink-stockouts.edit');
    Route::put('EBMS/drink-stockouts/update/{stockout_no}', 'Backend\DrinkStockoutController@update')->name('admin.drink-stockouts.update');
    Route::delete('EBMS/drink-stockouts/destroy/{stockout_no}', 'Backend\DrinkStockoutController@destroy')->name('admin.drink-stockouts.destroy');
    Route::get('EBMS/drink-stockouts/show/{stockout_no}','Backend\DrinkStockoutController@show')->name('admin.drink-stockouts.show');

    Route::get('EBMS/drink-stockouts/bonSortie/{stockout_no}','Backend\DrinkStockoutController@bonSortie')->name('admin.drink-stockouts.bonSortie');
    Route::put('EBMS/drink-stockouts/validate/{stockout_no}', 'Backend\DrinkStockoutController@validateStockout')->name('admin.drink-stockouts.validate');
    Route::put('EBMS/drink-stockouts/reject/{stockout_no}','Backend\DrinkStockoutController@reject')->name('admin.drink-stockouts.reject');
    Route::put('EBMS/drink-stockouts/reset/{stockout_no}','Backend\DrinkStockoutController@reset')->name('admin.drink-stockouts.reset');
    Route::put('EBMS/drink-stockouts/confirm/{stockout_no}','Backend\DrinkStockoutController@confirm')->name('admin.drink-stockouts.confirm');
    Route::put('EBMS/drink-stockouts/approuve/{stockout_no}','Backend\DrinkStockoutController@approuve')->name('admin.drink-stockouts.approuve');
    Route::get('EBMS/drink-stockouts/export-to-excel','Backend\DrinkStockoutController@exportToExcel')->name('admin.drink-stockouts.export-to-excel');


    //private-drink stockouts routes
    Route::get('PDG/private-drink-stockouts/index', 'Backend\PrivateDrinkStockoutController@index')->name('admin.private-drink-stockouts.index');
    Route::get('PDG/private-drink-stockouts/create', 'Backend\PrivateDrinkStockoutController@create')->name('admin.private-drink-stockouts.create');
    Route::post('PDG/private-drink-stockouts/store', 'Backend\PrivateDrinkStockoutController@store')->name('admin.private-drink-stockouts.store');
    Route::delete('PDG/private-drink-stockouts/destroy/{stockout_no}', 'Backend\PrivateDrinkStockoutController@destroy')->name('admin.private-drink-stockouts.destroy');
    Route::get('PDG/private-drink-stockouts/show/{stockout_no}','Backend\PrivateDrinkStockoutController@show')->name('admin.private-drink-stockouts.show');

    Route::get('PDG/private-drink-stockouts/bonSortie/{stockout_no}','Backend\PrivateDrinkStockoutController@bonSortie')->name('admin.private-drink-stockouts.bonSortie');
    Route::put('PDG/private-drink-stockouts/validate/{stockout_no}', 'Backend\PrivateDrinkStockoutController@validateStockout')->name('admin.private-drink-stockouts.validate');
    Route::put('PDG/private-drink-stockouts/reject/{stockout_no}','Backend\PrivateDrinkStockoutController@reject')->name('admin.private-drink-stockouts.reject');
    Route::put('PDG/private-drink-stockouts/reset/{stockout_no}','Backend\PrivateDrinkStockoutController@reset')->name('admin.private-drink-stockouts.reset');
    Route::put('PDG/private-drink-stockouts/confirm/{stockout_no}','Backend\PrivateDrinkStockoutController@confirm')->name('admin.private-drink-stockouts.confirm');
    Route::put('PDG/private-drink-stockouts/approuve/{stockout_no}','Backend\PrivateDrinkStockoutController@approuve')->name('admin.private-drink-stockouts.approuve');

    Route::get('PDG/private-drink-stockouts/export-to-excel','Backend\PrivateDrinkStockoutController@exportToExcel')->name('admin.private-drink-stockouts.export-to-excel');

    //food stockouts routes
    Route::get('EBMS/food-stockouts/index', 'Backend\FoodStockoutController@index')->name('admin.food-stockouts.index');
    Route::get('EBMS/food-stockouts/create', 'Backend\FoodStockoutController@create')->name('admin.food-stockouts.create');
    Route::post('EBMS/food-stockouts/store', 'Backend\FoodStockoutController@store')->name('admin.food-stockouts.store');
    Route::get('EBMS/food-stockouts/edit/{stockout_no}', 'Backend\FoodStockoutController@edit')->name('admin.food-stockouts.edit');
    Route::put('EBMS/food-stockouts/update/{stockout_no}', 'Backend\FoodStockoutController@update')->name('admin.food-stockouts.update');
    Route::delete('EBMS/food-stockouts/destroy/{stockout_no}', 'Backend\FoodStockoutController@destroy')->name('admin.food-stockouts.destroy');
    Route::get('EBMS/food-stockouts/show/{stockout_no}','Backend\FoodStockoutController@show')->name('admin.food-stockouts.show');

    Route::get('EBMS/food-stockouts/bonSortie/{stockout_no}','Backend\FoodStockoutController@bonSortie')->name('admin.food-stockouts.bonSortie');
    Route::put('EBMS/food-stockouts/validate/{stockout_no}', 'Backend\FoodStockoutController@validateStockout')->name('admin.food-stockouts.validate');
    Route::put('EBMS/food-stockouts/reject/{stockout_no}','Backend\FoodStockoutController@reject')->name('admin.food-stockouts.reject');
    Route::put('EBMS/food-stockouts/reset/{stockout_no}','Backend\FoodStockoutController@reset')->name('admin.food-stockouts.reset');
    Route::put('EBMS/food-stockouts/confirm/{stockout_no}','Backend\FoodStockoutController@confirm')->name('admin.food-stockouts.confirm');
    Route::put('EBMS/food-stockouts/approuve/{stockout_no}','Backend\FoodStockoutController@approuve')->name('admin.food-stockouts.approuve');

    Route::get('EBMS/food-stockouts/export-to-pdf', 'Backend\FoodStockoutController@exportTopdf')->name('admin.food-stockouts.export-to-pdf');

    //drink extra big store report routes
    Route::get('EBMS/drink-extra-big-store-report/index','Backend\DrinkExtraBigReportController@index')->name('admin.drink-extra-big-store-report.index');
    Route::get('EBMS/drink-extra-big-store-report/export-to-pdf','Backend\DrinkExtraBigReportController@exportToPdf')->name('admin.drink-extra-big-store-report.export-to-pdf');
    Route::get('EBMS/drink-extra-big-store-report/export-to-excel','Backend\DrinkExtraBigReportController@exportToExcel')->name('admin.drink-extra-big-store-report.export-to-excel');

    //drink big store report routes
    Route::get('EBMS/drink-big-store-report/index','Backend\DrinkBigReportController@index')->name('admin.drink-big-store-report.index');
    Route::get('EBMS/drink-big-store-report/export-to-pdf','Backend\DrinkBigReportController@exportToPdf')->name('admin.drink-big-store-report.export-to-pdf');
    Route::get('EBMS/drink-big-store-report/export-to-excel','Backend\DrinkBigReportController@exportToExcel')->name('admin.drink-big-store-report.export-to-excel');

    //drink small store report routes
    Route::get('EBMS/drink-small-store-report/index','Backend\DrinkSmallReportController@index')->name('admin.drink-small-store-report.index');
    Route::get('EBMS/drink-small-store-report/export-to-pdf','Backend\DrinkSmallReportController@exportToPdf')->name('admin.drink-small-store-report.export-to-pdf');
    Route::get('EBMS/drink-small-store-report/export-to-excel','Backend\DrinkSmallReportController@exportToExcel')->name('admin.drink-small-store-report.export-to-excel');

    //food extra big store report routes
    Route::get('EBMS/food-extra-big-store-report/index','Backend\FoodExtraBigReportController@index')->name('admin.food-extra-big-store-report.index');
    Route::get('EBMS/food-extra-big-store-report/export-to-pdf','Backend\FoodExtraBigReportController@exportToPdf')->name('admin.food-extra-big-store-report.export-to-pdf');
    Route::get('EBMS/food-extra-big-store-report/export-to-excel','Backend\FoodExtraBigReportController@exportToExcel')->name('admin.food-extra-big-store-report.export-to-excel');
    //food big store report routes
    Route::get('EBMS/food-big-store-report/index','Backend\FoodBigReportController@index')->name('admin.food-big-store-report.index');
    Route::get('EBMS/food-big-store-report/export-to-pdf','Backend\FoodBigReportController@exportToPdf')->name('admin.food-big-store-report.export-to-pdf');
    Route::get('EBMS/food-big-store-report/export-to-excel','Backend\FoodBigReportController@exportToExcel')->name('admin.food-big-store-report.export-to-excel');

    //food small store report routes
    Route::get('EBMS/food-small-store-report/index','Backend\FoodSmallReportController@index')->name('admin.food-small-store-report.index');
    Route::get('EBMS/food-small-store-report/export-to-pdf','Backend\FoodSmallReportController@exportToPdf')->name('admin.food-small-store-report.export-to-pdf');
    Route::get('EBMS/food-small-store-report/export-to-excel','Backend\FoodSmallReportController@exportToExcel')->name('admin.food-small-store-report.export-to-excel');


    //material extra big store report routes
    Route::get('EBMS/material-extra-big-store-report/index','Backend\MaterialExtraBigReportController@index')->name('admin.material-extra-big-store-report.index');
    Route::get('EBMS/material-extra-big-store-report/export-to-pdf','Backend\MaterialExtraBigReportController@exportToPdf')->name('admin.material-extra-big-store-report.export-to-pdf');
    Route::get('EBMS/material-extra-big-store-report/export-to-excel','Backend\MaterialExtraBigReportController@exportToExcel')->name('admin.material-extra-big-store-report.export-to-excel');
    //material big store report routes
    Route::get('EBMS/material-big-store-report/index','Backend\MaterialBigReportController@index')->name('admin.material-big-store-report.index');
    Route::get('EBMS/material-big-store-report/export-to-pdf','Backend\MaterialBigReportController@exportToPdf')->name('admin.material-big-store-report.export-to-pdf');
    Route::get('EBMS/material-big-store-report/export-to-excel','Backend\MaterialBigReportController@exportToExcel')->name('admin.material-big-store-report.export-to-excel');

    //material small store report routes
    Route::get('EBMS/material-small-store-report/index','Backend\MaterialSmallReportController@index')->name('admin.material-small-store-report.index');
    Route::get('EBMS/material-small-store-report/export-to-pdf','Backend\MaterialSmallReportController@exportToPdf')->name('admin.material-small-store-report.export-to-pdf');
    Route::get('EBMS/material-small-store-report/export-to-excel','Backend\MaterialSmallReportController@exportToExcel')->name('admin.material-small-store-report.export-to-excel');

    //barrist drink big report routes
    Route::get('EBMS/barrist-drink-big-report/index','Backend\BarristBigReportController@indexDrink')->name('admin.barrist-drink-big-report.index');
    Route::get('EBMS/barrist-drink-big-report/export-to-pdf','Backend\BarristBigReportController@exportToPdfDrink')->name('admin.barrist-drink-big-report.export-to-pdf');
    Route::get('EBMS/barrist-drink-big-report/export-to-excel','Backend\BarristBigReportController@exportToExcelDrink')->name('admin.barrist-drink-big-report.export-to-excel');

    //barrist food big report routes
    Route::get('EBMS/barrist-food-big-report/index','Backend\BarristBigReportController@indexFood')->name('admin.barrist-food-big-report.index');
    Route::get('EBMS/barrist-food-big-report/export-to-pdf','Backend\BarristBigReportController@exportToPdfFood')->name('admin.barrist-food-big-report.export-to-pdf');
    Route::get('EBMS/barrist-food-big-report/export-to-excel','Backend\BarristBigReportController@exportToExcelFood')->name('admin.barrist-food-big-report.export-to-excel');

    //barrist small store report routes
    Route::get('EBMS/barrist-small-store-report/index','Backend\BarristSmallReportController@index')->name('admin.barrist-small-store-report.index');
    Route::get('EBMS/barrist-small-store-report/export-to-pdf','Backend\BarristSmallReportController@exportToPdf')->name('admin.barrist-small-store-report.export-to-pdf');
    Route::get('EBMS/barrist-small-store-report/export-to-excel','Backend\BarristSmallReportController@exportToExcel')->name('admin.barrist-small-store-report.export-to-excel');

    //invoice report routes
    Route::get('EBMS/invoice-report/index','Backend\FactureRestaurantController@report')->name('admin.invoice-report.report');
    Route::get('EBMS/invoice-report-one/export-to-pdf','Backend\FactureRestaurantController@exportToPdfReportOne')->name('admin.invoice-report-one.export-to-pdf');
    Route::get('EBMS/invoice-report-two/export-to-pdf','Backend\FactureRestaurantController@exportToPdfReportTwo')->name('admin.invoice-report-two.export-to-pdf');
    Route::get('EBMS/invoice-report/export-to-excel','Backend\FactureRestaurantController@exportToExcel')->name('admin.invoice-report.export-to-excel');

    Route::get('EBMS/order-server-report/index','Backend\FactureRestaurantController@reportServer')->name('admin.order_client_by_employe.export-to-pdf');


    //settings routes
    Route::get('EBMS/settings/index', 'Backend\SettingController@index')->name('admin.settings.index');
    Route::get('EBMS/settings/create', 'Backend\SettingController@create')->name('admin.settings.create');
    Route::post('EBMS/settings/store', 'Backend\SettingController@store')->name('admin.settings.store');
    Route::get('EBMS/settings/edit/{id}', 'Backend\SettingController@edit')->name('admin.settings.edit');
    Route::put('EBMS/settings/update/{id}', 'Backend\SettingController@update')->name('admin.settings.update');
    Route::delete('EBMS/settings/destroy/{id}', 'Backend\SettingController@destroy')->name('admin.settings.destroy');

    Route::get('EBMS/supplier/export','Backend\SupplierController@get_supplier_data')->name('admin.supplier.export');

    //hr routes
    //hr-departements routes
    Route::get('hr-departements/index', 'Backend\Hr\DepartementController@index')->name('admin.hr-departements.index');
    Route::get('hr-departements/create', 'Backend\Hr\DepartementController@create')->name('admin.hr-departements.create');
    Route::post('hr-departements/store', 'Backend\Hr\DepartementController@store')->name('admin.hr-departements.store');
    Route::get('hr-departements/edit/{id}', 'Backend\Hr\DepartementController@edit')->name('admin.hr-departements.edit');
    Route::put('hr-departements/update/{id}', 'Backend\Hr\DepartementController@update')->name('admin.hr-departements.update');
    Route::delete('hr-departements/destroy/{id}', 'Backend\Hr\DepartementController@destroy')->name('admin.hr-departements.destroy');

    //hr-services routes
    Route::get('hr-services/index', 'Backend\Hr\ServiceController@index')->name('admin.hr-services.index');
    Route::get('hr-services/create', 'Backend\Hr\ServiceController@create')->name('admin.hr-services.create');
    Route::post('hr-services/store', 'Backend\Hr\ServiceController@store')->name('admin.hr-services.store');
    Route::get('hr-services/edit/{id}', 'Backend\Hr\ServiceController@edit')->name('admin.hr-services.edit');
    Route::put('hr-services/update/{id}', 'Backend\Hr\ServiceController@update')->name('admin.hr-services.update');
    Route::delete('hr-services/destroy/{id}', 'Backend\Hr\ServiceController@destroy')->name('admin.hr-services.destroy');

    //hr-fonctions routes
    Route::get('hr-fonctions/index', 'Backend\Hr\FonctionController@index')->name('admin.hr-fonctions.index');
    Route::get('hr-fonctions/create', 'Backend\Hr\FonctionController@create')->name('admin.hr-fonctions.create');
    Route::post('hr-fonctions/store', 'Backend\Hr\FonctionController@store')->name('admin.hr-fonctions.store');
    Route::get('hr-fonctions/edit/{id}', 'Backend\Hr\FonctionController@edit')->name('admin.hr-fonctions.edit');
    Route::put('hr-fonctions/update/{id}', 'Backend\Hr\FonctionController@update')->name('admin.hr-fonctions.update');
    Route::delete('hr-fonctions/destroy/{id}', 'Backend\Hr\FonctionController@destroy')->name('admin.hr-fonctions.destroy');

    //hr-banques routes
    Route::get('hr-banques/index', 'Backend\Hr\BanqueController@index')->name('admin.hr-banques.index');
    Route::get('hr-banques/create', 'Backend\Hr\BanqueController@create')->name('admin.hr-banques.create');
    Route::post('hr-banques/store', 'Backend\Hr\BanqueController@store')->name('admin.hr-banques.store');
    Route::get('hr-banques/edit/{id}', 'Backend\Hr\BanqueController@edit')->name('admin.hr-banques.edit');
    Route::put('hr-banques/update/{id}', 'Backend\Hr\BanqueController@update')->name('admin.hr-banques.update');
    Route::delete('hr-banques/destroy/{id}', 'Backend\Hr\BanqueController@destroy')->name('admin.hr-banques.destroy');

    //hr-grades routes
    Route::get('hr-grades/index', 'Backend\Hr\GradeController@index')->name('admin.hr-grades.index');
    Route::get('hr-grades/create', 'Backend\Hr\GradeController@create')->name('admin.hr-grades.create');
    Route::post('hr-grades/store', 'Backend\Hr\GradeController@store')->name('admin.hr-grades.store');
    Route::get('hr-grades/edit/{id}', 'Backend\Hr\GradeController@edit')->name('admin.hr-grades.edit');
    Route::put('hr-grades/update/{id}', 'Backend\Hr\GradeController@update')->name('admin.hr-grades.update');
    Route::delete('hr-grades/destroy/{id}', 'Backend\Hr\GradeController@destroy')->name('admin.hr-grades.destroy');

    //hr-employes routes
    Route::get('hr-employes/index/{company_id}', 'Backend\Hr\EmployeController@index')->name('admin.hr-employes.index');
    Route::get('hr-employes/create', 'Backend\Hr\EmployeController@create')->name('admin.hr-employes.create');
    Route::get('hr-employes/show/{id}', 'Backend\Hr\EmployeController@show')->name('admin.hr-employes.show');
    Route::post('hr-employes/store', 'Backend\Hr\EmployeController@store')->name('admin.hr-employes.store');
    Route::get('hr-employes/edit/{id}', 'Backend\Hr\EmployeController@edit')->name('admin.hr-employes.edit');
    Route::put('hr-employes/update/{id}', 'Backend\Hr\EmployeController@update')->name('admin.hr-employes.update');
    Route::delete('hr-employes/destroy/{id}', 'Backend\Hr\EmployeController@destroy')->name('admin.hr-employes.destroy');

    Route::get('hr-employes/exportToExcel/{company_id}', 'Backend\Hr\EmployeController@exportToExcel')->name('admin.hr-employes.exportToExcel');

    //hr-stagiaires routes
    Route::get('hr-stagiaires/index/{company_id}', 'Backend\Hr\StagiaireController@index')->name('admin.hr-stagiaires.index');
    Route::get('hr-stagiaire/select-by-company', 'Backend\Hr\StagiaireController@selectByCompany')->name('admin.stagiare-select-by-company');
    Route::get('hr-stagiaires/create', 'Backend\Hr\StagiaireController@create')->name('admin.hr-stagiaires.create');
    Route::get('hr-stagiaires/show/{id}', 'Backend\Hr\StagiaireController@show')->name('admin.hr-stagiaires.show');
    Route::post('hr-stagiaires/store', 'Backend\Hr\StagiaireController@store')->name('admin.hr-stagiaires.store');
    Route::get('hr-stagiaires/edit/{id}', 'Backend\Hr\StagiaireController@edit')->name('admin.hr-stagiaires.edit');
    Route::put('hr-stagiaires/update/{id}', 'Backend\Hr\StagiaireController@update')->name('admin.hr-stagiaires.update');
    Route::delete('hr-stagiaires/destroy/{id}', 'Backend\Hr\StagiaireController@destroy')->name('admin.hr-stagiaires.destroy');

    //hr-jours-feries routes
    Route::get('hr-jours-feries/index', 'Backend\Hr\JourFerieController@index')->name('admin.hr-jours-feries.index');
    Route::get('hr-jours-feries/create', 'Backend\Hr\JourFerieController@create')->name('admin.hr-jours-feries.create');
    Route::post('hr-jours-feries/store', 'Backend\Hr\JourFerieController@store')->name('admin.hr-jours-feries.store');
    Route::get('hr-jours-feries/edit/{id}', 'Backend\Hr\JourFerieController@edit')->name('admin.hr-jours-feries.edit');
    Route::put('hr-jours-feries/update/{id}', 'Backend\Hr\JourFerieController@update')->name('admin.hr-jours-feries.update');
    Route::delete('hr-jours-feries/destroy/{id}', 'Backend\Hr\JourFerieController@destroy')->name('admin.hr-jours-feries.destroy');

    //hr-jours-travails routes
    Route::get('hr-jours-travails/index', 'Backend\Hr\JourTravailController@index')->name('admin.hr-jours-travails.index');
    Route::get('hr-jours-travails/create', 'Backend\Hr\JourTravailController@create')->name('admin.hr-jours-travails.create');
    Route::post('hr-jours-travails/store', 'Backend\Hr\JourTravailController@store')->name('admin.hr-jours-travails.store');
    Route::get('hr-jours-travails/edit/{id}', 'Backend\Hr\JourTravailController@edit')->name('admin.hr-jours-travails.edit');
    Route::put('hr-jours-travails/update/{id}', 'Backend\Hr\JourTravailController@update')->name('admin.hr-jours-travails.update');
    Route::delete('hr-jours-travails/destroy/{id}', 'Backend\Hr\JourTravailController@destroy')->name('admin.hr-jours-travails.destroy');

    //hr-ecoles routes
    Route::get('hr-ecoles/index', 'Backend\Hr\EcoleController@index')->name('admin.hr-ecoles.index');
    Route::get('hr-ecoles/create', 'Backend\Hr\EcoleController@create')->name('admin.hr-ecoles.create');
    Route::post('hr-ecoles/store', 'Backend\Hr\EcoleController@store')->name('admin.hr-ecoles.store');
    Route::get('hr-ecoles/edit/{id}', 'Backend\Hr\EcoleController@edit')->name('admin.hr-ecoles.edit');
    Route::put('hr-ecoles/update/{id}', 'Backend\Hr\EcoleController@update')->name('admin.hr-ecoles.update');
    Route::delete('hr-ecoles/destroy/{id}', 'Backend\Hr\EcoleController@destroy')->name('admin.hr-ecoles.destroy');

    //hr-filieres routes
    Route::get('hr-filieres/index', 'Backend\Hr\FiliereController@index')->name('admin.hr-filieres.index');
    Route::get('hr-filieres/create', 'Backend\Hr\FiliereController@create')->name('admin.hr-filieres.create');
    Route::post('hr-filieres/store', 'Backend\Hr\FiliereController@store')->name('admin.hr-filieres.store');
    Route::get('hr-filieres/edit/{id}', 'Backend\Hr\FiliereController@edit')->name('admin.hr-filieres.edit');
    Route::put('hr-filieres/update/{id}', 'Backend\Hr\FiliereController@update')->name('admin.hr-filieres.update');
    Route::delete('hr-filieres/destroy/{id}', 'Backend\Hr\FiliereController@destroy')->name('admin.hr-filieres.destroy');

    //hr-type-absences routes
    Route::get('hr-type-absences/index', 'Backend\Hr\TypeAbsenceController@index')->name('admin.hr-type-absences.index');
    Route::get('hr-type-absences/create', 'Backend\Hr\TypeAbsenceController@create')->name('admin.hr-type-absences.create');
    Route::post('hr-type-absences/store', 'Backend\Hr\TypeAbsenceController@store')->name('admin.hr-type-absences.store');
    Route::get('hr-type-absences/edit/{id}', 'Backend\Hr\TypeAbsenceController@edit')->name('admin.hr-type-absences.edit');
    Route::put('hr-type-absences/update/{id}', 'Backend\Hr\TypeAbsenceController@update')->name('admin.hr-type-absences.update');
    Route::delete('hr-type-absences/destroy/{id}', 'Backend\Hr\TypeAbsenceController@destroy')->name('admin.hr-type-absences.destroy');


    //hr-conge-payes routes
    Route::get('hr-conge-payes/index', 'Backend\Hr\CongePayeController@index')->name('admin.hr-conge-payes.index');
    Route::get('hr-conge-payes/create', 'Backend\Hr\CongePayeController@create')->name('admin.hr-conge-payes.create');
    Route::post('hr-conge-payes/store', 'Backend\Hr\CongePayeController@store')->name('admin.hr-conge-payes.store');
    Route::get('hr-conge-payes/edit/{id}', 'Backend\Hr\CongePayeController@edit')->name('admin.hr-conge-payes.edit');
    Route::put('hr-conge-payes/update/{id}', 'Backend\Hr\CongePayeController@update')->name('admin.hr-conge-payes.update');
    Route::delete('hr-conge-payes/destroy/{id}', 'Backend\Hr\CongePayeController@destroy')->name('admin.hr-conge-payes.destroy');

    //hr-type-conges routes
    Route::get('hr-type-conges/index', 'Backend\Hr\TypeCongeController@index')->name('admin.hr-type-conges.index');
    Route::get('hr-type-conges/create', 'Backend\Hr\TypeCongeController@create')->name('admin.hr-type-conges.create');
    Route::post('hr-type-conges/store', 'Backend\Hr\TypeCongeController@store')->name('admin.hr-type-conges.store');
    Route::get('hr-type-conges/edit/{id}', 'Backend\Hr\TypeCongeController@edit')->name('admin.hr-type-conges.edit');
    Route::put('hr-type-conges/update/{id}', 'Backend\Hr\TypeCongeController@update')->name('admin.hr-type-conges.update');
    Route::delete('hr-type-conges/destroy/{id}', 'Backend\Hr\TypeCongeController@destroy')->name('admin.hr-type-conges.destroy');

    //hr-take-conges routes
    Route::get('hr-take-conges/index/{company_id}', 'Backend\Hr\TakeCongeController@index')->name('admin.hr-take-conges.index');
    Route::get('hr-leave-taken/select-by-company', 'Backend\Hr\TakeCongeController@selectByCompany')->name('admin.hr-leave-taken.select-by-company');
    Route::get('hr-take-conges/create/{company_id}', 'Backend\Hr\TakeCongeController@create')->name('admin.hr-take-conges.create');
    Route::post('hr-take-conges/store', 'Backend\Hr\TakeCongeController@store')->name('admin.hr-take-conges.store');
    Route::get('hr-take-conges/edit/{id}', 'Backend\Hr\TakeCongeController@edit')->name('admin.hr-take-conges.edit');
    Route::put('hr-take-conges/update/{id}', 'Backend\Hr\TakeCongeController@update')->name('admin.hr-take-conges.update');
    Route::delete('hr-take-conges/destroy/{id}', 'Backend\Hr\TakeCongeController@destroy')->name('admin.hr-take-conges.destroy');

    Route::get('hr-take-conges/billet-sortie/{id}', 'Backend\Hr\TakeCongeController@billetSortie')->name('admin.hr-take-conges.billetSortie');

    //hr-take-conge-payes routes
    Route::get('hr-take-conge-payes/index/{company_id}', 'Backend\Hr\TakeCongePayeController@index')->name('admin.hr-take-conge-payes.index');
    Route::get('hr-take-paid-leave/select-by-company', 'Backend\Hr\TakeCongePayeController@selectByCompany')->name('admin.hr-take-paid-leave.select-by-company');
    Route::get('hr-take-conge-payes/create/{company_id}', 'Backend\Hr\TakeCongePayeController@create')->name('admin.hr-take-conge-payes.create');
    Route::post('hr-take-conge-payes/store', 'Backend\Hr\TakeCongePayeController@store')->name('admin.hr-take-conge-payes.store');
    Route::get('hr-take-conge-payes/edit/{id}', 'Backend\Hr\TakeCongePayeController@edit')->name('admin.hr-take-conge-payes.edit');
    Route::put('hr-take-conge-payes/update/{id}', 'Backend\Hr\TakeCongePayeController@update')->name('admin.hr-take-conge-payes.update');
    Route::delete('hr-take-conge-payes/destroy/{id}', 'Backend\Hr\TakeCongePayeController@destroy')->name('admin.hr-take-conge-payes.destroy');

    Route::post('hr-take-conge-payes/fetch', 'Backend\Hr\TakeCongePayeController@fetch')->name('admin.hr-take-conge-payes.fetch');

    Route::get('hr-take-conge-payes/lettre-Demande-Conge/{id}', 'Backend\Hr\TakeCongePayeController@lettreDemandeConge')->name('admin.hr-take-conge-payes.lettreDemandeConge');

    Route::put('hr-take-conge-paye/valider/{id}', 'Backend\Hr\TakeCongePayeController@validerConge')->name('admin.hr-take-conge-paye.valider');
    Route::put('hr-take-conge-paye/confirmer/{id}', 'Backend\Hr\TakeCongePayeController@confirmerConge')->name('admin.hr-take-conge-paye.confirmer');
    Route::put('hr-take-conge-paye/approuver/{id}', 'Backend\Hr\TakeCongePayeController@approuverConge')->name('admin.hr-take-conge-paye.approuver');
    Route::put('hr-take-conge-paye/rejeter/{id}', 'Backend\Hr\TakeCongePayeController@rejeterConge')->name('admin.hr-take-conge-paye.rejeter');
    Route::put('hr-take-conge-paye/annuler/{id}', 'Backend\Hr\TakeCongePayeController@annulerConge')->name('admin.hr-take-conge-paye.annuler');

    //hr-loans routes
    Route::get('hr-loans/index', 'Backend\Hr\LoanController@index')->name('admin.hr-loans.index');
    Route::get('hr-loans/create', 'Backend\Hr\LoanController@create')->name('admin.hr-loans.create');
    Route::post('hr-loans/store', 'Backend\Hr\LoanController@store')->name('admin.hr-loans.store');
    Route::get('hr-loans/edit/{id}', 'Backend\Hr\LoanController@edit')->name('admin.hr-loans.edit');
    Route::put('hr-loans/update/{id}', 'Backend\Hr\LoanController@update')->name('admin.hr-loans.update');
    Route::delete('hr-loans/destroy/{id}', 'Backend\Hr\LoanController@destroy')->name('admin.hr-loans.destroy');

    //hr-cotations routes
    Route::get('hr-cotations/index', 'Backend\Hr\CotationController@index')->name('admin.hr-cotations.index');
    Route::get('hr-cotations/create', 'Backend\Hr\CotationController@create')->name('admin.hr-cotations.create');
    Route::post('hr-cotations/store', 'Backend\Hr\CotationController@store')->name('admin.hr-cotations.store');
    Route::get('hr-cotations/edit/{id}', 'Backend\Hr\CotationController@edit')->name('admin.hr-cotations.edit');
    Route::put('hr-cotations/update/{id}', 'Backend\Hr\CotationController@update')->name('admin.hr-cotations.update');
    Route::delete('hr-cotations/destroy/{id}', 'Backend\Hr\CotationController@destroy')->name('admin.hr-cotations.destroy');

    //hr-paiements routes
    Route::get('hr-paiement/create-by-company', 'Backend\Hr\PaiementController@createByCompany')->name('admin.hr-paiement.createByCompany');
    Route::get('hr-paiement/select-by-company', 'Backend\Hr\PaiementController@selectByCompany')->name('admin.hr-paiement.selectByCompany');
    Route::get('hr-paiements/index/{company_id}', 'Backend\Hr\PaiementController@index')->name('admin.hr-paiements.index');
    Route::get('hr-paiements/create/{company_id}', 'Backend\Hr\PaiementController@create')->name('admin.hr-paiements.create');
    Route::post('hr-paiements/store', 'Backend\Hr\PaiementController@store')->name('admin.hr-paiements.store');
    Route::get('hr-paiements/edit/{id}/by-company/{company_id}', 'Backend\Hr\PaiementController@edit')->name('admin.hr-paiements.edit');
    Route::get('hr-paiements/show/{code}', 'Backend\Hr\PaiementController@show')->name('admin.hr-paiements.show');
    Route::put('hr-paiements/update/{id}', 'Backend\Hr\PaiementController@update')->name('admin.hr-paiements.update');
    Route::delete('hr-paiements/destroy/{id}', 'Backend\Hr\PaiementController@destroy')->name('admin.hr-paiements.destroy');

    Route::get('hr-fiche-paie/print/{code}', 'Backend\Hr\PaiementController@ficheSalaire')->name('admin.hr-fiche-paie.print');

    Route::post('hr-paiements/fetch', 'Backend\Hr\PaiementController@fetch')->name('admin.hr-paiements.fetch');

    //hr-note-interne routes
    Route::get('hr-note-interne/index', 'Backend\Hr\NoteInterneController@index')->name('admin.hr-note-interne.index');
    Route::get('hr-note-interne/create', 'Backend\Hr\NoteInterneController@create')->name('admin.hr-note-interne.create');
    Route::post('hr-note-interne/store', 'Backend\Hr\NoteInterneController@store')->name('admin.hr-note-interne.store');
    Route::get('hr-note-interne/edit/{id}', 'Backend\Hr\NoteInterneController@edit')->name('admin.hr-note-interne.edit');
    Route::put('hr-note-interne/update/{id}', 'Backend\Hr\NoteInterneController@update')->name('admin.hr-note-interne.update');
    Route::delete('hr-note-interne/destroy/{id}', 'Backend\Hr\NoteInterneController@destroy')->name('admin.hr-note-interne.destroy');

    //hr-reglages routes
    Route::get('hr-reglages/index', 'Backend\Hr\ReglageController@index')->name('admin.hr-reglages.index');
    Route::get('hr-reglages/create', 'Backend\Hr\ReglageController@create')->name('admin.hr-reglages.create');
    Route::post('hr-reglages/store', 'Backend\Hr\ReglageController@store')->name('admin.hr-reglages.store');
    Route::get('hr-reglages/edit/{id}', 'Backend\Hr\ReglageController@edit')->name('admin.hr-reglages.edit');
    Route::put('hr-reglages/update/{id}', 'Backend\Hr\ReglageController@update')->name('admin.hr-reglages.update');
    Route::delete('hr-reglages/destroy/{id}', 'Backend\Hr\ReglageController@destroy')->name('admin.hr-reglages.destroy');

    //hr-companies routes
    Route::get('hr-company/select', 'Backend\Hr\CompanyController@selectCompany')->name('admin.hr-company.select');
    Route::get('hr-companies/index', 'Backend\Hr\CompanyController@index')->name('admin.hr-companies.index');
    Route::get('hr-companies/create', 'Backend\Hr\CompanyController@create')->name('admin.hr-companies.create');
    Route::post('hr-companies/store', 'Backend\Hr\CompanyController@store')->name('admin.hr-companies.store');
    Route::get('hr-companies/edit/{id}', 'Backend\Hr\CompanyController@edit')->name('admin.hr-companies.edit');
    Route::put('hr-companies/update/{id}', 'Backend\Hr\CompanyController@update')->name('admin.hr-companies.update');
    Route::delete('hr-companies/destroy/{id}', 'Backend\Hr\CompanyController@destroy')->name('admin.hr-companies.destroy');

    //hr-journal-paies routes
    Route::get('hr-journal-paie/select-by-company/{code}', 'Backend\Hr\JournalPaieController@selectByCompany')->name('admin.hr-journal-paie.select-by-company');
    Route::get('hr-journal-paies/index', 'Backend\Hr\JournalPaieController@index')->name('admin.hr-journal-paies.index');
    Route::get('hr-journal-paies/export-to-excel', 'Backend\Hr\JournalPaieController@exportToExcel')->name('admin.hr-journal-paies.export-to-excel');
    Route::get('hr-journal-paies/create', 'Backend\Hr\JournalPaieController@create')->name('admin.hr-journal-paies.create');
    Route::post('hr-journal-paies/store', 'Backend\Hr\JournalPaieController@store')->name('admin.hr-journal-paies.store');
    Route::get('hr-journal-paies/edit/{id}', 'Backend\Hr\JournalPaieController@edit')->name('admin.hr-journal-paies.edit');
    Route::get('hr-journal-paies/show/{code}/{company_id}', 'Backend\Hr\JournalPaieController@show')->name('admin.hr-journal-paies.show');
    Route::put('hr-journal-paies/cloturer/{id}', 'Backend\Hr\JournalPaieController@cloturer')->name('admin.hr-journal-paies.cloturer');
    Route::put('hr-journal-paies/update/{id}', 'Backend\Hr\JournalPaieController@update')->name('admin.hr-journal-paies.update');
    Route::delete('hr-journal-paies/destroy/{id}', 'Backend\Hr\JournalPaieController@destroy')->name('admin.hr-journal-paies.destroy');

    //hr-journal-cotisations routes
    Route::get('hr-journal-cotisations/index/{company_id}', 'Backend\Hr\JournalCotisationController@index')->name('admin.hr-journal-cotisations.index');
    Route::get('hr-journal-cotisations/select-by-company', 'Backend\Hr\JournalCotisationController@selectByCompany')->name('admin.hr-journal-cotisations.select-by-company');
    Route::get('hr-journal-cotisations/export-to-excel', 'Backend\Hr\JournalCotisationController@exportToExcel')->name('admin.hr-journal-cotisations.export-to-excel');

    //hr-journal-impots routes
    Route::get('hr-journal-impots/index/{company_id}', 'Backend\Hr\JournalImpotController@index')->name('admin.hr-journal-impots.index');
    Route::get('hr-journal-impots/select-by-company', 'Backend\Hr\JournalImpotController@selectByCompany')->name('admin.hr-journal-impots.select-by-company');
    Route::get('hr-journal-impots/export-to-excel', 'Backend\Hr\JournalImpotController@exportToExcel')->name('admin.hr-journal-impots.export-to-excel');

    //hr-journal-conges routes
    Route::get('hr-journal-conges/index', 'Backend\Hr\JournalCongeController@index')->name('admin.hr-journal-conges.index');
    Route::get('hr-journal-conges/create', 'Backend\Hr\JournalCongeController@create')->name('admin.hr-journal-conges.create');
    Route::post('hr-journal-conges/store', 'Backend\Hr\JournalCongeController@store')->name('admin.hr-journal-conges.store');
    Route::get('hr-journal-conges/edit/{id}', 'Backend\Hr\JournalCongeController@edit')->name('admin.hr-journal-conges.edit');
    Route::put('hr-journal-conges/update/{id}', 'Backend\Hr\JournalCongeController@update')->name('admin.hr-journal-conges.update');
    Route::delete('hr-journal-conges/destroy/{id}', 'Backend\Hr\JournalCongeController@destroy')->name('admin.hr-journal-conges.destroy');

    Route::get('hr-choose-report/index', 'Backend\Hr\ReportController@choose')->name('admin.hr-choose-report.choose');



    Route::get('/404/muradutunge/ivyomwasavye-ntibishoboye-kuboneka',function(){
        return view('errors.404');


    });
});