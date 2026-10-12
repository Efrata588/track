<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\BelongsTo;

class Task extends Model
{
    use HasFactory;#for testing purpose

    protected $fillable=[
        'user_id',
        'title',
        'description',
        'due_at',
        'completed_at',
    ];
    
    protected function casts(): array{
        return [
            'due_at'=>'datetime',
            'completed_at'=>'datetime',
        ];
    }

    public function user(): BelongsTo
    {
      return $this->belongsTo(User::class);
    }
}
