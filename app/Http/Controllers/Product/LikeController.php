<?php

namespace App\Http\Controllers\Product;

use App\Http\Controllers\Controller;
use App\Models\Like;
use App\Models\Product;
use App\Traits\GeneralTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use phpDocumentor\Reflection\Types\This;
use function Sodium\increment;

class LikeController extends Controller
{
    use  GeneralTrait;

    static public function isLiked($id)
    {

        if (Like::where('user_id', Auth::id())->where('product_id', $id)->exists()) {
            return true;
        }
        return false;
    }


    public function likeAble($id)
    {
        if (!Product::find($id)) {
            return $this->returnError(404, 'not found');
        }
        if (Like::where('user_id', Auth::id())->where('product_id', $id)->exists()) {
            return $this->returnSuccessMessage('product liked successfully');
        }
        $like = Like::where('user_id', Auth::id())
            ->where('product_id', $id)->first();
        if (is_null($like)) {
            Like::create([
                'product_id' => $id,
                'user_id' => Auth::id()
            ]);
        } else {
            $like->delete();
        }
    }


}
