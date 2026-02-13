<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreResumeRequest;
use App\Http\Requests\UpdateResumeRequest;
use App\Http\Resources\ResumeResource;
use App\Services\AuditLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ResumeController extends Controller
{
    private function calculateCompletion($resume): int
    {
        $score = 0;

        if (
            $resume->personalDetails &&
            collect([
                $resume->personalDetails->fullName,
                $resume->personalDetails->email,
                $resume->personalDetails->phone,
                $resume->personalDetails->about,
            ])->filter()->isNotEmpty()
        ) {
            $score += 20;
        }

        if ($resume->educationDetails->isNotEmpty()) {
            $score += 15;
        }

        if (
            $resume->skills &&
            collect($resume->skills->toArray())
            ->except(['id', 'resume_id', 'created_at', 'updated_at'])
            ->filter()
            ->isNotEmpty()
        ) {
            $score += 15;
        }

        if ($resume->professionalExperience->isNotEmpty()) {
            $score += 25;
        }

        if ($resume->projects->isNotEmpty()) {
            $score += 15;
        }

        if ($resume->certifications->isNotEmpty()) {
            $score += 10;
        }

        return min($score, 100);
    }

    public function getPastResumes(Request $request)
    {
        $type = $request->query('type', 'published'); // drafts|published

        $query = $request->user()->resumes()->orderByDesc('updated_at');

        if ($type === 'drafts') $query->where('isDraft', true);
        if ($type === 'published') $query->where('isDraft', false);

        $resumes = $query->paginate(10, ['id', 'resumeTitle', 'resumeType', 'updated_at', 'accentColor', 'isDraft']);

        $resumes->getCollection()->transform(fn($r) => [
            '_id' => $r->id,
            'resumeTitle' => $r->resumeTitle,
            'resumeType' => $r->resumeType,
            'updatedAt' => $r->updated_at,
            'color' => $r->accentColor,
            'isDraft' => $r->isDraft,
            'completion' => $this->calculateCompletion($r),
        ]);

        return response()->json(['success' => true, 'data' => $resumes]);
    }



    public function show(Request $request, string $id)
    {
        $resume = $request->user()
            ->resumes()
            ->with([
                'personalDetails',
                'educationDetails',
                'professionalExperience',
                'otherExperience',
                'projects',
                'certifications',
                'skills',
            ])
            ->findOrFail($id);

        return new ResumeResource($resume);
    }


    public function create(StoreResumeRequest $request)
    {
        $user = $request->user();

        $resume = $user->resumes()->create([
            'resumeTitle' => $request->resumeTitle ?? '',
            'resumeType'  => $request->resumeType,
            'accentColor' => $request->accentColor,
            'isDraft' => $request->isDraft,
        ]);

        AuditLogger::log(
            'RESUME_CREATED',
            $resume,
            null,
            $resume->toArray()
        );

        return response()->json([
            'success' => true,
            'data' => [
                '_id' => $resume->id,
                'resumeTitle' => $resume->resumeTitle,
                'resumeType' => $resume->resumeType,
            ],
        ], 201);
    }


    public function update(UpdateResumeRequest $request, string $id)
    {
        $data = $request->validated();

        $resume = $request->user()
            ->resumes()
            ->findOrFail($id);

        $before = $resume->toArray();


        DB::transaction(function () use ($resume, $data) {

            $resume->update([
                'resumeTitle' => $data['resumeTitle'],
                'resumeType' => $data['resumeType'],
            ]);

            if (isset($data['accentColor'])) {
                $resume->update([
                    'accentColor' => $data['accentColor'],
                ]);
            }

            if (isset($data['isDraft'])) {
                $resume->update([
                    'isDraft' => $data['isDraft'],
                ]);
            }

            if (isset($data['personalDetails'])) {
                $resume->personalDetails()->updateOrCreate(
                    [],
                    $data['personalDetails']
                );
            }

            if (isset($data['educationDetails'])) {
                $resume->replaceRelation(
                    'educationDetails',
                    collect($data['educationDetails'])->map(fn($e) => [
                        'resumeId'  => $resume->id,
                        'name'      => $e['name'] ?? "",
                        'degree'    => $e['degree'] ?? null,
                        'location'  => $e['location'] ?? null,
                        'startDate' => $e['dates']['startDate'] ?? null,
                        'endDate'   => $e['dates']['endDate'] ?? null,
                        'grades'    => $e['grades'] ?? null,
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

        AuditLogger::log(
            'RESUME_UPDATED',
            $resume,
            $before,
            $resume->fresh()->toArray()
        );

        return response()->json([
            'success' => true,
            'message' => 'Resume updated successfully',
        ]);
    }


    public function destroy(Request $request, string $id)
    {
        $resume = $request->user()
            ->resumes()
            ->findOrFail($id);

        $before = $resume->toArray();

        $resume->delete();

        AuditLogger::log(
            'RESUME_DELETED',
            $resume,
            $before,
            null
        );

        return response()->json(['success' => true]);
    }



    // PROFILE STATS
    // User Dashboard Stats
    public function stats(Request $request)
    {
        $user = $request->user();

        $resumes = $user->resumes();

        $totalResumes = $resumes->count();
        $completedResumes = $resumes->where('isDraft', false)->count();
        $draftResumes = $resumes->where('isDraft', true)->count();


        $averageCompletion = $totalResumes > 0
            ? round(($completedResumes / $totalResumes) * 100)
            : 0;



        $profileStrength = 0;

        if ($user->fullName) $profileStrength += 30;
        if ($user->email) $profileStrength += 30;
        if ($totalResumes > 0) $profileStrength += 40;


        $lastEditedResume = $user->resumes()
            ->latest('updated_at')
            ->first();

        $lastResumeData = null;

        $completion = $this->calculateCompletion($lastEditedResume);

        if ($lastEditedResume) {
            $lastResumeData = [
                'id' => $lastEditedResume->id,
                'title' => $lastEditedResume->resumeTitle,
                'isDraft' => $lastEditedResume->isDraft,
                'updatedAt' => $lastEditedResume->updated_at,
                // temporary completion logic
                'completion' => $completion,
            ];
        }

        return response()->json([
            'totalResumes' => $totalResumes,
            'completedResumes' => $completedResumes,
            // 'draftResumes' => $draftResumes,
            'averageCompletion' => $averageCompletion,
            // 'profileStrength' => $profileStrength,
            // 'lastEditedResumeId' => $lastEditedResume?->id,
            'lastResume' => $lastResumeData,
        ]);
    }
}
