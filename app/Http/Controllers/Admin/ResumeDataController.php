<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\ResumeResource;
use App\Models\Resume;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Str;

class ResumeDataController extends Controller
{
    public function show(Resume $resume)
    {
        $resume->load([
            'personalDetails',
            'educationDetails',
            'skills',
            'professionalExperience',
            'otherExperience',
            'projects',
            'certifications',
            'user',
        ]);

        $resumeData = (new ResumeResource($resume))->toArray(request());

        return view('admin.resumes.show', [
            'resume' => $resume,
            'resumeData' => $resumeData['data'],
        ]);
    }

    public function preview(Resume $resume)
    {
        $resume->load([
            'personalDetails',
            'educationDetails',
            'skills',
            'professionalExperience',
            'otherExperience',
            'projects',
            'certifications',
            'user',
        ]);

        $resumeData = (new ResumeResource($resume))->toArray(request());

        return view('admin.resumes.preview', [
            'resume' => $resume,
            'resumeData' => $resumeData['data'],
        ]);
    }
}
