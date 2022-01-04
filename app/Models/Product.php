<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @method static latest()
 * @method static create(array $product)
 * @method static find(int $id)
 * @method static selecte(string $string, string $string1)
 * @method static findorfail($id)
 * @method static withCount(string $string)
 * @method static where(string $string, $name)
 * @method static date_create($get)
 */
class Product extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'image',
        'endDate',
        'classification',
        'contact',
        'cat_Id',
        'quantity',
        'price',
        'r1',
        'r2',
        'r3',
        'main_price',
        'dis1',
        'dis2',
        'dis3',
        'days',
        'user_id',
        'reads'
    ];
    protected $hidden = [
        'r1',
        'r2',
        'r3',
        'main_price',
        'dis1',
        'dis2',
        'dis3'
    ];
    public function user(){
        return $this->belongsTo(User::class,'user_id');
    }
    public function views(){
        return $this->hasMany(View::class,'product_id');
    }
    public function comments(){
        return $this->hasMany(Comment::class,'product_id');
    }
    public function likes(){
        return $this->hasMany(Like::class,'product_id');
    }
}
