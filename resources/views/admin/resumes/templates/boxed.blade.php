{{-- resources/views/admin/resumes/templates/boxed.blade.php --}}

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

<div class="resume-container boxed-template">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&family=Open+Sans:wght@400;600&display=swap');

        .resume-container {
            display: flex;
            width: 210mm;
            height: 297mm;
            background-color: #ffffff;
            overflow: hidden;
            box-sizing: border-box;
            font-family: 'Open Sans', sans-serif;
            color: #333;
        }

        /* LEFT */
        .left-sidebar {
            width: 32%;
            background-color: #F0F2F5;
            padding: 40px 30px;
            display: flex;
            flex-direction: column;
            gap: 40px;
        }

        /* RIGHT */
        .right-content {
            width: 68%;
            padding: 40px 40px;
            display: flex;
            flex-direction: column;
        }

        /* HEADER BOX */
        .header-box {
            border: 2px solid {{ $color }};
            padding: 25px 20px;
            text-align: center;
            margin-bottom: 40px;
            background: #fff;
            width: 90%;
            align-self: center;
        }

        .header-name {
            font-family: 'Montserrat', sans-serif;
            font-size: 32px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 2px;
            color: {{ $color }};
            margin: 0;
            line-height: 1.2;
        }

        /* SECTION TITLES */
        .section-title {
            font-family: 'Montserrat', sans-serif;
            font-size: 14px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 2px;
            color: #111;
            border-bottom: 1px solid #ccc;
            padding-bottom: 8px;
            margin-bottom: 20px;
        }

        .sidebar-title {
            font-family: 'Montserrat', sans-serif;
            font-size: 13px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 2px;
            color: #111;
            border-bottom: 1px solid #ccc;
            padding-bottom: 6px;
            margin-bottom: 15px;
        }

        /* CONTACTS */
        .contact-group {
            margin-bottom: 20px;
        }

        .contact-label {
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            color: #666;
            margin-bottom: 4px;
            display: block;
        }

        .contact-value {
            font-size: 13px;
            color: #333;
            word-break: break-all;
        }

        /* SKILLS */
        .skill-item {
            margin-bottom: 8px;
            font-size: 13px;
            font-weight: 500;
        }

        /* MAIN BLOCK */
        .main-block {
            margin-bottom: 25px;
        }

        .block-header {
            display: flex;
            justify-content: space-between;
            align-items: baseline;
            margin-bottom: 4px;
            gap: 10px;
        }

        .block-title {
            font-weight: 700;
            font-size: 15px;
            color: #000;
        }

        .block-loc {
            font-size: 12px;
            color: #666;
            text-transform: uppercase;
            text-align: right;
            white-space: nowrap;
        }

        .block-sub {
            font-size: 13px;
            font-weight: 600;
            color: #444;
            margin-bottom: 4px;
        }

        .block-date {
            font-size: 12px;
            color: #888;
            margin-bottom: 8px;
            font-style: italic;
        }

        .block-desc {
            font-size: 13px;
            line-height: 1.6;
            color: #444;
            margin: 0;
        }

        a {
            color: inherit;
            text-decoration: none;
        }

        .proj-links a {
            color: {{ $color }};
            margin-right: 10px;
            text-decoration: underline;
            font-size: 11px;
        }
    </style>

    {{-- LEFT SIDEBAR --}}
    <div class="left-sidebar">

        {{-- DETAILS --}}
        <div>
            <h3 class="sidebar-title">Details</h3>

            <div class="contact-group">
                <span class="contact-label">Address</span>
                <div class="contact-value">{{ $personal['address'] ?? 'City, Country' }}</div>
            </div>

            <div class="contact-group">
                <span class="contact-label">Phone</span>
                <div class="contact-value">{{ $personal['phone'] ?? '(123) 456-7890' }}</div>
            </div>

            <div class="contact-group">
                <span class="contact-label">Email</span>
                <div class="contact-value">{{ $personal['email'] ?? 'hello@example.com' }}</div>
            </div>

            {{-- ✅ Socials: [{"name":"LINKEDIN","link":"http://..."}] --}}
            @if (!empty($personal['socials']) && is_array($personal['socials']))
                @foreach ($personal['socials'] as $i => $s)
                    <div class="contact-group">
                        <span class="contact-label">{{ $s['name'] ?? 'SOCIAL' }}</span>
                        <div class="contact-value">
                            @if (!empty($s['link']))
                                <a href="{{ $s['link'] }}" target="_blank" rel="noopener noreferrer">
                                    View Profile
                                </a>
                            @else
                                -
                            @endif
                        </div>
                    </div>
                @endforeach
            @endif
        </div>

        {{-- SKILLS --}}
        @if (!empty($skills))
            <div>
                <h3 class="sidebar-title">Skills</h3>
                @foreach ($skills as $i => $s)
                    <div class="skill-item">
                        {{ is_array($s) ? $s['skillName'] ?? '' : $s }}
                    </div>
                @endforeach
            </div>
        @endif

        {{-- CERTIFICATIONS --}}
        @if (!empty($certifications))
            <div>
                <h3 class="sidebar-title">Certifications</h3>
                @foreach ($certifications as $i => $cert)
                    <div style="margin-bottom:12px;">
                        <div style="font-size:13px; font-weight:700;">
                            {{ $cert['title'] ?? '' }}
                        </div>
                        <div style="font-size:11px; color:#666;">
                            {{ $cert['issuingAuthority'] ?? '' }}
                        </div>
                        <div style="font-size:11px; font-style:italic; color:#888;">
                            @if (!empty($cert['issueDate']))
                                {{ date('Y', strtotime($cert['issueDate'])) }}
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @endif

    </div>

    {{-- RIGHT CONTENT --}}
    <div class="right-content">

        {{-- HEADER --}}
        <div class="header-box">
            <h1 class="header-name">{{ $personal['fullName'] ?? 'YOUR NAME' }}</h1>
        </div>

        {{-- PROFILE --}}
        @if (!empty($personal['about']))
            <div style="margin-bottom:30px;">
                <h2 class="section-title">Profile</h2>
                <p class="block-desc">{{ $personal['about'] }}</p>
            </div>
        @endif

        {{-- EXPERIENCE --}}
        @if (!empty($experience))
            <div style="margin-bottom:10px;">
                <h2 class="section-title">Employment History</h2>

                @foreach ($experience as $i => $exp)
                    <div class="main-block">
                        <div class="block-header">
                            <span class="block-title">
                                {{ $exp['position'] ?? 'Position' }} at {{ $exp['companyName'] ?? '' }}
                            </span>
                            <span class="block-loc">{{ $exp['companyAddress'] ?? '' }}</span>
                        </div>

                        <div class="block-date">
                            {{ formatDateRange($exp['dates'] ?? null) }}
                        </div>

                        @if (!empty($exp['workDescription']))
                            <p class="block-desc">{{ $exp['workDescription'] }}</p>
                        @endif
                    </div>
                @endforeach
            </div>
        @endif

        {{-- PROJECTS --}}
        @if (!empty($projects))
            <div style="margin-bottom:10px;">
                <h2 class="section-title">Projects</h2>

                @foreach ($projects as $i => $proj)
                    <div class="main-block">
                        <div class="block-header">
                            <span class="block-title">{{ $proj['title'] ?? 'Project Title' }}</span>
                        </div>

                        {{-- ✅ Project links: [{"link":"https://sskks"}] --}}
                        @if (!empty($proj['links']) && is_array($proj['links']))
                            <div class="proj-links" style="margin-bottom:4px;">
                                @foreach ($proj['links'] as $idx => $l)
                                    @if (!empty($l['link']))
                                        <a href="{{ $l['link'] }}" target="_blank" rel="noopener noreferrer">
                                            Link {{ $idx + 1 }}
                                        </a>
                                    @endif
                                @endforeach
                            </div>
                        @endif

                        <p class="block-desc">{{ $proj['description'] ?? '' }}</p>

                        @if (!empty($proj['extraDetails']))
                            <p class="block-desc" style="margin-top:5px;">
                                {{ $proj['extraDetails'] }}
                            </p>
                        @endif
                    </div>
                @endforeach
            </div>
        @endif

        {{-- OTHER EXPERIENCE --}}
        @if (!empty($otherExp))
            <div style="margin-bottom:10px;">
                <h2 class="section-title">Other Experience</h2>

                @foreach ($otherExp as $i => $exp)
                    <div class="main-block">
                        <div class="block-header">
                            <span class="block-title">{{ $exp['position'] ?? '' }}</span>
                            <span class="block-loc">{{ $exp['companyAddress'] ?? '' }}</span>
                        </div>

                        <div class="block-sub">{{ $exp['companyName'] ?? '' }}</div>
                        <div class="block-date">{{ formatDateRange($exp['dates'] ?? null) }}</div>

                        @if (!empty($exp['workDescription']))
                            <p class="block-desc">{{ $exp['workDescription'] }}</p>
                        @endif
                    </div>
                @endforeach
            </div>
        @endif

        {{-- EDUCATION --}}
        @if (!empty($education))
            <div>
                <h2 class="section-title">Education</h2>

                @foreach ($education as $i => $edu)
                    <div class="main-block">
                        <div class="block-header">
                            <span class="block-title">{{ $edu['degree'] ?? 'Degree' }}</span>
                            <span class="block-loc">{{ $edu['location'] ?? '' }}</span>
                        </div>

                        <div class="block-sub">{{ $edu['name'] ?? 'University Name' }}</div>
                        <div class="block-date">{{ formatDateRange($edu['dates'] ?? null) }}</div>

                        @if (!empty($edu['grades']['score']))
                            <div class="block-desc" style="font-size:12px;">
                                {{ $edu['grades']['type'] ?? '' }}: {{ $edu['grades']['score'] }}
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        @endif

    </div>
</div>
