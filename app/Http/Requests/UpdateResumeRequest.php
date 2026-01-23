<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateResumeRequest extends FormRequest
{
    public function rules()
    {
        return [
            'resumeTitle' => 'required|string|max:255',
            'resumeType'  => 'required|in:Classic,Modern,Minimal,Charm,Boxed,Bold',
            'accentColor' => 'nullable|string',
            'isDraft' => 'nullable|boolean',

            'personalDetails.fullName' => ['nullable', 'regex:/^[a-zA-Z\s]+$/'],
            'personalDetails.email' => 'nullable|string',
            'personalDetails.phone' => 'nullable',
            'personalDetails.address' => 'nullable|string',
            'personalDetails.about' => 'nullable|string',

            'personalDetails.socials' => 'array',
            'personalDetails.socials.*.name' => 'in:LINKEDIN,INSTAGRAM,GITHUB',
            'personalDetails.socials.*.link' => 'nullable|url',

            'educationDetails' => 'array',
            'educationDetails.*.name' => 'nullable|string',
            'educationDetails.*.degree' => 'nullable|string',
            'educationDetails.*.location' => 'nullable|string',
            'educationDetails.*.dates.startDate' => 'nullable|date',
            'educationDetails.*.dates.endDate' => 'nullable|date',
            'educationDetails.*.grades.type' => 'nullable|in:Percentage,CGPA',
            'educationDetails.*.grades.score' => 'nullable|string',
            'educationDetails.*.grades.message' => 'nullable|string',

            'skills' => 'array',
            'skills.*.skillName' => 'nullable|string',

            'professionalExperience' => 'array',
            'professionalExperience.*.companyName' => 'nullable|string',
            'professionalExperience.*.companyAddress' => 'nullable|string',
            'professionalExperience.*.position' => 'nullable|string',
            'professionalExperience.*.dates.startDate' => 'nullable|date',
            'professionalExperience.*.dates.endDate' => 'nullable|date',
            'professionalExperience.*.workDescription' => 'nullable|string',

            'otherExperience' => 'array',
            'otherExperience.*.companyName' => 'nullable|string',
            'otherExperience.*.companyAddress' => 'nullable|string',
            'otherExperience.*.position' => 'nullable|string',
            'otherExperience.*.dates.startDate' => 'nullable|date',
            'otherExperience.*.dates.endDate' => 'nullable|date',
            'otherExperience.*.workDescription' => 'nullable|string',

            'projects' => 'array',
            'projects.*.title' => 'nullable|string',
            'projects.*.description' => 'nullable|string',
            'projects.*.extraDetails' => 'nullable|string',
            'projects.*.links' => 'array',
            'projects.*.links.*.link' => 'nullable|url',

            'certifications' => 'array',
            'certifications.*.issuingAuthority' => 'nullable|string',
            'certifications.*.title' => 'nullable|string',
            'certifications.*.issueDate' => 'nullable|date|before:today',
            'certifications.*.link' => 'nullable|url',
        ];
    }
}
