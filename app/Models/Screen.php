<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $name
 * @property string $table
 * @property string $title
 * @property string $description
 * @property string $icon
 * @property string $url
 * @property string $pk_name
 *
 */
class Screen extends Model
{
    use HasFactory;
    const TABLE = 'screen';
    protected $table = self::TABLE;

    protected $fillable = [
            'name',
            'table',
            'title',
            'icon',
            'description',
            'url'
    ];

    public function fields() {
        return $this->hasMany(ScreenFields::class);
    }
}
