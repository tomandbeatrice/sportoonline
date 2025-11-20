<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InvoiceExportLog extends Model
{
    protected $fillable = [
        'vendor_slug',
        'filename',
        'download_token',
        'admin_name',
        'created_at'
    ];

    public $timestamps = false;
}