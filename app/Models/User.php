<?php

namespace App\Models;

use App\Models\Application;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;

class User extends Authenticatable
{
    use Notifiable,
        SoftDeletes,
        HasFactory;

    const ROLE_ADMIN = 0;
    const ROLE_MEMBER = 1;
    const ROLES = [
        'admin' => self::ROLE_ADMIN,
        'member' => self::ROLE_MEMBER
    ];

    const LANGUAGE_JP = 'jp';
    const LANGUAGE_EN = 'en';
    const LANGUAGES = [
        '日本語' => self::LANGUAGE_JP,
        'English' => self::LANGUAGE_EN
    ];

    const MUST_CHANGE_PASSWORD = 1;
    const MUST_NOT_CHANGE_PASSWORD = 0;

    const RESET_PASSWORD_TOKEN_EXPIRED_HOUR = 2;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'role',
        'email',
        'password',
        'avatar',
        'language',
        'is_must_change_password'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function application()
    {
        return $this->hasMany(Application::class);
    }

    public function applications()
    {
        return $this->belongsToMany(Application::class, 'invited_users', 'tester_id', 'app_id');
    }

    public function getAvatarAttribute($value)
    {
        $value = $value ? config('constants.DEFAULT_IMAGE_FOLDER') . $value : config('constants.DEFAULT_IMAGE');

        return url($value);
    }

    public function scopeAnotherUser($query)
    {
        $userLogged = auth()->user();
        return $query->where('id', '!=', $userLogged->id);
    }

    public function isAdmin()
    {
        $userLogged = auth()->user();
        return $userLogged->role == self::ROLE_ADMIN ? true : false;
    }

    public function setPasswordAttribute($value)
    {
        $this->attributes['password'] = Hash::make($value);
    }
}
