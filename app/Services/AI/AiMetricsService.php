<?php

namespace App\Services\AI;

use App\Models\AiUsageLog;
use App\Models\Conversation;
use Illuminate\Support\Facades\DB;

class AiMetricsService
{
    /**
     * Get aggregate production AI usage and performance metrics.
     *
     * @return array
     */
    public function getMetrics(): array
    {
        $totalRequests = AiUsageLog::count();
        $failedRequests = AiUsageLog::where('status', 'failed')->count();
        $successRequests = $totalRequests - $failedRequests;

        $failureRate = $totalRequests > 0 ? round(($failedRequests / $totalRequests) * 100, 2) : 0.0;

        $avgResponseTime = (float) (AiUsageLog::avg('response_time_ms') ?? 0);
        $totalInputTokens = (int) AiUsageLog::sum('input_tokens');
        $totalOutputTokens = (int) AiUsageLog::sum('output_tokens');
        $totalTokens = (int) AiUsageLog::sum('total_tokens');

        $totalEstimatedCost = (float) (AiUsageLog::sum('estimated_cost') ?? 0);

        $totalEscalations = Conversation::where('mode', 'human')->count();

        return [
            'total_requests'     => $totalRequests,
            'successful_requests'=> $successRequests,
            'failed_requests'    => $failedRequests,
            'failure_rate_pct'   => $failureRate,
            'avg_response_time'  => round($avgResponseTime, 2),
            'input_tokens'       => $totalInputTokens,
            'output_tokens'      => $totalOutputTokens,
            'total_tokens'       => $totalTokens,
            'total_estimated_cost' => round($totalEstimatedCost, 6),
            'total_escalations'  => $totalEscalations,
        ];
    }
}
