<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Order extends Model
{
    protected $guard=[];
    public $timestamps = false;
    protected $table="orders";
    protected $appends=[
        'images',
    ];
    public function getTimestampAttribute()
    {
        // dd("ee");
        return  Carbon::parse($this->attributes['timestamp'])->format('j-F-Y');
    }
    public function getImagesAttribute()
    {
        return (Image::find($this->attributes['image_id'])) ;
    }
    public function user()
    {
        return $this->belongsTo(User::class, 'users_id', 'id');
    }
    /**
     * Get all of the comments for the Order
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function comments()
    {
        return $this->hasMany(Comment::class);
    }
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
    public function box_order()
    {
        return $this->belongsTo(BoxOrder::class);
    }
    // public function getOrderCostAttribute()
    // {
    //     if(in_array($this->attributes['type_of_delivery'],["KSA"])) {
    //         return  $this->attributes['order_cost']/ 4.3;
    //     } elseif(in_array($this->attributes['type_of_delivery'] ,["Libya","Tezkar libya","Texor libya","Benar","Dara","Lemar"] )) {
    //         return  $this->attributes['order_cost'] / 3;
    //     }else{
    //         return  $this->attributes['order_cost'];

    //     }
    // }
    
    // public function getShippingFeesAttribute()
    // {
    //     if(in_array($this->attributes['type_of_delivery'],["KSA"])) {
    //         return $this->attributes['shipping_fees'] / 4.3;
    //     } elseif(in_array($this->attributes['type_of_delivery'],["Libya","Tezkar libya","Texor libya","Benar","Dara","Lemar"])) {
    //         return $this->attributes['shipping_fees'] / 3;
    //     }else{
    //         return $this->attributes['shipping_fees']; 
    //     }
    // }
    // public function getPriceAttribute()
    // {
    //     if(in_array($this->attributes['type_of_delivery'],['KSA'])) {
    //         return $this->attributes['price'] / 4.3;
    //     } elseif(in_array($this->attributes['type_of_delivery'],["Libya","Tezkar libya","Texor libya","Benar","Dara","Lemar"])) {
    //         return $this->attributes['price'] / 3;
    //     }else{
    //         return $this->attributes['price']; 
    //     }
    // }
}
