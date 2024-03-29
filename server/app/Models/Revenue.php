<?php

namespace App\Models;

use App\Models\Scopes\CompanyScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Revenue extends Model
{
    use HasFactory;

    protected $fillable = [
        'account_id',
        'company_id',
        'corporation_id',
        'category_id',
        'description',
        'amount',
        'type',
        'due_at'
    ];

    protected $casts = [
        'due_at' => 'datetime'
    ];

    public function account()
    {
        return $this->belongsTo(Account::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function corporation()
    {
        return $this->belongsTo(Corporation::class);
    }

    public function invoices()
    {
        return $this->belongsToMany(Invoice::class, 'invoice_revenue');
    }

    protected static function booted(): void
    {
        static::addGlobalScope(new CompanyScope);
    }
}
