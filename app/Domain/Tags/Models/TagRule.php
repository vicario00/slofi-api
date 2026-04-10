<?php

namespace App\Domain\Tags\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TagRule extends Model
{
    protected $fillable = ['tag_id', 'field', 'operator', 'value', 'priority'];

    protected $casts = [
        'priority' => 'integer',
    ];

    public function tag(): BelongsTo
    {
        return $this->belongsTo(Tag::class);
    }

    /**
     * Scope: load all rules for a user, ordered by priority descending.
     */
    public function scopeForUser($query, int $userId)
    {
        return $query->whereHas('tag', fn ($q) => $q->where('user_id', $userId))
            ->orderByDesc('priority');
    }
}
