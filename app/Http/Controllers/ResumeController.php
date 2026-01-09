<?php

namespace App\Http\Controllers;

use App\Models\Certification;
use App\Models\Education;
use App\Models\Experience;
use App\Models\PersonalDetail;
use App\Models\Project;
use App\Models\Resume;
use App\Models\Skill;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ResumeController extends Controller
{
    public function getPastResumes(Request $request)
    {
        $userId = $request->query('userId');

        if (!$userId) {
            return response()->json([
                'success' => false,
                'message' => 'User ID is required',
            ], 400);
        }

        $resumes = Resume::where('userId', $userId)
            ->orderBy('updated_at', 'desc')
            ->get([
                'id',
                'resumeTitle',
                'resumeType',
                'updated_at',
            ]);

        if ($resumes->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'No resumes found for this user',
            ], 404);
        }

        // Match Express response keys
        $formatted = $resumes->map(function ($resume) {
            return [
                '_id' => $resume->id,
                'resumeTitle' => $resume->resumeTitle,
                'resumeType' => $resume->resumeType,
                'updatedAt' => $resume->updated_at,
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $formatted,
        ], 200);
    }


    public function show(string $id)
    {
        $resume = Resume::with([
            'personalDetail',
            'educations',
            'experiences',
            'projects',
            'certifications',
            'skills',
        ])->findOrFail($id);

        $response = [
            'resumeTitle' => $resume->resumeTitle ?? '',
            'resumeType'  => $resume->resumeType ?? 'Modern',

            /* ---------------- Personal Details ---------------- */
            'personalDetails' => $resume->personalDetail ? [
                'fullName' => $resume->personalDetail->fullName ?? '',
                'email'    => $resume->personalDetail->email ?? '',
                'phone'    => $resume->personalDetail->phone ?? '',
                'address'  => $resume->personalDetail->address ?? '',
                'about'    => $resume->personalDetail->about ?? '',
                'socials'  => $resume->personalDetail->socials ?? [],
            ] : (object)[],

            /* ---------------- Education ---------------- */
            'educationDetails' => $resume->educations->map(fn($edu) => [
                'name'     => $edu->name ?? '',
                'degree'   => $edu->degree ?? '',
                'location' => $edu->location ?? '',
                'dates'    => [
                    'startDate' => $edu->startDate,
                    'endDate'   => $edu->endDate,
                ],
                'grades' => [
                    'type'    => $edu->grades['type'] ?? '',
                    'score'   => $edu->grades['score'] ?? '',
                    'message' => $edu->grades['message'] ?? '',
                ],
            ])->values(),

            /* ---------------- Skills ---------------- */
            'skills' => $resume->skills
                ? $resume->skills->skills
                : [],

            /* ---------------- Professional Experience ---------------- */
            'professionalExperience' => $resume->experiences
                ->where('category', 'professional')
                ->map(fn($exp) => [
                    'companyName'    => $exp->companyName ?? '',
                    'companyAddress' => $exp->companyAddress ?? '',
                    'position'       => $exp->position ?? '',
                    'dates' => [
                        'startDate' => $exp->startDate,
                        'endDate'   => $exp->endDate,
                    ],
                    'workDescription' => $exp->workDescription ?? '',
                ])
                ->values(),

            /* ---------------- Other Experience ---------------- */
            'otherExperience' => $resume->experiences
                ->where('category', 'other')
                ->map(fn($exp) => [
                    'companyName'    => $exp->companyName ?? '',
                    'companyAddress' => $exp->companyAddress ?? '',
                    'position'       => $exp->position ?? '',
                    'dates' => [
                        'startDate' => $exp->startDate,
                        'endDate'   => $exp->endDate,
                    ],
                    'workDescription' => $exp->workDescription ?? '',
                ])
                ->values(),

            /* ---------------- Projects ---------------- */
            'projects' => $resume->projects->map(fn($project) => [
                'title'        => $project->title ?? '',
                'description'  => $project->description ?? '',
                'extraDetails' => $project->extraDetails ?? '',
                'links'        => $project->links ?? [],
            ])->values(),

            /* ---------------- Certifications ---------------- */
            'certifications' => $resume->certifications->map(fn($cert) => [
                'issuingAuthority' => $cert->issuingAuthority ?? '',
                'title'            => $cert->title ?? '',
                'issueDate'        => $cert->issueDate,
                'link'             => $cert->link ?? '',
            ])->values(),
        ];

        return response()->json([
            'success' => true,
            'data' => $response,
        ]);
    }



    // public function index()
    // {
    //     $resumes = Resume::with([
    //         'personalDetail',
    //         'educations',
    //         'experiences',
    //         'projects',
    //         'certifications',
    //     ])->get();

    //     return response()->json($resumes);
    // }

    public function create(Request $request)
    {
        try {
            // ⚠️ TEMP: same as req.userId (replace later with auth()->id())
            $userId = $request->input('userId');

            if (!$userId) {
                return response()->json([
                    'success' => false,
                    'message' => 'User ID is required',
                ], 400);
            }

            $resume = Resume::create([
                'userId' => $userId,
                'resumeTitle' => $request->input('resumeTitle', ''),
                'resumeType' => $request->input('resumeType'),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Resume Created Successfully',
                'data' => [
                    '_id' => $resume->id,
                    'resumeTitle' => $resume->resumeTitle,
                    'resumeType' => $resume->resumeType,
                ],
            ], 201);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Some error occurred' . $e,
            ], 500);
        }
    }

    public function update(Request $request, string $id)
    {
        // TEMP: same role as req.userId
        $userId = $request->input('userId');

        if (!$userId) {
            return response()->json([
                'success' => false,
                'message' => 'User ID is required',
            ], 400);
        }

        $resume = Resume::where('id', $id)
            ->where('userId', $userId)
            ->first();

        if (!$resume) {
            return response()->json([
                'success' => false,
                'message' => 'Resume not found',
            ], 404);
        }

        DB::transaction(function () use ($request, $resume) {

            /* ---------------- Resume core ---------------- */
            $resume->update([
                'resumeTitle' => $request->input('resumeTitle', ''),
                'resumeType'  => $request->input('resumeType'),
            ]);

            /* ---------------- Personal Details ---------------- */
            if ($request->has('personalDetails')) {
                PersonalDetail::updateOrCreate(
                    ['resumeId' => $resume->id],
                    $request->personalDetails
                );
            }

            /* ---------------- Education ---------------- */
            if ($request->has('educationDetails')) {
                Education::where('resumeId', $resume->id)->delete();

                foreach ($request->educationDetails as $edu) {
                    Education::create([
                        'resumeId' => $resume->id,
                        ...$edu,
                    ]);
                }
            }

            /* ---------------- Skills (JSON) ---------------- */
            if ($request->has('skills')) {
                Skill::updateOrCreate(
                    ['resumeId' => $resume->id],
                    ['skills' => $request->skills]
                );
            }

            /* ---------------- Experience ---------------- */
            if ($request->has('professionalExperience')) {
                Experience::where('resumeId', $resume->id)
                    ->where('category', 'professional')
                    ->delete();

                foreach ($request->professionalExperience as $exp) {
                    Experience::create([
                        'resumeId' => $resume->id,
                        'category' => 'professional',
                        ...$exp,
                    ]);
                }
            }

            if ($request->has('otherExperience')) {
                Experience::where('resumeId', $resume->id)
                    ->where('category', 'other')
                    ->delete();

                foreach ($request->otherExperience as $exp) {
                    Experience::create([
                        'resumeId' => $resume->id,
                        'category' => 'other',
                        ...$exp,
                    ]);
                }
            }

            /* ---------------- Projects ---------------- */
            if ($request->has('projects')) {
                Project::where('resumeId', $resume->id)->delete();

                foreach ($request->projects as $project) {
                    Project::create([
                        'resumeId' => $resume->id,
                        ...$project,
                    ]);
                }
            }

            /* ---------------- Certifications ---------------- */
            if ($request->has('certifications')) {
                Certification::where('resumeId', $resume->id)->delete();

                foreach ($request->certifications as $cert) {
                    Certification::create([
                        'resumeId' => $resume->id,
                        ...$cert,
                    ]);
                }
            }
        });

        return response()->json([
            'success' => true,
            'message' => 'Resume updated successfully',
        ]);
    }

    public function destroy(Request $request, string $id)
    {
        // TEMP: same role as req.userId (replace with auth()->id() later)
        $userId = $request->input('userId');

        if (!$userId) {
            return response()->json([
                'success' => false,
                'message' => 'User ID is required',
            ], 400);
        }

        $resume = Resume::where('id', $id)
            ->where('userId', $userId)
            ->first();

        if (!$resume) {
            return response()->json([
                'success' => false,
                'message' => 'Resume not found',
            ], 404);
        }

        $resume->delete(); // 🔥 cascades to all child tables

        return response()->json([
            'success' => true,
            'message' => 'Resume deleted successfully',
        ]);
    }
}
