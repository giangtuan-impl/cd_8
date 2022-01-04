<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Application extends Model
{
    use SoftDeletes;

    const ENV_ANDROID = 1;
    const ENV_IOS = 0;

    protected $date = ['deleted_at'];
    protected $table = 'applications';
    protected $fillable = [
        'app_name',
        'ios_name',
        'android_name',
        'user_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function members()
    {
        return $this->belongsToMany(User::class, 'invited_users', 'app_id', 'tester_id');
    }

    public function buildNumbers()
    {
        return $this->hasMany(BuildNumber::class, 'app_id');
    }
}
