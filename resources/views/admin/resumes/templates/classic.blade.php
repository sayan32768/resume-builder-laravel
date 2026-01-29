{{-- resources/views/admin/resumes/templates/classic.blade.php --}}

@php
    $data = $resumeData ?? [];

    $color = $data['accentColor'] ?? '#111827';

    $personal = $data['personalDetails'] ?? [];
    $education = $data['educationDetails'] ?? [];
    $experience = $data['professionalExperience'] ?? [];
    $projects = $data['projects'] ?? [];
    $otherExp = $data['otherExperience'] ?? [];
    $certifications = $data['certifications'] ?? [];
    $skills = $data['skills'] ?? [];

    function formatDateRange($dates)
    {
        if (!$dates) {
            return '';
        }

        $startDate = $dates['startDate'] ?? null;
        $endDate = $dates['endDate'] ?? null;

        $start = $startDate ? strtotime($startDate) : null;
        $end = $endDate ? strtotime($endDate) : null;

        if ($start && $end) {
            return date('Y', $start) . ' - ' . date('Y', $end);
        }
        if ($start) {
            return date('Y', $start) . ' - Present';
        }
        if ($end) {
            return 'Ended ' . date('Y', $end);
        }

        return '';
    }
@endphp

<div class="resume-container">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Outfit:wght@100..900&display=swap');

        .resume-container * {
            font-family: "Outfit", sans-serif !important;
        }

        .resume-container {
            display: flex;
            width: 210mm;
            height: 297mm;
            background-color: #ffffff;
            overflow: hidden;
            box-sizing: border-box;
        }

        .left-section {
            width: 35%;
            background-color: {{ $color }};
            color: #f9fafb;
            padding: 32px 24px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .right-section {
            width: 65%;
            padding: 36px 40px;
            color: #111827;
            display: flex;
            flex-direction: column;
        }

        .section-title {
            font-size: 16px;
            font-weight: 700;
            text-transform: uppercase;
            border-bottom: 1px solid #ffffff;
            padding-bottom: 8px;
            margin-bottom: 12px;
        }

        .contact-info,
        .edu-entry,
        .cert-entry {
            font-size: 13px;
            line-height: 1.6;
        }

        .contact-item {
            display: flex;
            align-items: center;
            gap: 6px;

        }

        .edu-entry p,
        .cert-entry p {
            margin: 0;
        }

        .skill-list {
            font-size: 13px;
            list-style: disc;
            padding-left: 18px;
        }

        .language {
            font-size: 13px;
        }

        .header-name {
            font-size: 32px;
            font-weight: 800;
            color: #111827;
            margin-bottom: 4px;
        }

        .header-title {
            font-size: 16px;
            font-weight: 500;
            color: #6b7280;
            margin-bottom: 20px;
        }

        .section-block {
            margin-bottom: 20px;
        }

        .section-heading {
            font-size: 18px;
            font-weight: 700;
            color: #1f2937;
            border-bottom: 2px solid #e5e7eb;
            padding-bottom: 4px;
            margin-bottom: 8px;
        }

        .about-text,
        .proj-desc,
        .other-desc {
            font-size: 13px;
            color: #374151;
            line-height: 1.6;
        }

        .exp-date {
            font-size: 13px;
            color: #6b7280;
            font-weight: 500;
        }

        .exp-company {
            font-weight: 700;
            font-size: 14px;
            color: #111827;
        }

        .exp-position {
            font-weight: 600;
            font-size: 13px;
            color: #1d4ed8;
        }

        .exp-list {
            font-size: 13px;
            line-height: 1.5;
        }

        .proj-title {
            font-weight: 700;
            font-size: 14px;
        }

        .proj-links {
            padding-left: 16px;
        }

        .proj-links a {
            color: #2563eb;
            text-decoration: underline;
            font-size: 13px;
        }

        .resume-container .lucide {
            height: 14px;
            width: 14px;
        }
    </style>

    {{-- LEFT SECTION --}}
    <div class="left-section">
        <div>
            <h2 class="section-title">Contact</h2>

            <div class="contact-info">
                {{-- Email --}}
                <div class="contact-item">
                    <x-lucide-mail class="lucide" />
                    {{ blank($personal['email'] ?? null) ? 'you@example.com' : $personal['email'] }}
                </div>

                {{-- Phone --}}
                <div class="contact-item">
                    <x-lucide-phone class="lucide" />
                    {{ blank($personal['phone'] ?? null) ? '+91 XXXXXXXXXX' : $personal['phone'] }}
                </div>

                {{-- Address --}}
                <div class="contact-item">
                    <x-lucide-map-pin class="lucide" />
                    {{ blank($personal['address'] ?? null) ? 'Your Address' : $personal['address'] }}
                </div>

                {{-- Socials --}}
                @if (!empty($personal['socials']) && is_array($personal['socials']))
                    @foreach ($personal['socials'] as $i => $s)
                        @php
                            $socialName = strtolower(trim($s['name'] ?? ''));
                        @endphp

                        <div class="contact-item">
                            @if ($socialName === 'linkedin')
                                <x-lucide-linkedin class="lucide" />
                            @elseif ($socialName === 'github')
                                <x-lucide-github class="lucide" />
                            @elseif ($socialName === 'twitter')
                                <x-lucide-twitter class="lucide" />
                            @else
                                <x-lucide-globe class="lucide" />
                            @endif

                            <span>
                                {{ $s['name'] ?? '' }}:
                                @if (!empty($s['link']))
                                    <a href="{{ $s['link'] }}" target="_blank" rel="noopener noreferrer"
                                        style="color:#93c5fd">
                                        {{ $s['link'] }}
                                    </a>
                                @else
                                    -
                                @endif
                            </span>
                        </div>
                    @endforeach
                @endif

            </div>

            {{-- EDUCATION --}}
            @if (!empty($education))
                <h2 class="section-title" style="margin-top:24px;">Education</h2>

                @foreach ($education as $i => $edu)
                    <div class="edu-entry" style="margin-bottom:12px;">
                        <p style="font-weight:600;">
                            {{ $edu['degree'] ?? 'Degree' }}
                            @if (!empty($edu['name']))
                                • {{ $edu['name'] }}
                            @endif
                        </p>

                        @if (!empty($edu['location']))
                            <p style="color:#d1d5db;">{{ $edu['location'] }}</p>
                        @endif

                        <p style="font-size:12px; color:#9ca3af;">
                            {{ formatDateRange($edu['dates'] ?? null) }}
                        </p>

                        @if (!empty($edu['grades']['score']) && !empty($edu['grades']['type']))
                            <p style="color:#d1d5db;">
                                @if (($edu['grades']['type'] ?? '') === 'CGPA')
                                    CGPA: {{ $edu['grades']['score'] }}
                                @else
                                    Percentage: {{ $edu['grades']['score'] }}%
                                @endif
                            </p>
                        @endif
                    </div>
                @endforeach
            @endif

            {{-- SKILLS --}}
            @if (!empty($skills))
                <h2 class="section-title" style="margin-top:24px;">Skills</h2>
                <ul class="skill-list">
                    @foreach ($skills as $i => $s)
                        <li>{{ is_array($s) ? $s['skillName'] ?? '' : $s }}</li>
                    @endforeach
                </ul>
            @endif

            {{-- CERTIFICATIONS --}}
            @if (!empty($certifications))
                <h2 class="section-title" style="margin-top:24px;">Certifications</h2>

                @foreach ($certifications as $i => $cert)
                    <div class="cert-entry" style="margin-bottom:12px;">
                        @if (!empty($cert['issueDate']))
                            <p style="font-style:italic; color:#9ca3af;">
                                ({{ date('Y', strtotime($cert['issueDate'])) }})
                            </p>
                        @endif

                        <p style="font-weight:600;">
                            {{ $cert['title'] ?? 'Certificate Title' }}
                        </p>

                        <p style="color:#d1d5db;">
                            {{ $cert['issuingAuthority'] ?? 'Authority' }}
                        </p>

                        @if (!empty($cert['link']))
                            <a href="{{ $cert['link'] }}" target="_blank" rel="noopener noreferrer"
                                style="color:#93c5fd; font-size:12px;">
                                {{ $cert['link'] }}
                            </a>
                        @endif
                    </div>
                @endforeach
            @endif

            {{-- LANGUAGES --}}
            @if (array_key_exists('languages', $personal))
                <h2 class="section-title" style="margin-top:24px;">Languages</h2>
                <p class="language">
                    @if (!empty($personal['languages']) && is_array($personal['languages']))
                        {{ implode(', ', $personal['languages']) }}
                    @else
                        English
                    @endif
                </p>
            @endif
        </div>
    </div>

    {{-- RIGHT SECTION --}}
    <div class="right-section">
        <div>
            <h1 class="header-name">{{ $personal['fullName'] ?? 'Your Name' }}</h1>
        </div>

        {{-- ABOUT --}}
        @if (!empty($personal['about']))
            <section class="section-block">
                <p class="about-text">{{ $personal['about'] }}</p>
            </section>
        @endif

        {{-- EXPERIENCE --}}
        @if (!empty($experience))
            <section class="section-block">
                <h2 class="section-heading">Professional Experience</h2>

                @foreach ($experience as $i => $exp)
                    <div style="margin-bottom:12px;">
                        <p class="exp-date">{{ formatDateRange($exp['dates'] ?? null) }}</p>
                        <p class="exp-company">{{ $exp['companyName'] ?? 'Company Name' }}</p>
                        @if (!empty($exp['companyAddress']))
                            <p>{{ $exp['companyAddress'] }}</p>
                        @endif
                        <p class="exp-position">{{ $exp['position'] ?? 'Position' }}</p>

                        @if (!empty($exp['workDescription']))
                            <p class="exp-list">{{ $exp['workDescription'] }}</p>
                        @endif
                    </div>
                @endforeach
            </section>
        @endif

        {{-- PROJECTS --}}
        @if (!empty($projects))
            <section class="section-block">
                <h2 class="section-heading">Projects</h2>

                @foreach ($projects as $i => $proj)
                    <div style="margin-bottom:12px;">
                        <p class="proj-title">
                            {{ $proj['title'] ?? ($proj['name'] ?? 'Project Title') }}
                        </p>

                        @if (!empty($proj['description']))
                            <p class="proj-desc">{{ $proj['description'] }}</p>
                        @endif

                        @if (!empty($proj['extraDetails']))
                            <p class="proj-desc">{{ $proj['extraDetails'] }}</p>
                        @endif

                        @if (!empty($proj['links']) && is_array($proj['links']))
                            <ul class="proj-links">
                                @foreach ($proj['links'] as $idx => $l)
                                    @if (!empty($l['link']))
                                        <li>
                                            <a href="{{ $l['link'] }}" target="_blank" rel="noopener noreferrer">
                                                {{ $l['link'] }}
                                            </a>
                                        </li>
                                    @endif
                                @endforeach
                            </ul>
                        @endif
                    </div>
                @endforeach
            </section>
        @endif

        {{-- OTHER EXPERIENCE --}}
        @if (!empty($otherExp))
            <section class="section-block">
                <h2 class="section-heading">Other Experience</h2>

                @foreach ($otherExp as $i => $exp)
                    <div style="margin-bottom:12px;">
                        <p class="exp-date">{{ formatDateRange($exp['dates'] ?? null) }}</p>
                        <p class="proj-title">{{ $exp['position'] ?? 'Position' }}</p>

                        <p class="about-text">
                            {{ $exp['companyName'] ?? 'Company Name' }}
                            @if (!empty($exp['companyAddress']))
                                – {{ $exp['companyAddress'] }}
                            @endif
                        </p>

                        @if (!empty($exp['workDescription']))
                            <p class="other-desc">{{ $exp['workDescription'] }}</p>
                        @endif
                    </div>
                @endforeach
            </section>
        @endif
    </div>
</div>
