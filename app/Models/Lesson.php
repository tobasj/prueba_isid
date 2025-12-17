<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Lesson extends Model
{
    use SoftDeletes;
    protected $table = 'lessons';
    protected $fillable = ['course_id','title','video_url','order'];

    public function course()
    {
        return $this->belongsTo(Course::class);
    }
}
