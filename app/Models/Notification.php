<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder;

/**
 *@method static Builder where(...$params)
 */
class Notification extends Model
{
    use HasFactory;

    protected $guarded = ["id"];
    protected $table = 'notification';
}
