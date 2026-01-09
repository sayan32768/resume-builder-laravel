<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory, HasUuids;

    public $timestamps = false;

    protected $fillable = [
        'resumeId',
        'title',
        'description',
        'extraDetails',
        'links',
    ];

    protected $casts = [
        'links' => 'array',
    ];
}
