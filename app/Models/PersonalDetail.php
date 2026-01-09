<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PersonalDetail extends Model
{
    use HasFactory, HasUuids;

    public $timestamps = false;

    protected $fillable = [
        'resumeId',
        'fullName',
        'email',
        'phone',
        'address',
        'about',
        'socials',
    ];

    protected $casts = [
        'socials' => 'array',
    ];

    protected $attributes = [
        'socials' => '[]',
    ];
}
