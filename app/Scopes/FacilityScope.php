<?php

namespace App\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Support\Facades\Auth;

class FacilityScope implements Scope
{
    public function apply(Builder $builder, Model $model)
    {
        $user = Auth::user();
        
        if (!$user) {
            return;
        }

        if ($user->hasRole('super administrator')) {
            return;
        }

        if ($user->hasRole(['facility staff administrator', 'facility staff user', 'facility staff reader'])) {
            $facilityIds = $user->facility_staffs->pluck('id')->toArray();
            $builder->whereHas('people_facilities', function ($query) use ($facilityIds) {
                $query->whereIn('facility_id', $facilityIds);
            });
        } elseif ($user->hasRole(['client family user', 'client family reader'])) {
            $builder->whereHas('people_family', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            });
        } else {
            $builder->where('id', 0); // 上記のロールを持たないユーザーの場合、結果を返さない
        }
    }
}