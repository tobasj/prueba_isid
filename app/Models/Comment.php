<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Comment extends Model
{
    use SoftDeletes;
    protected $table = 'comments';
    protected $fillable = ['user_id','course_id','text','rating'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    private function calcRating(Course $course): void
    {
        $stats = $course->comments()->selectRaw('COALESCE(AVG(rating),0) as avg, COUNT(*) as count')->first();
        $course->update(['average_rating' => round($stats->avg, 2), 'ratings_count' => $stats->count,]);
    }
}
