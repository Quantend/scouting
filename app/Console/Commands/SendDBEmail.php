<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use App\Mail\DBEmail;

class SendDBEmail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'email:send-db';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send a copy of the SQLite database via email';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $recipient = 'dylansbackups@gmail.com';

        Mail::to($recipient)->send(new DBEmail());

        $this->info("SQLite database backup sent to $recipient.");
    }
}
