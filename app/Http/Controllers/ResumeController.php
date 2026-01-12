<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreResumeRequest;
use App\Http\Requests\UpdateResumeRequest;
use App\Http\Resources\ResumeResource;
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
            'personalDetails',
            'educationDetails',
            'professionalExperience',
            'otherExperience',
            'projects',
            'certifications',
            'skills',
        ])->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => new ResumeResource($resume),
        ]);
    }


    public function create(StoreResumeRequest $request)
    {
        $data = $request->validated();

        // TEMP: replace later with auth()->id()
        $userId = $request->input('userId');

        if (!$userId) {
            return response()->json([
                'success' => false,
                'message' => 'User ID is required',
            ], 400);
        }

        $resume = Resume::create([
            'userId' => $userId,
            'resumeTitle' => $data['resumeTitle'] ?? '',
            'resumeType'  => $data['resumeType'],
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Resume Created Successfully',
            'data' => [
                '_id' => $resume->id,                  // Mongo compatibility
                'resumeTitle' => $resume->resumeTitle,
                'resumeType' => $resume->resumeType,
            ],
        ], 201);
    }


    public function update(UpdateResumeRequest $request, string $id)
    {
        $data = $request->validated();

        // TEMP auth replacement
        $userId = $request->input('userId');

        if (!$userId) {
            return response()->json([
                'success' => false,
                'message' => 'User ID is required',
            ], 400);
        }

        $resume = Resume::where('id', $id)
            ->where('userId', $userId)
            ->firstOrFail();

        DB::transaction(function () use ($resume, $data) {

            $resume->update([
                'resumeTitle' => $data['resumeTitle'],
                'resumeType' => $data['resumeType'],
            ]);

            if (isset($data['personalDetails'])) {
                $resume->personalDetails()->delete();
                $resume->personalDetails()->create([
                    'resumeId' => $resume->id,
                    ...$data['personalDetails']
                ]);
            }

            if (isset($data['educationDetails'])) {
                $resume->replaceRelation(
                    'educationDetails',
                    collect($data['educationDetails'])->map(fn($e) => [
                        'resumeId' => $resume->id,
                        ...$e
                    ])
                );
            }

            if (isset($data['professionalExperience'])) {
                $resume->replaceRelation(
                    'professionalExperience',
                    collect($data['professionalExperience'])->map(fn($e) => [
                        'resumeId' => $resume->id,
                        'category' => 'professional',
                        ...$e
                    ])
                );
            }

            if (isset($data['otherExperience'])) {
                $resume->replaceRelation(
                    'otherExperience',
                    collect($data['otherExperience'])->map(fn($e) => [
                        'resumeId' => $resume->id,
                        'category' => 'other',
                        ...$e
                    ])
                );
            }

            if (isset($data['projects'])) {
                $resume->replaceRelation(
                    'projects',
                    collect($data['projects'])->map(fn($p) => [
                        'resumeId' => $resume->id,
                        ...$p
                    ])
                );
            }

            if (isset($data['certifications'])) {
                $resume->replaceRelation(
                    'certifications',
                    collect($data['certifications'])->map(fn($c) => [
                        'resumeId' => $resume->id,
                        ...$c
                    ])
                );
            }

            if (isset($data['skills'])) {
                $resume->skills()->updateOrCreate(
                    ['resumeId' => $resume->id],
                    ['skills' => $data['skills']]
                );
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
