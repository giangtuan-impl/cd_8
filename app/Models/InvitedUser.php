<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class InvitedUser extends Model
{
    use SoftDeletes;
    protected $dates = ['deleted_at'];
    protected $table = 'invited_users';
    protected $fillable = [
        'app_id',
        'user_id',
        'tester_id'
    ];

    public function app()
    {
        return $this->belongsTo(Application::class);
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }
}
