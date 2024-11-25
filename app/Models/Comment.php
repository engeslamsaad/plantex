<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    use HasFactory;
    protected $casts = [
        'created_at' => 'datetime:Y-m-d h:i:s',
        'updated_at' => 'datetime:Y-m-d h:i:s',
        'deleted_at' => 'datetime:Y-m-d h:i:s'
     ];
    protected $fillable=[
        'order_id','comment','user_id'
    ];

    public function user()
    {
        return $this->belongsTo(Admin::class, 'user_id', 'id');
    }
}
