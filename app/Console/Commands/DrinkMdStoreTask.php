<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class DrinkMdStoreTask extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'drinkmdstore:task';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'ETAT DU STOCK INTERMEDIAIRE DE BOISSONS';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        DB::insert("insert into virtual_drink_md_stores(date,code,manager,emplacement,store_signature,drink_id,quantity_bottle,unit,purchase_price,cump,selling_price) select updated_at,code,manager,emplacement,store_signature,drink_id,quantity_bottle,unit,purchase_price,cump,selling_price from drink_big_store_details where drink_id != '' ");
    }
}
