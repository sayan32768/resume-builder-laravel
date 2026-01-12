<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ResumeResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'success' => true,
            'data' => [
                'resumeTitle' => $this->resumeTitle ?? '',
                'resumeType'  => $this->resumeType ?? 'Modern',

                /* ---------------- Personal ---------------- */
                'personalDetails' => $this->personalDetails
                    ? [
                        'fullName' => $this->personalDetails->fullName ?? '',
                        'email' => $this->personalDetails->email ?? '',
                        'phone' => $this->personalDetails->phone ?? '',
                        'address' => $this->personalDetails->address ?? '',
                        'about' => $this->personalDetails->about ?? '',
                        'socials' => $this->personalDetails->socials ?? [],
                    ]
                    : (object)[],

                /* ---------------- Education ---------------- */
                'educationDetails' => $this->educationDetails
                    ? $this->educationDetails->map(fn($edu) => [
                        'name' => $edu->name ?? '',
                        'degree' => $edu->degree ?? '',
                        'location' => $edu->location ?? '',
                        'dates' => $edu->dates,
                        'grades' => [
                            'type'    => $edu->grades['type'] ?? null,
                            'score'   => $edu->grades['score'] ?? null,
                            'message' => $edu->grades['message'] !== null
                                ? $edu->grades['message']
                                : '',
                        ],
                    ])->values()
                    : [],

                /* ---------------- Skills ---------------- */
                'skills' => $this->skills?->skills ?? [],

                /* ---------------- Professional Experience ---------------- */
                'professionalExperience' => $this->professionalExperience
                    ? $this->professionalExperience->map(fn($exp) => [
                        'companyName' => $exp->companyName ?? '',
                        'companyAddress' => $exp->companyAddress ?? '',
                        'position' => $exp->position ?? '',
                        'dates' => $exp->dates,
                        'workDescription' => $exp->workDescription ?? '',
                    ])->values()
                    : [],

                /* ---------------- Other Experience ---------------- */
                'otherExperience' => $this->otherExperience
                    ? $this->otherExperience->map(fn($exp) => [
                        'companyName' => $exp->companyName ?? '',
                        'companyAddress' => $exp->companyAddress ?? '',
                        'position' => $exp->position ?? '',
                        'dates' => $exp->dates,
                        'workDescription' => $exp->workDescription ?? '',
                    ])->values()
                    : [],

                /* ---------------- Projects ---------------- */
                'projects' => $this->projects
                    ? $this->projects->map(fn($p) => [
                        'title' => $p->title ?? '',
                        'description' => $p->description ?? '',
                        'extraDetails' => $p->extraDetails ?? '',
                        'links' => $p->links ?? [],
                    ])->values()
                    : [],

                /* ---------------- Certifications ---------------- */
                'certifications' => $this->certifications
                    ? $this->certifications->map(fn($c) => [
                        'issuingAuthority' => $c->issuingAuthority ?? '',
                        'title' => $c->title ?? '',
                        'issueDate' => $c->issueDate,
                        'link' => $c->link ?? '',
                    ])->values()
                    : [],
            ],
        ];
    }
}
