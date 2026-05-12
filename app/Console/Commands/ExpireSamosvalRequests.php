<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\SamosvalRequest;
use Illuminate\Support\Carbon;

class ExpireSamosvalRequests extends Command
{
    protected $signature = 'requests:expire-Samosvals';
    protected $description = 'Обновляет статус просроченных заявок на технику и создаёт новые';

    public function handle()
    {
        $now = Carbon::now();
        $expired = SamosvalRequest::where('status', 0)
            ->where('expires_at', '<=', $now)
            ->get();

        foreach ($expired as $req) {
            $req->update(['status' => 4]);

            SamosvalRequest::create([
                'Samosval_id' => $req->Samosval_id,
                'problem_id' => $req->problem_id, // исправлено
                'solution_id' => null,
                'status' => 0,
                'expires_at' => $now->copy()->addHours(2),
            ]);
        }

        $this->info('Обработано ' . $expired->count() . ' просроченных заявок на технику.');
    }
}
