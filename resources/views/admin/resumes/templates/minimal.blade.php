{{-- resources/views/admin/resumes/templates/minimal.blade.php --}}

@php
    $data = $resumeData ?? [];

    $color = blank($data['accentColor'] ?? null) ? '#111827' : $data['accentColor'];

    $personal = $data['personalDetails'] ?? [];
    $education = $data['educationDetails'] ?? [];
    $skills = $data['skills'] ?? [];
    $experience = $data['professionalExperience'] ?? [];
    $otherExp = $data['otherExperience'] ?? [];
    $projects = $data['projects'] ?? [];
    $certifications = $data['certifications'] ?? [];

    function yearFromDate($date)
    {
        if (blank($date)) {
            return '';
        }
        $ts = strtotime($date);
        return $ts ? date('Y', $ts) : '';
    }

    function yearsInline($dates)
    {
        // Matches the React inline format:
        // startYear + (endYear ? "–endYear" : "–Present")
        if (!$dates) {
            return '';
        }

        $startDate = $dates['startDate'] ?? null;
        if (blank($startDate)) {
            return '';
        }

        $startYear = yearFromDate($startDate);
        $endDate = $dates['endDate'] ?? null;

        if (!blank($endDate)) {
            return $startYear . '–' . yearFromDate($endDate);
        }

        return $startYear . '–Present';
    }
@endphp

<div class="resume-minimal">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap');

        .resume-minimal {
            width: 210mm;
            height: 297mm;
            padding: 44px 48px;
            font-family: "Inter", system-ui, sans-serif;
            color: #111827;
            background: white;
        }

        /* ================= HEADER ================= */
        .min-header {
            text-align: center;
            margin-bottom: 28px;
        }

        .min-header h1 {
            font-size: 32px;
            font-weight: 800;
            letter-spacing: 0.12em;
            text-transform: uppercase;
            margin: 0;
        }

        .min-header .subtitle {
            margin-top: 10px;
            font-size: 13px;
            color: #4b5563;
            max-width: 520px;
            margin-left: auto;
            margin-right: auto;
            line-height: 1.6;
        }

        /* CONTACT ROW */
        .min-contact {
            margin-top: 14px;
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 10px 22px;
            font-size: 11px;
            color: #374151;
        }

        /* HAIRLINE DIVIDER UNDER HEADER */
        .min-divider {
            margin: 28px 0 20px;
            height: 1px;
            background: #e5e7eb;
        }

        /* ================= SECTION TITLE ================= */
        .min-title {
            margin-top: 26px;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 11px;
            letter-spacing: 0.22em;
            text-transform: uppercase;
            color: {{ $color }};
        }

        .min-title::after {
            content: "";
            flex: 1;
            height: 1px;
            background: #e5e7eb;
        }

        /* ================= BLOCK TEXT ================= */
        .min-block {
            font-size: 12px;
            line-height: 1.65;
            color: #374151;
            margin-bottom: 14px;
        }

        /* ================= EXPERIENCE ================= */
        .min-exp {
            margin-bottom: 18px;
        }

        .min-exp .role {
            font-weight: 600;
            font-size: 13px;
            color: #111827;
        }

        .min-exp .meta {
            font-size: 11px;
            color: #6b7280;
            margin-top: 2px;
        }

        .min-exp .desc {
            margin-top: 6px;
            font-size: 12px;
            line-height: 1.6;
            color: #374151;
        }

        /* ================= SKILLS ================= */
        .min-skills {
            font-size: 12px;
            color: #374151;
            line-height: 1.7;
        }

        .min-skills span:not(:last-child)::after {
            content: " · ";
            color: #9ca3af;
        }

        /* ================= LINKS ================= */
        .min-link {
            display: inline-block;
            margin-top: 4px;
            color: {{ $color }};
            text-decoration: underline;
            font-size: 11px;
        }
    </style>

    {{-- ================= HEADER ================= --}}
    <div class="min-header">
        <h1>{{ blank($personal['fullName'] ?? null) ? '-' : $personal['fullName'] }}</h1>

        @if (!blank($personal['about'] ?? null))
            <p class="subtitle">{{ $personal['about'] }}</p>
        @endif

        <div class="min-contact">
            @if (!blank($personal['email'] ?? null))
                <span>{{ $personal['email'] }}</span>
            @endif

            @if (!blank($personal['phone'] ?? null))
                <span>{{ $personal['phone'] }}</span>
            @endif

            @if (!blank($personal['address'] ?? null))
                <span>{{ $personal['address'] }}</span>
            @endif

            @if (!empty($personal['socials']) && is_array($personal['socials']))
                @foreach ($personal['socials'] as $i => $s)
                    <span>
                        {{ blank($s['name'] ?? null) ? '' : $s['name'] }}:
                        {{ blank($s['link'] ?? null) ? '-' : $s['link'] }}
                    </span>
                @endforeach
            @endif
        </div>
    </div>

    <div class="min-divider"></div>

    {{-- ================= EXPERIENCE ================= --}}
    @if (!empty($experience))
        <div class="min-title">Experience</div>

        @foreach ($experience as $i => $exp)
            <div class="min-exp">
                <div class="role">{{ blank($exp['position'] ?? null) ? '-' : $exp['position'] }}</div>

                <div class="meta">
                    {{ blank($exp['companyName'] ?? null) ? '-' : $exp['companyName'] }}

                    @if (!blank($exp['companyAddress'] ?? null))
                        · {{ $exp['companyAddress'] }}
                    @endif

                    @if (!blank($exp['dates']['startDate'] ?? null))
                        · {{ yearsInline($exp['dates'] ?? null) }}
                    @endif
                </div>

                @if (!blank($exp['workDescription'] ?? null))
                    <div class="desc">{{ $exp['workDescription'] }}</div>
                @endif
            </div>
        @endforeach
    @endif

    {{-- ================= PROJECTS ================= --}}
    @if (!empty($projects))
        <div class="min-title">Projects</div>

        @foreach ($projects as $i => $p)
            <div class="min-exp">
                <div class="role">
                    @if (!blank($p['title'] ?? null))
                        {{ $p['title'] }}
                    @elseif(!blank($p['name'] ?? null))
                        {{ $p['name'] }}
                    @else
                        -
                    @endif
                </div>

                @if (!blank($p['description'] ?? null))
                    <div class="desc">{{ $p['description'] }}</div>
                @endif

                @if (!blank($p['extraDetails'] ?? null))
                    <div class="desc">{{ $p['extraDetails'] }}</div>
                @endif

                @if (!empty($p['links']) && is_array($p['links']))
                    @foreach ($p['links'] as $idx => $l)
                        @if (!blank($l['link'] ?? null))
                            <a href="{{ $l['link'] }}" target="_blank" rel="noopener noreferrer" class="min-link">
                                {{ $l['link'] }}
                            </a>
                        @endif
                    @endforeach
                @endif
            </div>
        @endforeach
    @endif

    {{-- ================= EDUCATION ================= --}}
    @if (!empty($education))
        <div class="min-title">Education</div>

        @foreach ($education as $i => $edu)
            <div class="min-exp">
                <div class="role">
                    {{ blank($edu['degree'] ?? null) ? '' : $edu['degree'] }}
                    @if (!blank($edu['degree'] ?? null) && !blank($edu['name'] ?? null))
                        ·
                    @endif
                    {{ blank($edu['name'] ?? null) ? '' : $edu['name'] }}
                </div>

                <div class="meta">
                    {{ blank($edu['location'] ?? null) ? '' : $edu['location'] }}

                    @if (!blank($edu['dates']['startDate'] ?? null))
                        · {{ yearsInline($edu['dates'] ?? null) }}
                    @endif
                </div>

                @if (!blank($edu['grades']['score'] ?? null))
                    <div class="desc">
                        @if (($edu['grades']['type'] ?? '') === 'CGPA')
                            CGPA: {{ $edu['grades']['score'] }}
                        @else
                            Percentage: {{ $edu['grades']['score'] }}%
                        @endif
                    </div>
                @endif
            </div>
        @endforeach
    @endif

    {{-- ================= SKILLS ================= --}}
    @if (!empty($skills))
        <div class="min-title">Skills</div>

        <div class="min-skills">
            @foreach ($skills as $i => $s)
                @if (!blank($s['skillName'] ?? null))
                    <span>{{ $s['skillName'] }}</span>
                @endif
            @endforeach
        </div>
    @endif

    {{-- ================= CERTIFICATIONS ================= --}}
    @if (!empty($certifications))
        <div class="min-title">Certifications</div>

        @foreach ($certifications as $i => $c)
            <div class="min-exp">
                <div class="role">{{ blank($c['title'] ?? null) ? '' : $c['title'] }}</div>

                <div class="meta">
                    {{ blank($c['issuingAuthority'] ?? null) ? '' : $c['issuingAuthority'] }}

                    @if (!blank($c['issueDate'] ?? null))
                        · {{ yearFromDate($c['issueDate']) }}
                    @endif
                </div>

                @if (!blank($c['link'] ?? null))
                    <a href="{{ $c['link'] }}" target="_blank" rel="noopener noreferrer" class="min-link">
                        {{ $c['link'] }}
                    </a>
                @endif
            </div>
        @endforeach
    @endif

    {{-- ================= OTHER EXPERIENCE ================= --}}
    @if (!empty($otherExp))
        <div class="min-title">Other Experience</div>

        @foreach ($otherExp as $i => $exp)
            <div class="min-exp">
                <div class="role">{{ blank($exp['position'] ?? null) ? '-' : $exp['position'] }}</div>

                <div class="meta">
                    {{ blank($exp['companyName'] ?? null) ? '-' : $exp['companyName'] }}
                    @if (!blank($exp['companyAddress'] ?? null))
                        · {{ $exp['companyAddress'] }}
                    @endif
                </div>

                @if (!blank($exp['workDescription'] ?? null))
                    <div class="desc">{{ $exp['workDescription'] }}</div>
                @endif
            </div>
        @endforeach
    @endif
</div>
