<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\UuidTrait;

class Todo extends Model
{
    use HasFactory, UuidTrait;

    protected $dateFormat = 'U';

    protected $fillable = [
        'title',
        'description',
        'due_start',
        'due_end',
        'user_id',
        'completed_at',
    ];

    protected $casts = [
        // Unix timestamp for due_start, due_end, completed_at, created_at, updated_at
        "due_start" => "integer",
        "due_end" => "integer",
        "completed_at" => "integer",
        "created_at" => "integer",
        "updated_at" => "integer",
    ];
}
