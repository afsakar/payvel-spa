<?php

namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

trait CompanyScope
{
    /**
     * Apply the scope to a given Eloquent query builder.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $builder
     * @param  \Illuminate\Http\Request  $request
     * @return void
     */
    public function scopeCompany(Builder $builder, Request $request)
    {
        $companyId = $request->header('company_id');
        if ($companyId) {
            return $builder->where('company_id', $companyId);
        }
        return $builder;
    }
}
