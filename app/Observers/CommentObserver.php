<?php

namespace App\Observers;

use App\Models\Comment;

class CommentObserver
{
    public function created(Comment $comment): void
    {
        $this->rating($comment);
    }

    public function updated(Comment $comment): void
    {
        $this->rating($comment);
    }

    public function deleted(Comment $comment): void
    {
        $this->rating($comment);
    }

    public function restored(Comment $comment): void
    {
        $this->rating($comment);
    }

    protected function rating(Comment $comment): void
    {
        $course = $comment->course()->first();
        if (!$course) {
            return;
        }

        $stats = $course->comments()->selectRaw('COALESCE(AVG(rating),0) as avg, COUNT(*) as count')->first();

        $course->update(['average_rating' => round($stats->avg, 2), 'ratings_count' => $stats->count,]);
    }

}
