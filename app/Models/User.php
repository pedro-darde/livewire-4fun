<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Traits\HasDynamicTable;
use App\Traits\WithDynamicTable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements WithDynamicTable
{
    use HasApiTokens, HasFactory, Notifiable, HasDynamicTable;

    const TABLE = 'users';
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    static protected $fillableWithDefinitions = [
        'id' => [
            "columnDescription" => "ID",
            "columnRules" => "",
            'allowEdit' => false
        ],
        'name' => [
            "columnDescription" => "Nome",
            "columnRules" => "required|string"
        ],
        'email' => [
            "columnDescription" => "E-mail",
            "columnRules" => "required|email|unique:users,email"
        ]
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public static $useRecordActions = true;

    protected $primaryKey = "id";

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        static::$fillableWithDefinitions = [
            'id' => [
                "columnDescription" => "ID",
                "columnRules" => "",
                'allowEdit' => false
            ],
            'name' => [
                "columnDescription" => "Nome",
                "columnRules" => "required|string"
            ],
            'email' => [
                "columnDescription" => "E-mail",
                "columnRules" => function (?User $register)  {
                    $rule = 'required|email|unique:users,email';
                    if ($register?->{$this->primaryKey}) $rule .= ", " . $register?->{$this->primaryKey};
                    return $rule;
                }
            ]
        ];
    }
}
