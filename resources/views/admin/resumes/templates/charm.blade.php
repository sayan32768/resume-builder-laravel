{{-- resources/views/admin/resumes/templates/charm.blade.php --}}

@php
    $data = $resumeData ?? [];

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

    $theme = [
        'sidebarBg' => '#8F9B8F',
        'headerBg' => '#ECECE5',
        'mainBg' => '#FFFEFA',
        'textDark' => '#374151',
        'textLight' => '#F9FAFB',
        'accent' => '#6B7280',
    ];

    $sidebarColor = blank($data['accentColor'] ?? null) ? $theme['sidebarBg'] : $data['accentColor'];
@endphp

<div class="resume-container">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Outfit:wght@100..900&family=Playfair+Display:ital,wght@0,400..900;1,400..900&display=swap');

        .resume-container {
            display: flex;
            flex-direction: column;
            width: 210mm;
            height: 297mm;
            background-color: {{ $theme['mainBg'] }};
            overflow: hidden;
            box-sizing: border-box;
            font-family: "Outfit", sans-serif;
            color: {{ $theme['textDark'] }};
        }

        .resume-container .lucide {
            height: 14px;
            width: 14px;
        }

        .main-body {
            display: flex;
            flex: 1;
            overflow: hidden;
        }

        .left-sidebar {
            width: 38%;
            background-color: {{ $sidebarColor }};
            color: white;
            padding: 40px 30px;
            display: flex;
            flex-direction: column;
            gap: 30px;
        }

        .right-column {
            width: 62%;
            display: flex;
            flex-direction: column;
        }

        .right-header {
            background-color: {{ $theme['headerBg'] }};
            padding: 40px 40px 30px 40px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .right-content {
            padding: 30px 40px;
            flex: 1;
            display: flex;
            flex-direction: column;
            gap: 25px;
        }

        .footer-bar {
            background-color: {{ $theme['headerBg'] }};
            padding: 15px 40px;
            display: flex;
            justify-content: space-evenly;
            align-items: center;
            flex-wrap: wrap;
            gap: 15px;
            min-height: 50px;
        }

        /* Typography & Styling */
        .name-title {
            font-family: "Playfair Display", serif;
            font-size: 42px;
            font-weight: 700;
            color: #4A4A4A;
            margin: 0;
            line-height: 1.1;
        }

        .job-title {
            font-size: 14px;
            letter-spacing: 3px;
            text-transform: uppercase;
            color: #888;
            margin-top: 8px;
            font-weight: 500;
        }

        .sidebar-section-title {
            font-size: 15px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 2px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.4);
            padding-bottom: 8px;
            margin-bottom: 15px;
            color: white;
        }

        .main-section-title {
            font-size: 16px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 2px;
            color: #8F9B8F;
            margin-bottom: 15px;
            border-bottom: 1px solid #E5E7EB;
            padding-bottom: 5px;
        }

        .sidebar-text {
            font-size: 13px;
            line-height: 1.7;
            color: #F3F4F6;
            opacity: 0.95;
        }

        .skill-item {
            display: block;
            margin-bottom: 6px;
            font-size: 13px;
        }

        /* Experience & Education Styling */
        .entry-block {
            margin-bottom: 16px;
        }

        .entry-title {
            font-weight: 700;
            font-size: 15px;
            color: #333;
        }

        .entry-subtitle {
            font-size: 13px;
            color: #666;
            font-style: italic;
            margin-bottom: 4px;
        }

        .entry-info {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .entry-desc {
            font-size: 13px;
            line-height: 1.6;
            color: #4B5563;
            margin-top: 4px;
        }

        .entry-date {
            font-size: 12px;
            font-weight: 600;
            color: #8F9B8F;
        }

        /* Footer Contacts */
        .contact-pill {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 12px;
            color: #555;
            font-weight: 500;
        }

        a {
            text-decoration: none;
            color: inherit;
        }
    </style>

    {{-- Main Body --}}
    <div class="main-body">

        {{-- Left Sidebar --}}
        <div class="left-sidebar">
            {{-- Profile --}}
            <div class="sidebar-section">
                <h3 class="sidebar-section-title">Profile</h3>
                <p class="sidebar-text">
                    {{ blank($personal['about'] ?? null)
                        ? 'I am a professional with comprehensive knowledge in my field. Experienced in coordinating with stakeholders and managing complex tasks efficiently.'
                        : $personal['about'] }}
                </p>
            </div>

            {{-- Skills --}}
            @if (!empty($skills))
                <div class="sidebar-section">
                    <h3 class="sidebar-section-title">Skills</h3>
                    <div class="sidebar-text">
                        @foreach ($skills as $i => $s)
                            @if (!blank($s['skillName'] ?? null))
                                <span class="skill-item">• {{ $s['skillName'] }}</span>
                            @endif
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- Certifications --}}
            @if (!empty($certifications))
                <div class="sidebar-section">
                    <h3 class="sidebar-section-title">Awards & Certs</h3>

                    @foreach ($certifications as $i => $cert)
                        <div style="margin-bottom:15px;">
                            <p style="font-weight:600; font-size:14px; margin:0;">
                                {{ blank($cert['title'] ?? null) ? 'Certificate Title' : $cert['title'] }}
                            </p>

                            <p style="font-size:12px; opacity:0.8; margin:0;">
                                {{ blank($cert['issuingAuthority'] ?? null) ? '' : $cert['issuingAuthority'] }}
                                @if (!blank($cert['issueDate'] ?? null))
                                    , {{ date('Y', strtotime($cert['issueDate'])) }}
                                @endif
                            </p>
                        </div>
                    @endforeach
                </div>
            @endif

            {{-- Languages --}}
            @if (!empty($personal['languages']) && is_array($personal['languages']))
                <div class="sidebar-section">
                    <h3 class="sidebar-section-title">Languages</h3>
                    <p class="sidebar-text">
                        {{ implode(', ', array_values(array_filter($personal['languages'], fn($l) => !blank($l)))) }}
                    </p>
                </div>
            @endif
        </div>

        {{-- Right Column --}}
        <div class="right-column">

            {{-- Header --}}
            <div class="right-header">
                <h1 class="name-title">
                    {{ blank($personal['fullName'] ?? null) ? 'Your Name' : $personal['fullName'] }}
                </h1>
                <p class="job-title">Professional Profile</p>
            </div>

            {{-- Content --}}
            <div class="right-content">

                {{-- Experience --}}
                @if (!empty($experience))
                    <section>
                        <h2 class="main-section-title">Work Experience</h2>

                        @foreach ($experience as $i => $exp)
                            <div class="entry-block">
                                <div class="entry-info">
                                    <span class="entry-title">
                                        {{ blank($exp['position'] ?? null) ? 'Position' : $exp['position'] }}
                                    </span>
                                    <span class="entry-date">{{ formatDateRange($exp['dates'] ?? null) }}</span>
                                </div>

                                <p class="entry-subtitle">
                                    {{ blank($exp['companyName'] ?? null) ? 'Company Name' : $exp['companyName'] }}
                                    @if (!blank($exp['companyAddress'] ?? null))
                                        | {{ $exp['companyAddress'] }}
                                    @endif
                                </p>

                                @if (!blank($exp['workDescription'] ?? null))
                                    <p class="entry-desc">{{ $exp['workDescription'] }}</p>
                                @endif
                            </div>
                        @endforeach
                    </section>
                @endif

                {{-- Projects --}}
                @if (!empty($projects))
                    <section>
                        <h2 class="main-section-title">Projects</h2>

                        @foreach ($projects as $i => $proj)
                            <div class="entry-block">
                                <p class="entry-title">
                                    {{ blank($proj['title'] ?? null) ? 'Project Title' : $proj['title'] }}
                                </p>

                                @if (!blank($proj['description'] ?? null))
                                    <p class="entry-desc">{{ $proj['description'] }}</p>
                                @endif

                                @if (!blank($proj['extraDetails'] ?? null))
                                    <p class="entry-desc">{{ $proj['extraDetails'] }}</p>
                                @endif

                                @if (!empty($proj['links']) && is_array($proj['links']))
                                    @php
                                        $validLinks = collect($proj['links'])
                                            ->pluck('link')
                                            ->filter(fn($l) => !blank($l))
                                            ->values()
                                            ->all();
                                    @endphp

                                    @if (count($validLinks) > 0)
                                        <div class="entry-desc" style="font-size:12px;">
                                            @foreach ($validLinks as $idx => $link)
                                                <span style="margin-right:10px;">
                                                    <a href="{{ $link }}" target="_blank"
                                                        rel="noopener noreferrer"
                                                        style="color:#8F9B8F; text-decoration:underline;">
                                                        Link
                                                    </a>
                                                </span>
                                            @endforeach
                                        </div>
                                    @endif
                                @endif
                            </div>
                        @endforeach
                    </section>
                @endif

                {{-- Other Experience --}}
                @if (!empty($otherExp))
                    <section>
                        <h2 class="main-section-title">Other Experience</h2>

                        @foreach ($otherExp as $i => $exp)
                            <div class="entry-block">
                                <div class="entry-info">
                                    <span class="entry-title">
                                        {{ blank($exp['position'] ?? null) ? 'Position' : $exp['position'] }}
                                    </span>
                                    <span class="entry-date">{{ formatDateRange($exp['dates'] ?? null) }}</span>
                                </div>

                                <p class="entry-subtitle">
                                    {{ blank($exp['companyName'] ?? null) ? 'Company Name' : $exp['companyName'] }}
                                </p>

                                @if (!blank($exp['workDescription'] ?? null))
                                    <p class="entry-desc">{{ $exp['workDescription'] }}</p>
                                @endif
                            </div>
                        @endforeach
                    </section>
                @endif

                {{-- Education --}}
                @if (!empty($education))
                    <section>
                        <h2 class="main-section-title">Educational History</h2>

                        @foreach ($education as $i => $edu)
                            <div class="entry-block">
                                <div class="entry-info">
                                    <span class="entry-title">
                                        {{ blank($edu['name'] ?? null) ? 'Institution' : $edu['name'] }}
                                    </span>
                                    <span class="entry-date">{{ formatDateRange($edu['dates'] ?? null) }}</span>
                                </div>

                                <p class="entry-subtitle">
                                    {{ blank($edu['degree'] ?? null) ? 'Degree' : $edu['degree'] }}
                                    @if (!blank($edu['location'] ?? null))
                                        | {{ $edu['location'] }}
                                    @endif
                                </p>

                                @if (!blank($edu['grades']['score'] ?? null))
                                    <p class="entry-desc" style="font-style:italic; margin-top:2px;">
                                        @if (($edu['grades']['type'] ?? '') === 'CGPA')
                                            CGPA: {{ $edu['grades']['score'] }}
                                        @else
                                            Percentage: {{ $edu['grades']['score'] }}%
                                        @endif
                                    </p>
                                @endif
                            </div>
                        @endforeach
                    </section>
                @endif

            </div>
        </div>
    </div>

    {{-- Footer Bar --}}
    <div class="footer-bar">
        <div class="contact-pill">
            <x-lucide-phone class="lucide" />
            {{ blank($personal['phone'] ?? null) ? '+91 XXXXXXXXXX' : $personal['phone'] }}
        </div>

        <div class="contact-pill">
            <x-lucide-mail class="lucide" />
            {{ blank($personal['email'] ?? null) ? 'you@example.com' : $personal['email'] }}
        </div>

        @if (!blank($personal['address'] ?? null))
            <div class="contact-pill">
                <x-lucide-map-pin class="lucide" />
                {{ $personal['address'] }}
            </div>
        @endif

        {{-- Socials in Footer --}}
        @if (!empty($personal['socials']) && is_array($personal['socials']))
            @foreach ($personal['socials'] as $i => $s)
                @php
                    $socialName = strtolower(trim($s['name'] ?? ''));
                @endphp

                <div class="contact-pill">
                    @if ($socialName === 'linkedin')
                        <x-lucide-linkedin class="lucide" />
                    @elseif($socialName === 'github')
                        <x-lucide-github class="lucide" />
                    @elseif($socialName === 'twitter')
                        <x-lucide-twitter class="lucide" />
                    @else
                        <x-lucide-globe class="lucide" />
                    @endif

                    @if (!blank($s['link'] ?? null))
                        <a href="{{ $s['link'] }}" target="_blank" rel="noopener noreferrer">
                            {{ blank($s['name'] ?? null) ? 'Social' : $s['name'] }}
                        </a>
                    @else
                        {{ blank($s['name'] ?? null) ? 'Social' : $s['name'] }}
                    @endif
                </div>
            @endforeach
        @endif
    </div>
</div>
