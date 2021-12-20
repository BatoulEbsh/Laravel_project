<?php

namespace App\Console\Commands;

use App\Models\Product;
use App\Traits\GeneralTrait;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
class ExpirationCheck extends Command
{
    use GeneralTrait;
    /**
     * The name and signature of thjjje console command.
     *
     * @var string
     */
    protected $signature = 'product:expirecheck';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Checks all products and deletes the expired ones from the DB';

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
        $products = Product::all();
       foreach ($products as $product){
           if($product->endDate <= now())
               $product->delete();
       }//hello
        return $this->returnData('success','success');
    }
}
