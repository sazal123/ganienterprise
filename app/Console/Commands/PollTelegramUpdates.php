<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\Telegram\TelegramService;
use App\Http\Controllers\Api\TelegramWebhookController;
use Illuminate\Http\Request;

class PollTelegramUpdates extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'telegram:poll {--once : Process pending updates once and exit}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Poll Telegram API for updates and process admin replies locally without needing a public domain or ngrok.';

    /**
     * Execute the console command.
     */
    public function handle(TelegramService $telegramService, TelegramWebhookController $controller)
    {
        $this->info('🤖 Starting Telegram Local Dev Polling Service...');

        // Delete Webhook to enable getUpdates polling
        $delRes = $telegramService->deleteWebhook();
        if (empty($delRes['ok'])) {
            $this->error('Failed to clear webhook: ' . json_encode($delRes));
            return 1;
        }

        $this->info('✓ Webhook cleared. Ready to poll incoming Telegram updates!');
        $this->info('Press Ctrl+C to stop.');

        $offset = 0;
        $once   = $this->option('once');

        while (true) {
            try {
                $response = $telegramService->getUpdates($offset, 100, 2);

                if (!empty($response['ok']) && !empty($response['result'])) {
                    foreach ($response['result'] as $update) {
                        $updateId = $update['update_id'];
                        $offset   = $updateId + 1;

                        $this->line("📩 Processing Update #{$updateId}...");

                        // Dispatch update to TelegramWebhookController locally
                        $request = Request::create('/api/telegram/webhook', 'POST', [], [], [], [], json_encode($update));
                        $request->headers->set('Content-Type', 'application/json');

                        $res = $controller->handle($request);
                        $this->info("   Result: " . $res->getContent());
                    }
                }
            } catch (\Throwable $e) {
                $this->error('Polling error: ' . $e->getMessage());
                sleep(2);
            }

            if ($once) {
                break;
            }

            usleep(500000); // 0.5s pause between polls
        }

        $this->info('Polling stopped.');
        return 0;
    }
}
