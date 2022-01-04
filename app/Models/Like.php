<?php

namespace App\Models;

use App\Traits\GeneralTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @method static create(array $array)
 * @method static where(string $string, int|string|null $id)
 * @method static whereUserId(int|string|null $id)
 */
class Like extends Model
{
    use GeneralTrait;
    use HasFactory;
    protected $fillable = [
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
