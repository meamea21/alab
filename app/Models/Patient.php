<?php

namespace App\Models;

use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class Patient extends Model implements AuthenticatableContract
{
    use HasFactory, Authenticatable;

    protected $fillable = [
        'patient_id',
        'name',
        'surname',
        'sex',
        'birth_date',
    ];

    public function orders()
    {
        return $this->hasMany(Order::class)->distinct();
    }
}

