<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\SamosvalRequest;
use Carbon\Carbon;

class CheckExpiredRequests extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:check-expired-requests';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check and update expired Samosval requests';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $now = Carbon::now();

        $expired = SamosvalRequest::where('status', SamosvalRequest::STATUS_OPEN)
            ->where('expires_at', '<=', $now)
            ->get();

        foreach ($expired as $request) {
            $request->update(['status' => SamosvalRequest::STATUS_WAITING_PARTS]);

            $newRequest = $request->replicate();
            $newRequest->status = SamosvalRequest::STATUS_OPEN;
            $newRequest->solution_id = null;
            $newRequest->expires_at = now()->addHours(SamosvalRequest::EXPIRES_IN_HOURS);
            $newRequest->created_at = now();
            $newRequest->updated_at = now();
            $newRequest->save();
        }

        $this->info('Expired check done. Count: ' . $expired->count());

        return 0;
    }
}
