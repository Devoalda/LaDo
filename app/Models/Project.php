<?php

namespace App\Models;

use App\Traits\UuidTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Project extends Model
{
    use HasFactory, UuidTrait;

    protected $dateFormat = 'U';

    protected $fillable = [
        'name',
        'description',
    ];

    /**
     * Relationship with Todo model
     * @return BelongsToMany
     */
    public function todos(): belongsToMany
    {
        return $this->belongsToMany(Todo::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(projectUser::class, 'project_user', 'project_id', 'user_id');
    }
}
