<?php

namespace App\Models;

use App\Models\Scopes\CompanyScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Waybill extends Model
{
    use HasFactory, SoftDeletes, CompanyScope;

    protected $fillable = [
        'number',
        'company_id',
        'corporation_id',
        'address',
        'status',
        'content',
        'due_date',
        'waybill_date',
    ];

    protected $dates = [
        'due_date',
        'waybill_date',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function corporation()
    {
        return $this->belongsTo(Corporation::class);
    }

    public function items()
    {
        return $this->hasMany(WaybillItem::class);
    }
}
