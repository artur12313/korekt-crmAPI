<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Client extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'name', 'town','address', 'phone', 'refersTo', 'range'
    ];

    protected $appends = ['total_sales_value', 'total_purchase_value', 'total_labor'];

    protected $table = 'clients';

    public function author()
    {
        return $this->belongsTo('App\Models\User');
    }

    public function orders()
    {
        return $this->hasMany('App\Models\Order', 'client_id');
    }

    public function getTotalSalesValueAttribute()
    {
        $totalSalesValue = 0;
        foreach($this->orders as $order)
        {
            $totalSalesValue += $order->total_sales_value;
        }
        return $totalSalesValue;
    }

    public function getTotalPurchaseValueAttribute()
    {
        $totalPurchaseValue = 0;
        foreach($this->orders as $order)
        {
            $totalPurchaseValue += $order->total_purchase_value;
        }
        return $totalPurchaseValue;
    }

    public function getTotalGrossSalesValueAttribute()
    {
        $totalGrossSalesValue = 0;
        foreach($this->orders as $order)
        {
            $totalGrossSalesValue += $order->total_gross_sales_value;
        }
        return $totalGrossSalesValue;
    }

    public function getTotalLaborAttribute()
    {
        $totalLabor = 0;
        foreach($this->orders as $order)
        {
            $totalLabor += $order->labor;
        }
        return $totalLabor;
    }
}
