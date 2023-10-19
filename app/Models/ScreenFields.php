<?php

namespace App\Models;

use App\Utils\ArrU;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class ScreenFields extends Model
{
    use HasFactory;

    const TABLE = 'screen_fields';
    protected $table = self::TABLE;

    protected $appends = [
        'config_parsed',
        'name',
        'column_name',
        'description',
        'type',
        'rules',
        'label',
        'hasRelation'
    ];
    protected $fillable = [
        'screen_id',
        'config'
    ];

    public function screen()
    {
        return $this->belongsTo(Screen::class);
    }

    public function getNameAttribute()
    {
        return $this->configparsed->label . " ({$this->configParsed->name}) ";
    }

    public function getColumnNameAttribute()
    {
        return Str::snake($this->configParsed->name);
    }

    public function getDescriptionAttribute()
    {
        return $this->configparsed->description;
    }

    public function getTypeAttribute()
    {
        return $this->configparsed->type;
    }

    public function getConfigParsedAttribute()
    {
        return json_decode($this->config);
    }

    public function getRulesAttribute()
    {
        return $this->configparsed->rules;
    }

    public function getLabelAttribute()
    {
        return $this->configparsed->label;
    }

    public function getHasRelationAttribute()
    {
        return isset($this->config_parsed->relations)
            && ArrU::every($this->config_parsed->relations, function ($relation) {
                return isset($relation->field->id) && isset($relation->screen->id);
            });
    }
}
