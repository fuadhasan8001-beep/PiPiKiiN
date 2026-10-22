<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Question extends Model
{
    use HasFactory;

    protected $fillable = [
        'quiz_id', 'subject_id', 'owner_id', 'text', 'type',
        'correct_answer', 'explanation', 'hint', 'difficulty',
        'time_limit', 'points', 'image_path', 'image_url',
        'is_published', 'is_archived', 'sort_order', 'options',
    ];

    protected $casts = [
        'is_published' => 'boolean',
        'is_archived' => 'boolean',
        'options' => 'array',
        'time_limit' => 'integer',
        'points' => 'integer',
    ];

    public function quiz(): BelongsTo
    {
        return $this->belongsTo(Quiz::class);
    }

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }
}
