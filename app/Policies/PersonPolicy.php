<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Person;
use Illuminate\Auth\Access\HandlesAuthorization;

class PersonPolicy
{
    use HandlesAuthorization;

    public function view(User $user, Person $person)
    {
        if ($user->hasRole('super administrator')) {
            return true;
        }

        if ($user->hasRole(['facility staff administrator', 'facility staff user', 'facility staff reader'])) {
            $facilityIds = $user->facility_staffs->pluck('id')->toArray();
            return $person->people_facilities->whereIn('facility_id', $facilityIds)->isNotEmpty();
        }

        if ($user->hasRole(['client family user', 'client family reader'])) {
            return $user->people_family()->where('person_id', $person->id)->exists();
        }

        return false;
    }
}