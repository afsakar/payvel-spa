<?php

namespace App\Models;

use App\Models\Scopes\CompanyScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Invoice extends Model
{
    use HasFactory, SoftDeletes;

    public $fillable = [
        'company_id',
        'corporation_id',
        'withholding_id',
        'waybill_id',
        'issue_date',
        'notes',
        'number',
        'status',
        'discount',
    ];

    public $dates = [
        'issue_date'
    ];

    public $appends = [
        'waybill_items',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function corporation()
    {
        return $this->belongsTo(Corporation::class);
    }

    public function withholding()
    {
        return $this->belongsTo(Withholding::class);
    }

    public function waybill()
    {
        return $this->belongsTo(Waybill::class);
    }

    public function items()
    {
        return $this->hasMany(InvoiceItem::class);
    }

    // waybill items

    public function getWaybillItemsAttribute()
    {
        return $this->waybill->items;
    }

    protected static function booted(): void
    {
        static::addGlobalScope(new CompanyScope);
    }
}
