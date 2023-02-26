<?php

namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class CompanyScope implements Scope
{
    /**
     * Apply the scope to a given Eloquent query builder.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $builder
     * @param  \Illuminate\Http\Request  $request
     * @return void
     */
    public function apply(Builder $builder, Model $model)
    {
        $companyId = request()->header('company_id');

        if ($companyId) {
            $builder->where('company_id', $companyId);
        }
    }
}
