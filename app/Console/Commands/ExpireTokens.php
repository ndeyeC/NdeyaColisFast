<?php

namespace App\Console\Commands;

use App\Models\TokenTransaction;
use Carbon\Carbon;
use Illuminate\Console\Command;

class ExpireTokens extends Command
{
    protected $signature = 'tokens:expire';
    protected $description = 'Marque les jetons expirés comme utilisés';

    public function handle()
    {
        TokenTransaction::where('type', TokenTransaction::TYPE_PURCHASE)
                       ->where('status', TokenTransaction::STATUS_COMPLETED)
                       ->where('amount', '>', 0)
                       ->where('expiry_date', '<', Carbon::now())
                       ->update(['amount' => 0]);

        $this->info('Jetons expirés mis à jour avec succès.');
    }
}