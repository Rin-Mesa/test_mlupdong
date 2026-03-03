<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Table extends Model
{
    use HasFactory;

    // 'tables' is a reserved word in MySQL — use a safe name
    protected $table = 'restaurant_tables';

    protected $fillable = [
        'number',
        'qr_url',
        'qr_token',
        'qr_code_path',
    ];

    protected $appends = ['qr_code_url'];

    public function getQrCodeUrlAttribute()
    {
        return $this->qr_code_path ? asset($this->qr_code_path) : null;
    }

    public function activeOrder()
    {
        return $this->hasOne(Order::class)->where('status', '!=', 'Served');
    }
}