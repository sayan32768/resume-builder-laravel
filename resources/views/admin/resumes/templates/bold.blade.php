{{-- resources/views/admin/resumes/templates/bold.blade.php --}}

@php
    $data = $resumeData ?? [];

    $color = blank($data['accentColor'] ?? null) ? '#f14d34' : $data['accentColor'];

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

    function stripHttp($url)
    {
        if (blank($url)) {
            return '';
        }
        return preg_replace('#^https?://#', '', $url);
    }
@endphp

<div class="resume-container">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700;900&display=swap');

        .resume-container {
            display: flex;
            width: 210mm;
            height: 297mm;
            background-color: #ffffff;
            overflow: hidden;
            box-sizing: border-box;
            font-family: 'Roboto', sans-serif;
            color: #222;
        }

        /* ✅ Lucide icon sizing */
        .resume-container .lucide {
            height: 14px;
            width: 14px;
        }

        /* The Left Colored Strip */
        .accent-strip {
            width: 55px;
            height: 100%;
            background-color: {{ $color }};
            flex-shrink: 0;
        }

        /* Main Content Area */
        .content-area {
            flex-grow: 1;
            padding: 40px 45px;
            display: flex;
            flex-direction: column;
        }

        /* Header */
        .header-name {
            font-size: 48px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            line-height: 1;
            color: #000;
            margin-bottom: 15px;
        }

        .contact-line {
            font-size: 13px;
            font-weight: 700;
            color: #000;
            margin-bottom: 25px;
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 6px;
        }

        .contact-separator {
            margin: 0 4px;
        }

        .profile-summary {
            font-size: 13px;
            line-height: 1.5;
            color: #444;
            margin-bottom: 30px;
            text-align: justify;
        }

        /* Sections */
        .section-block {
            margin-bottom: 25px;
        }

        .section-heading {
            font-size: 18px;
            font-weight: 700;
            text-transform: uppercase;
            color: #000;
            margin-bottom: 12px;
            letter-spacing: 0.5px;
        }

        /* Experience / Entries */
        .entry-block {
            margin-bottom: 18px;
        }

        .entry-date {
            font-weight: 700;
            font-size: 13px;
            color: #000;
            margin-bottom: 2px;
        }

        .entry-header {
            font-size: 13px;
            color: #000;
            margin-bottom: 6px;
        }

        .entry-desc {
            font-size: 13px;
            line-height: 1.5;
            color: #444;
        }

        /* Lists within entries (like education bullets) */
        .entry-list {
            margin-top: 4px;
            padding-left: 18px;
            font-size: 13px;
            color: #444;
        }

        .entry-list li {
            margin-bottom: 2px;
        }

        /* Skills Grid */
        .skills-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            column-gap: 20px;
            row-gap: 4px;
        }

        .skill-item {
            font-size: 13px;
            color: #444;
            display: flex;
            align-items: center;
        }

        .skill-bullet {
            margin-right: 8px;
            font-size: 16px;
            line-height: 10px;
        }

        a {
            color: inherit;
            text-decoration: none;
        }
    </style>

    {{-- Vertical Strip --}}
    <div class="accent-strip"></div>

    {{-- Main Content --}}
    <div class="content-area">

        {{-- Name --}}
        <div class="header-name">
            {{ blank($personal['fullName'] ?? null) ? 'Your Name' : $personal['fullName'] }}
        </div>

        {{-- Contact Info Row --}}
        <div class="contact-line">
            @if (!blank($personal['address'] ?? null))
                <span>{{ $personal['address'] }}</span>
                <span class="contact-separator">|</span>
            @endif

            <span>
                {{ blank($personal['phone'] ?? null) ? '(555) 555-5555' : $personal['phone'] }}
            </span>

            <span class="contact-separator">|</span>

            <span>
                {{ blank($personal['email'] ?? null) ? 'email@example.com' : $personal['email'] }}
            </span>

            @if (!empty($personal['socials']) && is_array($personal['socials']))
                @foreach ($personal['socials'] as $i => $s)
                    <span style="display:flex; align-items:center;">
                        <span class="contact-separator">|</span>
                        @if (!blank($s['link'] ?? null))
                            <a href="{{ $s['link'] }}" target="_blank" rel="noopener noreferrer">
                                {{ stripHttp($s['link']) }}
                            </a>
                        @else
                            {{ $s['name'] ?? '' }}
                        @endif
                    </span>
                @endforeach
            @endif
        </div>

        {{-- Summary --}}
        @if (!blank($personal['about'] ?? null))
            <div class="profile-summary">{{ $personal['about'] }}</div>
        @endif

        {{-- Experience --}}
        @if (!empty($experience))
            <div class="section-block">
                <div class="section-heading">Experience</div>

                @foreach ($experience as $i => $exp)
                    <div class="entry-block">
                        <div class="entry-date">{{ formatDateRange($exp['dates'] ?? null) }}</div>

                        <div class="entry-header">
                            {{ blank($exp['position'] ?? null) ? 'Position' : $exp['position'] }}
                            |
                            {{ blank($exp['companyName'] ?? null) ? 'Company' : $exp['companyName'] }}
                            @if (!blank($exp['companyAddress'] ?? null))
                                | {{ $exp['companyAddress'] }}
                            @endif
                        </div>

                        @if (!blank($exp['workDescription'] ?? null))
                            <div class="entry-desc">{{ $exp['workDescription'] }}</div>
                        @endif
                    </div>
                @endforeach
            </div>
        @endif

        {{-- Projects (Styled like Experience) --}}
        @if (!empty($projects))
            <div class="section-block">
                <div class="section-heading">Projects</div>

                @foreach ($projects as $i => $proj)
                    <div class="entry-block">
                        <div class="entry-date">
                            {{ blank($proj['title'] ?? null) ? 'Project Title' : $proj['title'] }}
                        </div>

                        @if (!empty($proj['links']) && is_array($proj['links']))
                            @php
                                $linkStrings = collect($proj['links'])
                                    ->pluck('link')
                                    ->filter(fn($l) => !blank($l))
                                    ->values()
                                    ->all();
                            @endphp

                            @if (count($linkStrings) > 0)
                                <div class="entry-header">
                                    {{ implode(' | ', $linkStrings) }}
                                </div>
                            @endif
                        @endif

                        <div class="entry-desc">
                            {{ $proj['description'] ?? '' }}

                            @if (!blank($proj['extraDetails'] ?? null))
                                <div style="margin-top:4px;">
                                    {{ $proj['extraDetails'] }}
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @endif

        {{-- Other Experience --}}
        @if (!empty($otherExp))
            <div class="section-block">
                <div class="section-heading">Other Experience</div>

                @foreach ($otherExp as $i => $exp)
                    <div class="entry-block">
                        <div class="entry-date">{{ formatDateRange($exp['dates'] ?? null) }}</div>

                        <div class="entry-header">
                            {{ blank($exp['position'] ?? null) ? 'Position' : $exp['position'] }}
                            |
                            {{ blank($exp['companyName'] ?? null) ? 'Company' : $exp['companyName'] }}
                        </div>

                        @if (!blank($exp['workDescription'] ?? null))
                            <div class="entry-desc">{{ $exp['workDescription'] }}</div>
                        @endif
                    </div>
                @endforeach
            </div>
        @endif

        {{-- Education --}}
        @if (!empty($education))
            <div class="section-block">
                <div class="section-heading">Education</div>

                @foreach ($education as $i => $edu)
                    <div class="entry-block">
                        <div class="entry-date">{{ formatDateRange($edu['dates'] ?? null) }}</div>

                        <div class="entry-header">
                            {{ blank($edu['degree'] ?? null) ? 'Degree' : $edu['degree'] }}
                            |
                            {{ blank($edu['name'] ?? null) ? 'University' : $edu['name'] }}
                            @if (!blank($edu['location'] ?? null))
                                | {{ $edu['location'] }}
                            @endif
                        </div>

                        <ul class="entry-list">
                            @if (!blank($edu['grades']['score'] ?? null))
                                <li>
                                    {{ ($edu['grades']['type'] ?? '') === 'CGPA' ? 'CGPA' : 'Grade' }}:
                                    {{ $edu['grades']['score'] }}
                                </li>
                            @endif
                        </ul>
                    </div>
                @endforeach
            </div>
        @endif

        {{-- Certifications (Styled like Education) --}}
        @if (!empty($certifications))
            <div class="section-block">
                <div class="section-heading">Certifications</div>

                @foreach ($certifications as $i => $cert)
                    <div class="entry-block" style="margin-bottom:10px;">
                        <div class="entry-date">
                            @if (!blank($cert['issueDate'] ?? null))
                                {{ date('Y', strtotime($cert['issueDate'])) }}
                            @else
                                Year
                            @endif
                        </div>

                        <div class="entry-header">
                            {{ blank($cert['title'] ?? null) ? 'Certification' : $cert['title'] }}
                            |
                            {{ blank($cert['issuingAuthority'] ?? null) ? 'Authority' : $cert['issuingAuthority'] }}
                        </div>

                        @if (!blank($cert['link'] ?? null))
                            <div class="entry-desc">{{ $cert['link'] }}</div>
                        @endif
                    </div>
                @endforeach
            </div>
        @endif

        {{-- Skills (Two Column Grid) --}}
        @php
            $langs = !empty($personal['languages']) && is_array($personal['languages']) ? $personal['languages'] : [];
        @endphp

        @if (!empty($skills) || !empty($langs))
            <div class="section-block">
                <div class="section-heading">Skills</div>

                <div class="skills-grid">
                    @foreach ($skills as $i => $s)
                        <div class="skill-item">
                            <span class="skill-bullet">•</span>
                            {{ blank($s['skillName'] ?? null) ? '' : $s['skillName'] }}
                        </div>
                    @endforeach

                    @foreach ($langs as $i => $lang)
                        @if (!blank($lang))
                            <div class="skill-item">
                                <span class="skill-bullet">•</span>
                                {{ $lang }} (Language)
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>
        @endif

    </div>
</div>
