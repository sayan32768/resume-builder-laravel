<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Certification extends Model
{
    use HasFactory, HasUuids;

    public $timestamps = false;

    protected $fillable = [
        'resumeId',
        'issuingAuthority',
        'title',
        'issueDate',
        'link',
    ];

    protected $casts = [
        'issueDate' => 'date:Y-m-d',
    ];

    public function setIssueDateAttribute($value)
    {
        if (! $value) {
            $this->attributes['issueDate'] = null;
            return;
        }

        $this->attributes['issueDate'] = Carbon::parse($value)
            ->timezone('UTC')
            ->toDateString();
    }
}
