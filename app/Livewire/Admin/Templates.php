<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\Resume;

class Templates extends Component
{
    public $search = '';

    public function render()
    {
        $templates = collect(config('resume_templates'))
            ->when($this->search, function ($col) {
                $search = strtolower(trim($this->search));

                return $col->filter(function ($t) use ($search) {
                    return str_contains(strtolower($t['name']), $search)
                        || str_contains(strtolower($t['key']), $search);
                });
            })
            ->values();

        $usageCounts = Resume::query()
            ->selectRaw('"resumes"."resumeType", COUNT(*) as total')
            ->groupByRaw('"resumes"."resumeType"')
            ->pluck('total', 'resumeType')
            ->mapWithKeys(fn($total, $type) => [strtolower($type) => $total])
            ->toArray();

        return view('livewire.admin.templates', [
            'templates' => $templates,
            'usageCounts' => $usageCounts,
        ]);
    }
}
