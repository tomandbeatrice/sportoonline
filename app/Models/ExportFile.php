<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class ExportFile extends Model
{
    use HasFactory;

    protected $fillable = [
        'filename',
        'filetype',
        'filesize',
        'user_id',
        'status',
        'exported_at',
    ];

    protected $dates = ['exported_at'];

    /**
     * İlişkiler
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * İndirilebilir URL accessor
     */
    public function getDownloadUrlAttribute()
    {
        return Storage::url('exports/' . $this->filename);
    }

    /**
     * Dosya boyutunu okunabilir formatta döndür
     */
    public function getReadableSizeAttribute()
    {
        return number_format($this->filesize / 1024, 2) . ' KB';
    }

    /**
     * Export zamanını formatla
     */
    public function getFormattedExportedAtAttribute()
    {
        return $this->exported_at ? $this->exported_at->format('Y-m-d H:i') : '-';
    }

    /**
     * Dosya mevcut mu?
     */
    public function getExistsInStorageAttribute()
    {
        return Storage::exists('exports/' . $this->filename);
    }
}