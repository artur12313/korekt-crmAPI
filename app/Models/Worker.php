<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Worker extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'First_name', 'Last_name', 'Bank_account_number', 'phone'
    ];

    protected $table = 'workers';

    public function Settlements()
    {
        return $this->hasMany('App\Models\Settlement', 'worker_id');
    }
}
