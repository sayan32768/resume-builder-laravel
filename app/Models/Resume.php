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
        'accentColor',
        'isDraft'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'userId');
    }


    public function replaceRelation($relation, $rows)
    {
        $this->$relation()->delete();
        $this->$relation()->createMany($rows);
    }


    public function personalDetails()
    {
        return $this->hasOne(PersonalDetail::class, 'resumeId');
    }


    public function educationDetails()
    {
        return $this->hasMany(Education::class, 'resumeId');
    }


    public function professionalExperience()
    {
        return $this->hasMany(Experience::class, 'resumeId')
            ->where('category', 'professional');
    }

    public function otherExperience()
    {
        return $this->hasMany(Experience::class, 'resumeId')
            ->where('category', 'other');
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

    protected $casts = [
        'isDraft' => 'boolean',
    ];
}
