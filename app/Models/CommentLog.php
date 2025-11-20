<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CommentLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'comment_id',
        'action',
        'performed_by',
        'performed_at',
    ];

    public function comment()
    {
        return $this->belongsTo(Comment::class);
    }
}