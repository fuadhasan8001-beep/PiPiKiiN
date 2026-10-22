<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Room extends Model
{
    use HasFactory;

    protected $fillable = [
        'code', 'quiz_id', 'host_id', 'status', 'max_players',
        'allow_guest_join', 'current_question_index',
        'started_at', 'finished_at', 'settings',
    ];

    protected $casts = [
        'allow_guest_join' => 'boolean',
        'started_at' => 'datetime',
        'finished_at' => 'datetime',
        'settings' => 'array',
    ];

    public function quiz(): BelongsTo
    {
        return $this->belongsTo(Quiz::class);
    }

    public function host(): BelongsTo
    {
        return $this->belongsTo(User::class, 'host_id');
    }

    public function participants(): HasMany
    {
        return $this->hasMany(RoomParticipant::class);
    }
}
