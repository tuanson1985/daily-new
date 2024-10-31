<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;


class Setting extends BaseModel
{

    protected $table = 'settings';

    protected $guarded = [];


    protected $attributes = [
        'locale' => 'vn',
    ];



    public static function add($key, $val, $type = 'string')
    {
        if ( self::has($key) ) {
            return self::set($key, $val, $type);
        }
        $shop_id = session('shop_id');
        return self::create(['shop_id' => $shop_id, 'name' => $key, 'val' => $val, 'type' => $type]) ? $val : false;
    }

    public static function addAll($key, $val,$shop_id, $type = 'string')
    {
        if ( self::hasAll($key,$shop_id) ) {
            return self::setAll($key, $val,$shop_id, $type);
        }

        return self::create(['shop_id' => $shop_id, 'name' => $key, 'val' => $val, 'type' => $type]) ? $val : false;
    }

    /**
     * Get a settings value
     *
     * @param $key
     * @param null $default
     * @return bool|int|mixed
     */
    public static function get($key, $default = null)
    {
        $shop_id = session('shop_id');
        if ( self::has($key) ) {
            $setting = self::getAllSettingsShopId($shop_id)->where('name', $key)->first();
            return self::castValue(isset($setting->val) ? $setting->val : null, isset($setting->type) ? $setting->type : null);
        }
        return self::getDefaultValue($key, $default);
    }
    public static function getSettingShop($key, $default = null, $shop_id = null)
    {
        $setting = self::getAllSettingsShopId($shop_id)->where('name', $key)->first();
        return self::castValue(isset($setting->val) ? $setting->val : null, isset($setting->type) ? $setting->type : null);
    }

    /**
     * Set a value for setting
     *
     * @param $key
     * @param $val
     * @param string $type
     * @return bool
     */
    public static function set($key, $val, $type = 'string')
    {
        $shop_id = session('shop_id');
        if ( $setting = self::getAllSettingsShopId($shop_id)->where('name', $key)->first() ) {
            return $setting->update([
                'name' => $key,
                'val' => $val,
                'type' => $type]) ? $val : false;
        }

        return self::add($key, $val, $type);
    }

    public static function setAll($key, $val,$shop_id, $type = 'string')
    {

        if ( $setting = self::getAllSettingsShopId($shop_id)->where('name', $key)->first() ) {
            return $setting->update([
                'name' => $key,
                'shop_id' => $shop_id,
                'val' => $val,
                'type' => $type]) ? $val : false;
        }

        return self::addAll($key, $val,$shop_id, $type);
    }

    /**
     * Remove a setting
     *
     * @param $key
     * @return bool
     */
    public static function remove($key)
    {
        $shop_id = session('shop_id');
        if( self::has($key) ) {
            return self::whereName($key)->where('shop_id',$shop_id)->delete();
        }

        return false;
    }

    /**
     * Check if setting exists
     *
     * @param $key
     * @return bool
     */
    public static function has($key)
    {
        $shop_id = session('shop_id');
        return (boolean) self::getAllSettingsShopId($shop_id)->whereStrict('name', $key)->count();
    }

    public static function hasAll($key,$shop_id)
    {
        return (boolean) self::getAllSettingsShopId($shop_id)->whereStrict('name', $key)->count();
    }


    /**
     * Get the validation rules for setting fields
     *
     * @return array
     */
    public static function getValidationRules()
    {
        return self::getDefinedSettingFields()->pluck('rules', 'name')
            ->reject(function ($val) {
                return is_null($val);
            })->toArray();
    }

    /**
     * Get the data type of a setting
     *
     * @param $field
     * @return mixed
     */
    public static function getDataType($field)
    {
        $type  = self::getDefinedSettingFields()
            ->pluck('data', 'name')
            ->get($field);

        return is_null($type) ? 'string' : $type;
    }


    public static function getInputType($field)
    {
        $type  = self::getDefinedSettingFields()
            ->pluck('type', 'name')
            ->get($field);

        return is_null($type) ? null : $type;
    }


    /**
     * Get default value for a setting
     *
     * @param $field
     * @return mixed
     */
    public static function getDefaultValueForField($field)
    {
        return self::getDefinedSettingFields()
            ->pluck('value', 'name')
            ->get($field);
    }

    /**
     * Get default value from config if no value passed
     *
     * @param $key
     * @param $default
     * @return mixed
     */
    private static function getDefaultValue($key, $default)
    {
        return is_null($default) ? self::getDefaultValueForField($key) : $default;
    }

    /**
     * Get all the settings fields from config
     *
     * @return Collection
     */
    private static function getDefinedSettingFields()
    {
        return collect(config('setting_fields'))->pluck('elements')->flatten(1);
    }

    /**
     * caste value into respective type
     *
     * @param $val
     * @param $castTo
     * @return bool|int
     */
    private static function castValue($val, $castTo)
    {
        switch ($castTo) {
            case 'int':
            case 'integer':
                return intval($val);
                break;

            case 'bool':
            case 'boolean':
                return boolval($val);
                break;

            default:
                return $val;
        }
    }

    /**
     * Get all the settings
     *
     * @return mixed
     */
    public static function getAllSettings()
    {
        return Cache::rememberForever('settings.all', function() {
            return self::all();
        });
    }
    public static function getAllSettingsShopId($shop_id){
        return Cache::rememberForever('settings.shop_'.$shop_id, function() use ($shop_id) {
            return self::where('shop_id',$shop_id)->select('id','shop_id','name','val','type','desc','locale')->get();
        });
    }
    public static function flushCache()
    {
        Cache::forget('settings.all');
    }
    public static function flushCacheShopId($shop_id)
    {
        Cache::forget('settings.shop_'.$shop_id);
    }

    public static function boot()
    {
        parent::boot();


        static::saving(function ($model) {
            $model->locale = app()->getLocale();
            $model->shop_id = session('shop_id');
        });
        static::updated(function () {
            $shop_id = session('shop_id');
            if($shop_id){
                self::flushCacheShopId($shop_id);
            }
            else{
                self::flushCache();
            }
        });

        static::created(function() {
            $shop_id = session('shop_id');
            if($shop_id){
                self::flushCacheShopId($shop_id);
            }
            else{
                self::flushCache();
            }
            self::flushCache();
        });

//        static::deleting(function($model) {
//            $model->language_key()->sync([]);
//            return true;
//        });
    }




}
