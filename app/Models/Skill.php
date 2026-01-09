<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Skill extends Model
{
    use HasFactory, HasUuids;

    public $timestamps = false;

    protected $fillable = ['resumeId', 'skills'];

    protected $casts = [
        'skills' => 'array',
    ];

    protected $attributes = [
        'skills' => '[]',
    ];
}
