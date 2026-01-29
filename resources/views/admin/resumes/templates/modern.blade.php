{{-- resources/views/admin/resumes/templates/modern.blade.php --}}

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

    function formatDateRangeAlt($dates)
    {
        // this matches your React experience logic exactly (with End Date-)
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
            return date('Y', $start) . '- Present';
        }
        if ($end) {
            return 'End Date-' . date('Y', $end);
        }

        return '';
    }
@endphp

<div class="resume-preview">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Crete+Round:ital@0;1&family=Nunito+Sans:ital,opsz,wght@0,6..12,200..1000;1,6..12,200..1000&family=Sanchez:ital@0;1&display=swap');

        .resume-preview * {
            font-family: "Nunito Sans", serif !important;
        }

        .resume-preview {
            width: 210mm;
            height: 297mm;
            background-color: white;
            color: #1f2937;
            margin: 0 auto;
            display: flex;
        }

        .left-column {
            width: 35%;
            background-color: #F8FAFC;
            color: #1f2937;
            padding: 24px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            overflow: hidden;
            border-right: 1px solid #E5E7EB;
        }

        .left-column section {
            margin-bottom: 24px;
        }

        .left-column h3 {
            text-transform: uppercase;
            color: {{ $color }};
            font-weight: 600;
            margin-bottom: 12px;
            letter-spacing: 0.1em;
            font-size: 12px;
        }

        .left-column ul {
            font-size: 12px;
            line-height: 1.5;
            margin: 0;
            padding: 0;
            list-style-type: none;
        }

        .left-column div {
            font-size: 12px;
            margin-bottom: 8px;
        }

        .left-column div p {
            margin: 0;
        }

        .left-column div p.italic {
            font-style: italic;
        }

        .left-column div p.bold {
            font-weight: 600;
        }

        /* Softer link color for editorial feel */
        .left-column a {
            color: {{ $color }};
            text-decoration: underline;
        }

        /* RIGHT COLUMN */
        .right-column {
            width: 65%;
            padding: 32px;
            overflow: hidden;
        }

        .right-column header {
            border-bottom: 3px solid {{ $color }};
            padding-bottom: 16px;
            margin-bottom: 24px;
        }

        /* FIXED: dark text on white */
        .right-column header h1 {
            font-size: 24px;
            font-weight: 700;
            color: #1f2937;
            letter-spacing: 0.1em;
            text-transform: uppercase;
            margin: 0;
        }

        .right-column header p {
            text-align: left;
            font-size: 12px;
            letter-spacing: 0.3em;
            color: #4b5563;
            margin-top: 4px;
        }

        .right-column section {
            margin-bottom: 24px;
        }

        /* FIXED: dark section headings */
        .right-column h2 {
            text-transform: uppercase;
            color: #1f2937;
            font-weight: 600;
            margin-bottom: 8px;
            letter-spacing: 0.1em;
            font-size: 12px;
        }

        .right-column div {
            margin-bottom: 12px;
        }

        .right-column div p {
            font-size: 12px;
            margin: 0;
        }

        .right-column div p.bold {
            font-weight: 700;
        }

        .right-column div p.semibold {
            font-weight: 600;
        }

        .right-column div p.italic {
            font-style: italic;
            margin-bottom: 4px;
        }

        .right-column div p.description {
            color: #374151;
        }

        .right-column ul {
            font-size: 12px;
            list-style-type: disc;
            padding-left: 16px;
            margin: 0;
        }

        .right-column ul li a {
            color: {{ $color }};
            text-decoration: underline;
        }
    </style>

    {{-- LEFT --}}
    <div class="left-column">
        <div>
            {{-- CONTACT --}}
            <section>
                <h3>contact</h3>
                <ul>
                    @if (!blank($personal['phone'] ?? null))
                        <li>{{ $personal['phone'] }}</li>
                    @endif

                    @if (!blank($personal['email'] ?? null))
                        <li>{{ $personal['email'] }}</li>
                    @endif

                    @if (!blank($personal['address'] ?? null))
                        <li>{{ $personal['address'] }}</li>
                    @endif

                    @if (!empty($personal['socials']) && is_array($personal['socials']))
                        @foreach ($personal['socials'] as $i => $s)
                            <li>
                                {{ blank($s['name'] ?? null) ? '' : $s['name'] }}:
                                {{ blank($s['link'] ?? null) ? '-' : $s['link'] }}
                            </li>
                        @endforeach
                    @endif
                </ul>
            </section>

            {{-- EDUCATION --}}
            @if (!empty($education))
                <section class="flex flex-col gap-2">
                    <h3 class="mb-2 text-lg font-semibold text-slate-800">Education</h3>

                    @foreach ($education as $i => $edu)
                        <div>
                            @if (!blank($edu['degree'] ?? null) || !blank($edu['name'] ?? null))
                                <p class="font-medium text-slate-900">
                                    {{ blank($edu['degree'] ?? null) ? '' : $edu['degree'] }}
                                    @if (!blank($edu['degree'] ?? null) && !blank($edu['name'] ?? null))
                                        •
                                    @endif
                                    {{ blank($edu['name'] ?? null) ? '' : $edu['name'] }}
                                </p>
                            @endif

                            @if (!blank($edu['location'] ?? null))
                                <p class="text-sm text-slate-600 italic">{{ $edu['location'] }}</p>
                            @endif

                            @if (!blank($edu['dates']['startDate'] ?? null) || !blank($edu['dates']['endDate'] ?? null))
                                <p class="text-sm text-slate-700">
                                    {{ formatDateRange($edu['dates'] ?? null) }}
                                </p>
                            @endif

                            @if (!blank($edu['grades']['score'] ?? null) && !blank($edu['grades']['type'] ?? null))
                                <p class="text-sm text-slate-700">
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

            {{-- SKILLS --}}
            @if (!empty($skills))
                <section>
                    <h3>key skills</h3>
                    <ul>
                        @foreach ($skills as $i => $s)
                            @if (!blank($s['skillName'] ?? null))
                                <li>{{ $s['skillName'] }}</li>
                            @endif
                        @endforeach
                    </ul>
                </section>
            @endif

            {{-- CERTIFICATIONS --}}
            @if (!empty($certifications))
                <section>
                    <h3>certifications</h3>

                    @foreach ($certifications as $i => $cert)
                        <div>
                            @if (!blank($cert['issueDate'] ?? null))
                                <p class="italic">({{ date('Y', strtotime($cert['issueDate'])) }})</p>
                            @endif

                            <p class="bold">{{ blank($cert['title'] ?? null) ? '' : $cert['title'] }}</p>

                            <p>{{ blank($cert['issuingAuthority'] ?? null) ? '' : $cert['issuingAuthority'] }}</p>

                            @if (!blank($cert['link'] ?? null))
                                <a href="{{ $cert['link'] }}" target="_blank" rel="noopener noreferrer">
                                    {{ $cert['link'] }}
                                </a>
                            @endif
                        </div>
                    @endforeach
                </section>
            @endif
        </div>
    </div>

    {{-- RIGHT --}}
    <div class="right-column">
        <header>
            <h1>{{ blank($personal['fullName'] ?? null) ? '-' : $personal['fullName'] }}</h1>

            @if (!blank($personal['about'] ?? null))
                <p>{{ $personal['about'] }}</p>
            @endif
        </header>

        {{-- EXPERIENCE --}}
        @if (!empty($experience))
            <section>
                <h2>professional experience</h2>

                @foreach ($experience as $i => $exp)
                    <div>
                        {{ formatDateRangeAlt($exp['dates'] ?? null) }}

                        <p class="bold">{{ blank($exp['position'] ?? null) ? '-' : $exp['position'] }}</p>

                        <p class="italic">
                            {{ blank($exp['companyName'] ?? null) ? '-' : $exp['companyName'] }}
                            @if (!blank($exp['companyAddress'] ?? null))
                                – {{ $exp['companyAddress'] }}
                            @endif
                        </p>

                        @if (!blank($exp['workDescription'] ?? null))
                            <p class="description">{{ $exp['workDescription'] }}</p>
                        @endif
                    </div>
                @endforeach
            </section>
        @endif

        {{-- PROJECTS --}}
        @if (!empty($projects))
            <section>
                <h2>projects</h2>

                @foreach ($projects as $i => $item)
                    <div>
                        <p class="bold">
                            {{ blank($item['title'] ?? null) ? (blank($item['name'] ?? null) ? '-' : $item['name']) : $item['title'] }}
                        </p>

                        @if (!blank($item['description'] ?? null))
                            <p class="description">{{ $item['description'] }}</p>
                        @endif

                        @if (!blank($item['extraDetails'] ?? null))
                            <p class="description">{{ $item['extraDetails'] }}</p>
                        @endif

                        @if (!empty($item['links']) && is_array($item['links']))
                            @php
                                $validLinks = collect($item['links'])
                                    ->pluck('link')
                                    ->filter(fn($l) => !blank($l))
                                    ->values()
                                    ->all();
                            @endphp

                            @if (count($validLinks) > 0)
                                <ul>
                                    @foreach ($validLinks as $idx => $lnk)
                                        <li>
                                            <a href="{{ $lnk }}" target="_blank" rel="noopener noreferrer">
                                                {{ $lnk }}
                                            </a>
                                        </li>
                                    @endforeach
                                </ul>
                            @endif
                        @endif
                    </div>
                @endforeach
            </section>
        @endif

        {{-- OTHER EXPERIENCE --}}
        @if (!empty($otherExp))
            <section>
                <h2>other experience</h2>

                @foreach ($otherExp as $i => $exp)
                    <div>
                        {{ formatDateRangeAlt($exp['dates'] ?? null) }}

                        <p class="bold">{{ blank($exp['position'] ?? null) ? '-' : $exp['position'] }}</p>

                        <p class="italic">
                            {{ blank($exp['companyName'] ?? null) ? '-' : $exp['companyName'] }}
                            @if (!blank($exp['companyAddress'] ?? null))
                                – {{ $exp['companyAddress'] }}
                            @endif
                        </p>

                        @if (!blank($exp['workDescription'] ?? null))
                            <p class="description">{{ $exp['workDescription'] }}</p>
                        @endif
                    </div>
                @endforeach
            </section>
        @endif
    </div>
</div>
