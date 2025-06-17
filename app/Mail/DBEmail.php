<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class DBEmail extends Mailable
{
    use Queueable, SerializesModels;

    public string $context;

    /**
     * Create a new message instance.
     */
    public function __construct(string $context = 'default')
    {
        $this->context = $context;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        $databasePath = database_path('database.sqlite'); // Path to your SQLite DB

        return $this->subject('SQLite Database Backup')
            ->view('emails.backupDB') // Your email view
            ->with(['context' => $this->context])
            ->attach($databasePath, [
                'as' => 'backup.sqlite', // Name of the attachment
                'mime' => 'application/x-sqlite3', // Correct MIME type
            ]);
    }
}
