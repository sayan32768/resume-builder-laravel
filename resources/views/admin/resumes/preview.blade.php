{{-- resources/views/admin/resumes/preview.blade.php --}}

@php
    $data = $resumeData ?? [];
    $type = strtolower(trim($data['resumeType'] ?? 'classic'));
    $templateView =
        collect(config('resume_templates'))->firstWhere('key', $type)['view'] ?? 'admin.resumes.templates.classic';
@endphp

<div class="resume-viewer">
    <div class="resume-toolbar">
        <button type="button" onclick="history.back()" class="toolbar-btn">
            ← Back
        </button>
    </div>
    <div class="resume-scale">
        @include($templateView, ['resumeData' => $resumeData])
    </div>
</div>

<style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    :root {
        --resume-scale: 1;
    }

    .resume-viewer {
        width: 100vw;
        height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #e5e7eb;
        overflow: hidden;
        padding: 20px;
        box-sizing: border-box;
    }

    .resume-scale {
        transform: scale(var(--resume-scale));
        transform-origin: center center;
    }

    .resume-toolbar {
        position: absolute;
        top: 16px;
        left: 16px;
        right: 16px;
        z-index: 50;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        pointer-events: auto;
    }

    .toolbar-btn {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 10px 14px;
        font-size: 14px;
        border-radius: 10px;
        border: 1px solid #e2e8f0;
        background: rgba(255, 255, 255, 0.95);
        color: #0f172a;
        text-decoration: none;
        cursor: pointer;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.08);
        transition: 0.15s;
        backdrop-filter: blur(10px);
    }

    .toolbar-btn:hover {
        background: #fff;
        transform: translateY(-1px);
    }
</style>

<script>
    (function() {
        const HEIGHT = 1122;
        const WIDTH = 794;

        function applyScale() {
            const vh = window.innerHeight - 40;
            const vw = window.innerWidth - 40;

            const scale = Math.min(vh / HEIGHT, vw / WIDTH, 1);
            document.documentElement.style.setProperty('--resume-scale', scale.toString());
        }

        window.addEventListener('resize', applyScale);
        applyScale();
    })();
</script>
