<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Resume extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'userId',
        'resumeTitle',
        'resumeType',
    ];

    public function personalDetail()
    {
        return $this->hasOne(PersonalDetail::class, 'resumeId');
    }

    public function educations()
    {
        return $this->hasMany(Education::class, 'resumeId');
    }

    public function experiences()
    {
        return $this->hasMany(Experience::class, 'resumeId');
    }

    public function projects()
    {
        return $this->hasMany(Project::class, 'resumeId');
    }

    public function certifications()
    {
        return $this->hasMany(Certification::class, 'resumeId');
    }

    public function skills()
    {
        return $this->hasOne(Skill::class, 'resumeId');
    }
}
