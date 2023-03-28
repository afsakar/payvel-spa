<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExchangeRate extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'rates' => 'array',
    ];

    protected $dates = [
        'date',
    ];

    protected $fillable = [
        'date',
        'rates',
    ];

    public function scopeToday($query)
    {
        return $query->whereDate('date', now()->format('Y-m-d'));
    }

    public function scopeYesterday($query)
    {
        return $query->whereDate('date', now()->subDay()->format('Y-m-d'));
    }

    public function scopeLastWeek($query)
    {
        return $query->whereDate('date', '>=', now()->subWeek()->format('Y-m-d'));
    }
}
