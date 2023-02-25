<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WaybillItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'waybill_id',
        'material_id',
        'quantity',
        'price',
    ];

    protected $appends = [
        'unit_name',
    ];

    public function waybill()
    {
        return $this->belongsTo(Waybill::class);
    }

    public function material()
    {
        return $this->belongsTo(Material::class);
    }

    // get material name

    public function getUnitNameAttribute()
    {
        return $this->material->unit->name;
    }
}
