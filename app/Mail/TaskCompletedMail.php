<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TaskCompletedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $studentName,
        public string $routineName,
        public int    $tasksCompleted,
        public int    $totalTasks,
        public int    $pointsEarned,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '✅ ' . $this->studentName . " completed today's routine!",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'mail.task-completed',
        );
    }
}
