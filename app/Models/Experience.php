<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Experience extends Model
{
    use HasFactory, HasUuids;

    public $timestamps = false;

    protected $fillable = [
        'resumeId',
        'companyName',
        'companyAddress',
        'position',
        'startDate',
        'endDate',
        'workDescription',
        'category',
    ];
}
