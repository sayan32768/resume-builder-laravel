<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Experience extends Model
{
    use HasFactory, HasUuids;

    public $timestamps = false;

    protected $appends = ['dates'];

    protected $fillable = [
        'resumeId',
        'companyName',
        'companyAddress',
        'position',
        'startDate',
        'endDate',
        'workDescription',
        'category',
        'dates'
    ];

    public function getDatesAttribute()
    {
        return [
            'startDate' => $this->startDate,
            'endDate' => $this->endDate,
        ];
    }

    public function setDatesAttribute($value)
    {
        $this->startDate = $value['startDate'] ?? null;
        $this->endDate = $value['endDate'] ?? null;
    }
}
