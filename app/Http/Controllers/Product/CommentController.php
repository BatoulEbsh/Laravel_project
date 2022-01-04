<?php

namespace App\Http\Controllers\product;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use App\Models\Product;
use App\Traits\GeneralTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class CommentController extends Controller
{
    use  GeneralTrait;
    public function add($id,Request $request){
        if (!Product::find($id)){
            return $this->returnError(404,'not found');
        }
        $comment=$request->all();
        $validator = Validator::make($comment, [
            'title' => 'required|string',
        ]);

        if ($validator->fails()) {
            return $this->returnError(401, $validator->errors());
        }
        Comment::create([
            'title'=>$comment['title'],
            'user_id'=>Auth::id(),
            'product_id'=>$id
        ]);
        return $this->returnSuccessMessage('comment added successfully');

    }
    public function showComment($id){
        if (!Product::find($id)){
            return $this->returnError(404,'not found');
        }
       $comments = Comment::with('user')
           ->where('product_id',$id)->get();
        return $this->returnData('comments',$comments);
    }
    public function deleteComment($id){
        $comment = Comment::find($id);
        if ($comment['user_id'] != Auth::id())
            return $this->returnError(401, "not your own");
        $comment->delete();
        return $this->returnSuccessMessage("comment deleted successfully");
    }
}

