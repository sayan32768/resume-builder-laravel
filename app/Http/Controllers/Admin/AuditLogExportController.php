<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use Carbon\Carbon;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AuditLogExportController extends Controller
{
    public function exportCsv(): StreamedResponse
    {
        // ✅ Name includes timestamp for uniqueness
        $fileName = 'audit-logs-' . now()->timezone('Asia/Kolkata')->format('Y-m-d_H-i') . '.csv';

        return response()->streamDownload(function () {

            /**
             * ✅ Streaming exports must be stable even for 100k+ rows
             * - user may close browser
             * - export may run longer
             * - buffers may corrupt CSV output
             */
            ignore_user_abort(true);
            set_time_limit(0);

            /**
             * ✅ Clear all output buffers
             * If any whitespace/HTML exists in output buffers,
             * CSV gets corrupted + browser download fails.
             */
            while (ob_get_level() > 0) {
                ob_end_clean();
            }

            $out = fopen('php://output', 'w');

            /**
             * ✅ OPTIONAL: If you want Excel friendly UTF-8 support,
             * write BOM so Excel doesn't break special characters.
             */
            // fprintf($out, chr(0xEF) . chr(0xBB) . chr(0xBF));

            // ✅ HEADER section
            fputcsv($out, ['AUDIT LOGS REPORT']);
            fputcsv($out, ['Generated at', now()->toDateTimeString()]);
            fputcsv($out, []);


            /**
             * ✅ Summary block
             */
            $totalLogs = AuditLog::count();
            $uniqueActors = AuditLog::whereNotNull('actor_id')->distinct('actor_id')->count('actor_id');
            $uniqueActions = AuditLog::whereNotNull('action')->distinct('action')->count('action');

            fputcsv($out, ['Summary']);
            fputcsv($out, ['Total Logs', $totalLogs]);
            fputcsv($out, ['Unique Actors', $uniqueActors]);
            fputcsv($out, ['Unique Actions', $uniqueActions]);
            fputcsv($out, []);


            /**
             * ✅ Column headers
             *
             * Keep headers stable for CSV import usage.
             * Include:
             * - primary identifiers
             * - actor info
             * - target info
             * - request info
             * - JSON blocks as string
             */
            fputcsv($out, ['Audit Logs']);
            fputcsv($out, [
                'Log ID',
                'Action',

                'Actor ID',
                'Actor Name',
                'Actor Email',

                'Target Type',
                'Target ID',

                // 'IP',
                'User Agent',

                'Before (JSON)',
                'After (JSON)',
                'Meta (JSON)',

                'Created At',
            ]);

            /**
             * ✅ Export logs in chunks
             * Use chunkById for memory safety.
             *
             * NOTE:
             * - chunkById requires ordering by id
             * - ensures stable chunk pagination even if new logs insert while exporting
             */
            AuditLog::query()
                ->with('actor:id,fullName,email')   // keep it minimal, avoid heavy models
                ->select([
                    'id',
                    'actor_id',
                    'action',
                    'target_type',
                    'target_id',
                    'meta',
                    'before',
                    'after',
                    // 'ip',
                    'user_agent',
                    'created_at',
                ])
                ->orderBy('id') // required for chunkById
                ->chunkById(500, function ($logs) use ($out) {

                    foreach ($logs as $log) {

                        /**
                         * ✅ Convert JSON arrays into JSON strings
                         * - ensures CSV has single cell text
                         * - avoids "Array to string conversion"
                         */
                        $before = !empty($log->before)
                            ? json_encode($log->before, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)
                            : '';

                        $after = !empty($log->after)
                            ? json_encode($log->after, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)
                            : '';

                        $meta = !empty($log->meta)
                            ? json_encode($log->meta, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)
                            : '';

                        /**
                         * ✅ CSV row
                         * Use optional() to avoid fatal errors
                         */
                        fputcsv($out, [
                            $log->id,
                            $log->action ?? '',

                            $log->actor_id ?? '',
                            $log->actor?->fullName ?? '',
                            $log->actor?->email ?? '',

                            $log->target_type ?? '',
                            $log->target_id ?? '',

                            // $log->ip ?? '',
                            $log->user_agent ?? '',

                            $before,
                            $after,
                            $meta,

                            optional($log->created_at)->toDateTimeString(),
                        ]);
                    }
                });

            fclose($out);
        }, $fileName, [
            /**
             * ✅ Headers to ensure browser does not cache stale CSV
             * and treats it as a file download.
             */
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Cache-Control' => 'no-store, no-cache, must-revalidate',
            'Pragma' => 'no-cache',
        ]);
    }
}
