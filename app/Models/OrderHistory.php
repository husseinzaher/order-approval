<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderHistory extends Model
{
    protected $fillable = ['order_id', 'old_status', 'new_status'];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
