<?php

namespace App\Models;

use App\Traits\UuidTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Pomo extends Model
{
    use HasFactory, UuidTrait;

    protected $dateFormat = 'U';
    protected $table = 'pomos';

    protected $fillable = [
        'todo_id',
        'notes',
        'pomo_start',
        'pomo_end',
    ];

    public function todo(): BelongsTo
    {
        return $this->belongsTo(Todo::class);
    }

}
