@extends('admin.layouts.app')

@section('title', 'Resume Preview')
@section('pageTitle', 'Resume Preview')

@section('content')
    <div class="space-y-6">

        <div class="flex items-start justify-between">
            <div>
                <h1 class="text-2xl font-bold text-slate-900">
                    {{ $resumeData['resumeTitle'] ?? 'Untitled Resume' }}
                </h1>
                <p class="text-sm text-slate-500">
                    {{ $resume->user->fullName ?? '-' }} ({{ $resume->user->email ?? '-' }})
                </p>
            </div>

            <a href="{{ url()->previous() ?? route('admin.resumes.index') }}"
                class="px-4 py-2 rounded-lg border border-slate-200 text-slate-700 hover:bg-slate-50 text-sm">
                Back
            </a>
        </div>

        {{-- Meta --}}
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-5">
            <div class="text-lg font-bold text-slate-900 mb-3">Resume Info</div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                <div>
                    <div class="text-slate-500">Resume ID</div>
                    <div class="font-medium text-slate-900 break-all">{{ $resume->id }}</div>
                </div>
                <div>
                    <div class="text-slate-500">Template</div>
                    <div class="font-medium text-slate-900">{{ $resumeData['resumeType'] ?? '-' }}</div>
                </div>
                <div>
                    <div class="text-slate-500">Draft</div>
                    <div class="font-medium text-slate-900">{{ $resumeData['isDraft'] ?? true ? 'Yes' : 'No' }}</div>
                </div>
            </div>
        </div>

        {{-- Personal --}}
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-5">
            <div class="text-lg font-bold text-slate-900 mb-4">Personal Details</div>

            @php($p = $resumeData['personalDetails'] ?? [])
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                <div>
                    <div class="text-slate-500">Full Name</div>
                    <div class="font-medium text-slate-900">{{ $p['fullName'] ?? '-' }}</div>
                </div>
                <div>
                    <div class="text-slate-500">Email</div>
                    <div class="font-medium text-slate-900">{{ $p['email'] ?? '-' }}</div>
                </div>
                <div>
                    <div class="text-slate-500">Phone</div>
                    <div class="font-medium text-slate-900">{{ $p['phone'] ?? '-' }}</div>
                </div>
                <div>
                    <div class="text-slate-500">Address</div>
                    <div class="font-medium text-slate-900">{{ $p['address'] ?? '-' }}</div>
                </div>

                <div class="md:col-span-2">
                    <div class="text-slate-500">About</div>
                    <div class="font-medium text-slate-900 whitespace-pre-wrap">{{ $p['about'] ?? '-' }}</div>
                </div>
            </div>
        </div>

        {{-- Social Links --}}
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="p-5 border-b border-slate-200">
                <div class="text-lg font-bold text-slate-900">Social Links</div>
            </div>

            @php($socials = $resumeData['personalDetails']['socials'] ?? [])

            <div class="p-5 space-y-3 text-sm">
                @if (count($socials))
                    @foreach ($socials as $social)
                        <div class="flex items-center justify-between border border-slate-200 rounded-lg px-4 py-3">
                            <div class="font-medium text-slate-900">
                                {{ $social['name'] ?? 'Social' }}
                            </div>
                            <div class="text-slate-600">
                                @if (!empty($social['link']))
                                    <a href="{{ $social['link'] }}" target="_blank"
                                        class="text-brand hover:underline break-all">
                                        {{ $social['link'] }}
                                    </a>
                                @else
                                    -
                                @endif
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="text-slate-500">No social links.</div>
                @endif
            </div>
        </div>


        {{-- Education --}}
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="p-5 border-b border-slate-200">
                <div class="text-lg font-bold text-slate-900">Education</div>
            </div>

            <div class="p-5 space-y-4 text-sm">
                @forelse(($resumeData['educationDetails'] ?? []) as $edu)
                    <div class="border border-slate-200 rounded-lg p-4">
                        <div class="font-semibold text-slate-900">{{ $edu['name'] ?? '-' }}</div>
                        <div class="text-slate-600">{{ $edu['degree'] ?? '-' }} • {{ $edu['location'] ?? '-' }}</div>
                        <div class="text-slate-500 text-xs">{{ $edu['dates'] ?? '' }}</div>
                    </div>
                @empty
                    <div class="text-slate-500">No education records.</div>
                @endforelse
            </div>
        </div>

        {{-- Skills --}}
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-5">
            <div class="text-lg font-bold text-slate-900 mb-4">Skills</div>

            @php($skills = $resumeData['skills'] ?? [])
            @if (count($skills))
                <div class="flex flex-wrap gap-2">
                    @foreach ($skills as $skill)
                        <span class="px-3 py-1 rounded-full text-xs bg-slate-100 text-slate-700">
                            {{ $skill }}
                        </span>
                    @endforeach
                </div>
            @else
                <div class="text-slate-500 text-sm">No skills.</div>
            @endif
        </div>

        {{-- Professional Experience --}}
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="p-5 border-b border-slate-200">
                <div class="text-lg font-bold text-slate-900">Professional Experience</div>
            </div>

            <div class="p-5 space-y-4 text-sm">
                @forelse(($resumeData['professionalExperience'] ?? []) as $exp)
                    <div class="border border-slate-200 rounded-lg p-4">
                        <div class="font-semibold text-slate-900">{{ $exp['position'] ?? '-' }}</div>
                        <div class="text-slate-600">
                            {{ $exp['companyName'] ?? '-' }}
                            @if (!empty($exp['companyAddress']))
                                • {{ $exp['companyAddress'] }}
                            @endif
                        </div>
                        <div class="text-slate-500 text-xs mt-1">{{ $exp['dates'] ?? '' }}</div>

                        @if (!empty($exp['workDescription']))
                            <div class="text-slate-700 whitespace-pre-wrap mt-3">
                                {{ $exp['workDescription'] }}
                            </div>
                        @endif
                    </div>
                @empty
                    <div class="text-slate-500">No professional experience.</div>
                @endforelse
            </div>
        </div>

        {{-- Projects --}}
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="p-5 border-b border-slate-200">
                <div class="text-lg font-bold text-slate-900">Projects</div>
            </div>

            <div class="p-5 space-y-4 text-sm">
                @forelse(($resumeData['projects'] ?? []) as $proj)
                    <div class="border border-slate-200 rounded-lg p-4">
                        <div class="font-semibold text-slate-900">{{ $proj['title'] ?? '-' }}</div>
                        <div class="text-slate-700 whitespace-pre-wrap mt-2">{{ $proj['description'] ?? '' }}</div>
                    </div>
                @empty
                    <div class="text-slate-500">No projects.</div>
                @endforelse
            </div>
        </div>

        {{-- Other Experience --}}
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="p-5 border-b border-slate-200">
                <div class="text-lg font-bold text-slate-900">Other Experience</div>
            </div>

            <div class="p-5 space-y-4 text-sm">
                @forelse(($resumeData['otherExperience'] ?? []) as $exp)
                    <div class="border border-slate-200 rounded-lg p-4">
                        <div class="font-semibold text-slate-900">{{ $exp['position'] ?? '-' }}</div>
                        <div class="text-slate-600">
                            {{ $exp['companyName'] ?? '-' }}
                            @if (!empty($exp['companyAddress']))
                                • {{ $exp['companyAddress'] }}
                            @endif
                        </div>
                        <div class="text-slate-500 text-xs mt-1">{{ $exp['dates'] ?? '' }}</div>

                        @if (!empty($exp['workDescription']))
                            <div class="text-slate-700 whitespace-pre-wrap mt-3">
                                {{ $exp['workDescription'] }}
                            </div>
                        @endif
                    </div>
                @empty
                    <div class="text-slate-500">No other experience.</div>
                @endforelse
            </div>
        </div>


        {{-- Certifications --}}
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="p-5 border-b border-slate-200">
                <div class="text-lg font-bold text-slate-900">Certifications</div>
            </div>

            <div class="p-5 space-y-4 text-sm">
                @forelse(($resumeData['certifications'] ?? []) as $cert)
                    <div class="border border-slate-200 rounded-lg p-4">
                        <div class="font-semibold text-slate-900">
                            {{ $cert['title'] ?? '-' }}
                        </div>

                        <div class="text-slate-600">
                            {{ $cert['issuingAuthority'] ?? '-' }}
                        </div>

                        <div class="text-slate-500 text-xs mt-1">
                            {{ $cert['issueDate'] ?? '' }}
                        </div>

                        @if (!empty($cert['link']))
                            <div class="mt-2">
                                <a href="{{ $cert['link'] }}" target="_blank" class="text-brand hover:underline break-all">
                                    {{ $cert['link'] }}
                                </a>
                            </div>
                        @endif
                    </div>
                @empty
                    <div class="text-slate-500">No certifications.</div>
                @endforelse
            </div>
        </div>




    </div>
@endsection
