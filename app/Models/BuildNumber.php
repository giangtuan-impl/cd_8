<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BuildNumber extends Model
{
    use SoftDeletes;

    const ENV_ANDROID = 1;
    const ENV_IOS = 0;
    const ENV_ANDROID_EXTENSION = 'apk';
    const ENV_IOS_EXTENSION = 'ipa';
    const NUMBER_OF_OS = 2; # iOS & Android

    const LIST_ENV = [
        self::ENV_ANDROID,
        self::ENV_IOS,
    ];

    const MAX_NEW_VERSION_COUNT = 5;

    protected $table = 'build_numbers';
    protected $date = [
        'created_at',
        'updated_at',
        'deleted_at'
    ];
    protected $fillable = [
        'app_id',
        'build_number',
        'env',
        'build_date',
        'build_number',
        'bundle_id',
        'app_icon',
        'uuid_list',
        'link',
        'version_number',
        'version_code_number',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public function app()
    {
        return $this->belongsTo(Application::class);
    }

    public function getUuidListAttribute($value)
    {
        if (!$value) {
            return [];
        }
        return json_decode($value);
    }

    public function getAppIconAttribute($value)
    {
        return !$value ? asset(config('constants.DEFAULT_IMAGE_APP')) : asset(config('constants.DEFAULT_ICON_FOLDER') . $value);
    }

    public function scopeLatestIosBuild($query)
    {
        return $query->where('env', self::ENV_IOS)->latest('id')->first();
    }

    public function scopeLatestAndroidBuild($query)
    {
        return $query->where('env', self::ENV_ANDROID)->latest('id')->first();
    }

    public function scopeLatestAppIcon($query)
    {
        $collectBuilds = collect([
            $this->where('env', self::ENV_IOS)->latest('id')->first(),
            $this->where('env', self::ENV_ANDROID)->latest('id')->first()
        ]);

        if (count($collectBuilds->whereNotNull('id')) < self::NUMBER_OF_OS) // if exist only ios build or android build
        {
            if ($this->where('env', self::ENV_IOS)->latest('id')->first()) // if exist only ios build
            {
                return $this->scopeLatestIosBuild($query);
            } else {
                return $this->scopeLatestAndroidBuild($query);
            }
        } else    // if exist both ios & android build
        {
            // if same build -> ios is priority
            if (($collectBuilds[0]->build_number == $collectBuilds[1]->build_number) && ($collectBuilds[0]->app_icon != null)) {
                return $this->scopeLatestIosBuild($query);
            } else // if difference build -> get the latest
            {
                return $query->latest('id')->first();
            }
        }
    }

    public function scopeFilterIOSBuildNumbers($query)
    {
        return $query->where('env', self::ENV_IOS)->orderBy('build_date', 'desc');
    }

    public function scopeFilterAndroidBuildNumbers($query)
    {
        return $query->where('env', self::ENV_ANDROID)->orderBy('build_date', 'desc');
    }
}
