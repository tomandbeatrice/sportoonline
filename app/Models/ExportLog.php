<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExportLog extends Model
{
    protected $table = 'export_logs';

    protected $fillable = [
        'admin_id',
        'vendor_id',
        'vendor_name',
        'user_email',
        'action',
        'file_name',
        'ip',
        'created_at',
    ];

    public $timestamps = false; // çünkü sadece created_at var
}