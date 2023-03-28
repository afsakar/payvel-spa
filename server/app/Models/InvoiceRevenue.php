<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class InvoiceRevenue extends Pivot
{
    protected $table = 'invoice_revenue';

    protected $fillable = [
        'invoice_id',
        'revenue_id',
    ];

    public $incrementing = true;
    public $timestamps = false;
    public $with = ['revenue'];

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    public function revenue()
    {
        return $this->belongsTo(Revenue::class);
    }
}
