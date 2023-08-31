<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ScreenFields extends Model
{
    use HasFactory;
    const TABLE = 'screen_fields';
    protected $table = self::TABLE;
    protected $fillable = [
        'screen_id',
        'config'
    ];
    public function screen() {
        return $this->belongsTo(Screen::class);
    }
}
