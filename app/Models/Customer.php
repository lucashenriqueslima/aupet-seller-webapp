<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Customer extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function pets(): HasMany
    {
        return $this->hasMany(Pet::class);
    }
    public function proposals(): HasMany
    {
        return $this->hasMany(Proposal::class);
    }
}
