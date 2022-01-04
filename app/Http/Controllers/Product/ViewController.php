<?php

namespace App\Http\Controllers\Product;

use App\Http\Controllers\Controller;
use App\Models\View;
use App\Traits\GeneralTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ViewController extends Controller

{
    use GeneralTrait;
    public function view($id){
        if (Auth::id()==$id){
            return $this->returnError(401,"your own");
        }
       $view= View::where('user_id',Auth::id())->where('product_id',$id)->first();
        if (!$view){
            View::create([
                'user_id'=>Auth::id(),
                'product_id'=>$id
            ]);
            return $this->returnSuccessMessage("product viewed successfully");
        }
        return $this->returnError(401,"");
    }
}
