<?php

namespace App\Http\Controllers\Product;

use App\Models\Like;
use App\Models\Product;
use App\Traits\GeneralTrait;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Validator;


class ProductController extends Controller
{
    use  GeneralTrait;

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
            $product = $this->sort($request);
            for ($i = 0; $i < count($product); $i++) {
                $product[$i]['isLike'] = LikeController::isLiked($product[$i]['id']);
            }
            return $this->returnData('products',$product);
        }
    public function dateDiff($date1, $date2)
    {
        $date = date_diff($date1, $date2);
        return $date->format('%R%a') * 1;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $product = $request->all();
        $validator = Validator::make($product, [
            'name' => 'required|string',
            'image' => 'required|image',
            'endDate' => 'required|date',
            'contact' => 'required|string',
            'category' => 'required|string',
            'quantity' => 'required|integer',
            'price' => 'required|regex:/^[0-9]+(\.[0-9][0-9]?)?$/',
            'r1' => 'required|integer',//range1
            'r2' => 'required|integer',//range2
            'r3' => 'required|integer',
            'dis1' => 'required|integer', //sale1
            'dis2' => 'required|integer',//sale2
            'dis3' => 'required|integer'//sale3
        ]);

        if ($validator->fails()) {
            return $this->returnError(401, $validator->errors());
        }
        $nameImage = time() .$product['image']->getClientOriginalName();
        $product['image']->move("images", $nameImage);
        $product['image'] = URL::to('/images') . "/" . $nameImage;
        $product['endDate'] = date_create(date('Y/m/d', strtotime($product['endDate'])));
        $product['days'] = $this->dateDiff(date_create(date('Y/m/d')), $product['endDate']);
        $product['main_price'] = $product['price'];
        $product['price'] = $this->price(
            $product['r1'],
            $product['r2'],
            $product['r3'],
            $product['dis1'],
            $product['dis2'],
            $product['dis3'],
            $product['days'],
            $product['main_price']);
        $product['user_id'] = Auth::id();
        $product = Product::create($product);
        return $this->returnSuccessMessage('product added successfully');
    }

    public function searchName(Request $request)
    {
        $search = $request->all();
        $val = Validator::make($search, [
            'name' => 'required|string'
        ]);
        if ($val->fails()) {
            return $this->returnError(401, $val->errors());
        }
        $searchName = Product::
            where('name', 'like', '%' . $request['name'] . '%')->withCount('likes')
            ->withCount('views')
            ->get();
        for ($i = 0; $i < count($searchName); $i++) {
            $searchName[$i]['isLike'] = LikeController::isLiked($searchName[$i]['id']);
        }
        return $searchName;
    }
    public function searchDate(Request $request)
    {
        $searchD = $request->all();
        $val = Validator::make($searchD, [
            'endDate' => 'required|date'
        ]);
        if ($val->fails()) {
            return $this->returnError(401, $val->errors());
        }
        $request['endDate'] = date_create(date('Y/m/d', strtotime($request['endDate'])));
       $searchDate=Product:: where('endDate', $request['endDate'])->get();
        return $searchDate;
    }
    public function searchCat(Request $request)
    {
        $searchC = $request->all();
        $val = Validator::make($searchC, [
            'category' => 'required|string'
        ]);
        if ($val->fails()) {
            return $this->returnError(401, $val->errors());
        }
        $searchCat=Product:: where('category', $request['category'])->get();
        return $searchCat;
    }
    public function sort(Request $request){
     $sort = $request->header('sort');
     if(is_null($sort)){

         return Product::withCount('views')->withCount('likes')->get();
     }
    return Product::withCount('views')->withCount('likes')->orderby($sort);
}

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return array
     */

    public function show($id)
    {
        $product = Product::find($id);
        if (is_null($product)) {
            return $this->returnError(404, 'notfound');
        }
        return $this->returnData('product', Product::withCount('views')->get());
    }


    /**
     *
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, int $id)
    {
        $product = Product::find($id);
        if (!$product)
            return $this->returnError(401, "not found");
        if ($product['user_id'] != Auth::id())
            return $this->returnError(401, "not auth");
        $input = $request->all();
        $validator = Validator::make($input, [
            'name' => 'required',
            'image' => 'image',
            'contact' => 'required',
            'category' => 'required',
            'quantity' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->returnError(401, $validator->errors());
        }
        $product['name'] = $input['name'];
        $product['contact'] = $input['contact'];
        $product['category'] = $input['category'];
        $product['quantity'] = $input['quantity'];
        if ($request->has('image')) {
            unlink(substr($product['image'], strlen(URL::to('/')) + 1));
            $new = time() . $input['image']->getClientOriginalName();
            $input['image']->move("images", $new);
            $product['image'] = URL::to('/images') . "/" . $new;
        }
        $product->save();
        return $this->returnSuccessMessage('product updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return array
     */
    public function destroy($id)
    {
        $product = Product::find($id);
        if (!$product)
            return $this->returnError(401, "not found");
        if ($product['user_id'] != Auth::id())
            return $this->returnError(401, "asassaas");
        unlink(substr($product['image'], strlen(URL::to('/')) + 1));
        $product->delete();
        return $this->returnSuccessMessage("product delete successfully");
    }
}
