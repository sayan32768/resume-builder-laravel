<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreResumeRequest;
use App\Http\Requests\UpdateResumeRequest;
use App\Http\Resources\ResumeResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ResumeController extends Controller
{
    public function getPastResumes(Request $request)
    {
        $resumes = $request->user()
            ->resumes()
            ->orderByDesc('updated_at')
            ->get(['id', 'resumeTitle', 'resumeType', 'updated_at']);

        return response()->json([
            'success' => true,
            'data' => $resumes->map(fn($r) => [
                '_id' => $r->id,
                'resumeTitle' => $r->resumeTitle,
                'resumeType' => $r->resumeType,
                'updatedAt' => $r->updated_at,
            ])
        ]);
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
        ]);

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


        DB::transaction(function () use ($resume, $data) {

            $resume->update([
                'resumeTitle' => $data['resumeTitle'],
                'resumeType' => $data['resumeType'],
            ]);

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
        $resume = $request->user()
            ->resumes()
            ->findOrFail($id);

        $resume->delete();

        return response()->json(['success' => true]);
    }
}
