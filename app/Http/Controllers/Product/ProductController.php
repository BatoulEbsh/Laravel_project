<?php

namespace App\Http\Controllers\Product;

use App\Models\Product;
use App\Traits\GeneralTrait;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Validator;
use phpDocumentor\Reflection\Types\This;

class ProductController extends Controller
{
    use  GeneralTrait;

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return $this->returnData('products', Product::all());


    }

    function dateDiff($date1, $date2)
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
            'cat_Id' => 'required|string',
            'quantity' => 'required|integer',
            'price' => 'required',//TODO double
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
        $nameImage = time() . $this->returnCode(5) . $product['image']->getClientOriginalName();
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
        $product['user_id']=Auth::id();
        $product = Product::create($product);
        return $this->returnData("Product", $product);
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
        return $this->returnData('product', $product, 'success');
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
        $product=Product::find($id);
        if (!$product)
            return $this->returnError(401,"not found");
        if ($product['user_id']!=Auth::id())
            return $this->returnError(401,"not auth");
        $input = $request->all();
        $validator = Validator::make($input, [
            'name' => 'required',
            'image' => 'image',
            'contact' => 'required',
            'cat_Id' => 'required',
            'quantity' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->returnError(401, $validator->errors());
        }
        $product['name']=$input['name'];
        $product['contact']=$input['contact'];
        $product['cat_Id']=$input['cat_Id'];
        $product['quantity']=$input['quantity'];
        if ($request->has('image')){
            unlink(substr($product['image'],strlen(URL::to('/'))+1));
            $new = time() . $this->returnCode(5) . $input['image']->getClientOriginalName();
            $input['image']->move("images", $new);
            $product['image'] = URL::to('/images') . "/" . $new;
        }
        $product->save();
        return $this->returnData("Product", $input);
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
            return $this->returnError(401,"not found");
        if ($product['user_id']!=Auth::id())
            return $this->returnError(401,"asassaas");
        unlink(substr($product['image'],strlen(URL::to('/'))+1));
        $product->delete();
        return $this->returnSuccessMessage("product delete successfully");
    }
}
