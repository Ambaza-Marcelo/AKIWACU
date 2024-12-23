<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class FoodMdStoreTask extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'foodmdstore:task';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'ETAT DU STOCK INTERMEDIAIRE DE NOURRITURES';

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
        DB::insert("insert into virtual_food_md_stores(code,manager,emplacement,store_signature,food_id,quantity,unit,purchase_price,cump) select code,manager,emplacement,store_signature,food_id,quantity,unit,purchase_price,cump from food_big_store_details where food_id != '' ");
    }
}
