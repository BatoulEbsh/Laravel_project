<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @method static find($id)
 * @method static create(array $array)
 */
class Comment extends Model
{
    use HasFactory;
    protected $fillable = [
        'title',
        'user_id',
        'product_id',
    ];
    protected $hidden = [
        'user_id',
        'product_id',
        'created_at',
        'updated_at'
    ];
    public function user(){
        return $this->belongsTo(User::class,'user_id');
    }
    public function product(){
        return $this->belongsTo(Product::class,'product_id');
    }

}
