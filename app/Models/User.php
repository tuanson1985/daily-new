<?php

namespace App\Models;

use App\Notifications\MailResetPasswordToken;
use App\Traits\Metable;
use Carbon\Carbon;
use DateTime;
use DateTimeInterface;
use DB;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Spatie\Permission\Traits\HasPermissions;
use App\Library\Helpers;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Support\Facades\Log;

class User extends Authenticatable implements JWTSubject
{
    use HasFactory, Notifiable;
    use HasRoles;
    use Metable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

    protected $casts = [
        'buygem_discount' => 'object',
        'ninjaxu_discount' => 'object',
        'nrocoin_discount' => 'object',
    ];

    protected $fillable = [
        'username',
        'shop_id',
        'account_type',
        'email',
        'email_verified_at',
        'password',
        'password2',
        'is_change_password2',
        'google2fa_secret',
        'google2fa_enable',
        'balance',
        'balance_in',
        'balance_out',
        'balance_in_refund',
        'image',
        'cover',
        'security_money',
        'lastchangepass_at',
        'type',
        'phone',
        'birtday',
        'gender',
        'address',
        'status',
        'verify_code',
        'verify_code_expired_at',
        'is_verify',
        'odp_code',
        'odp_expired_at',
        'odp_active',
        'odp_fail',
        'provider_id',
        'shop_access',
        'shop_expect',
        'last_add_balance',
        'last_minus_balance',
        'is_agency_card',
        'is_agency_charge',
        'lastlogin_at',
        'lastlogout_at',
        'created_by',
        'created_at',
        'ruby_num1',
        'ruby_num2',
        'ruby_num3',
        'ruby_num4',
        'ruby_num5',
        'ruby_num6',
        'ruby_num7',
        'ruby_num8',
        'ruby_num9',
        'ruby_num10',
        'balance_lock',
        'free_wheel',
        'free_wheel_type',
        'ip_allow',
        'client_show',
        'bonus_gift',
        'type_information',
        'payment_limit',
        'partner_key_service',
        'is_agency_buygem',
        'active_api_buy_nrogem',
        'is_agency_nrocoin',
        'active_api_buy_nrocoin',
        'is_agency_ninjaxu',
        'active_api_buy_ninjaxu',
        'two_factor_recovery_codes',
        'type_information_ctv',
        'required_login_gmail'
    ];

    protected $meta_field = [
        'avatar',
        'cover',
        'follower',
        'booking_quantity',
        'booking_complete_rate',
        'camera',
        'voice',
        'mic',
        'game_play',
        'album_image',
        'album_video',
        'album_timeline',
        'is_online',
    ];


    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'password2',
        'remember_token',
        'refresh_token',
        'token',
        'exp_token_refresh',
    ];

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }



    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $dates = [
        'email_verified_at' => 'datetime',
    ];


    public function checkBalanceValid()
    {
        try{

            $access_user = \Request::header('access_user');
            if(isset($access_user)){
                $data_access_user = Helpers::Decrypt($access_user,config('module.user.encryt'));
                if($data_access_user == ""){
//                    $message_access_user = "Thành viên: <b>".$this->username."</b> - <b>".$this->shop->domain."</b> có dấu hiệu giao dịch của qtv khi đăng nhập vào tài khoản, vui lòng kiểm tra.";
                    $message_access_user = "QTV có dấu hiệu giao dịch trên tài khoản thành viên: <b>".$this->username."</b> - <b>".$this->shop->domain."</b>  vui lòng kiểm tra.";
                }
                else{
                    $data_access_user = explode(',',$data_access_user);
                    $message_access_user = "QTV: <b>".$data_access_user[0]."</b> giao dịch trên tài khoản thành viên - <b>".$this->username."</b> - <b>".$this->shop->domain."</b>, vui lòng kiểm tra.";

                }
                Helpers::TelegramNotify($message_access_user,config('telegram.bots.mybot.channel_noty_access_user'));
            }
            if($this->balance<0){
                return false;
            }
            if ($this->balance_in - $this->balance_out + $this->balance_in_refund  - $this->balance == 0) {
                return true;
            } else {
                $text_tele = "Cảnh báo: Thành viên ".$this->username." - của shop ".$this->shop->domain." biến động số dư bị chênh lệch, vui lòng kiểm tra lại. Số tiền vào: ".number_format($this->balance_in).". - Số tiền chi tiêu: ".number_format($this->balance_out).". - Số tiền hoàn: ".number_format($this->balance_out).". - Số dư hiện tại: ".number_format($this->balance_in_refund).". - Chênh lệch: ".number_format($this->balance_in - $this->balance_out + $this->balance_in_refund - $this->balance)." VNĐ";
                Helpers::TelegramNotify($text_tele,config('telegram.bots.mybot.channel_notify_check_balance_user'));
                return false;
            }
        }
        catch(\Exception $e){
            Log::error($e);
            return null;
        }
    }


    //send mail recover password
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new MailResetPasswordToken($token));
    }

    //hash Google2faSecret
    public function setGoogle2faSecretAttribute($value)
    {
        $this->attributes['google2fa_secret'] = encrypt($value);
    }

    public function getGoogle2faSecretAttribute($value)
    {
        if ($value == "") {
            return "";
        }
        return decrypt($value);
    }


    public function setCreatedAtAttribute($value)
    {

        if ($this->verifyDate($value, 'd/m/Y H:i:s')) {
            $this->attributes['created_at'] = Carbon::createFromFormat('d/m/Y H:i:s', $value);;
        } else {
            $this->attributes['created_at'] = Carbon::now();
        }
    }

    function verifyDate($value, $format)
    {
        return (DateTime::createFromFormat($format, $value) !== false);
    }

    public function firstTxn()
    {
        return $this->hasOne(Txns::class)->orderBy('created_at', 'desc');
    }
    public function txns()
    {
        return $this->hasMany(Txns::class);
    }
    //nhớ thêm select('id','domain') để bảo mật key nạp thẻ và mua thẻ
    public function shop(){
        return $this->belongsTo(Shop::class)->select('id','domain');
    }

    public function access_categories(){
        return $this->belongsToMany(Group::class, 'game_access', 'user_id', 'group_id')->withPivot(['ratio', 'active']);
    }

    public function access_shops(){
        return $this->belongsToMany(Shop::class, 'shop_access', 'user_id', 'shop_id');
    }

    public function access_shop_groups(){
        return $this->belongsToMany(Shop_Group::class, 'user_shop_group_access', 'user_id', 'group_id');
    }

    public function service_access() {
        return $this->hasOne(ServiceAccess::class,'user_id')->where('module','user');
    }

    public static function boot()
    {
        parent::boot();
        //set default auto add  scope to query
        // static::addGlobalScope('global_scope', function (Builder $model) {
        //     $model->where('users.shop_id', session('shop_id')??1);
        // });
        // static::saving(function ($model) {
        //     $model->shop_id = session('shop_id')??1;
        // });

        // static::creating(function ($model) {
        //     $model->url_display =  md5("P@ZZ".$model->email);
        // });

    }

    //1. Check balance valid
    //2. Check số tiền giao dịch  nhở hơn số dự tài khoản
    //3. Check số tiền giao dịch nhỏ <0
    //4. Check giao money limit < số tiền cài đặt giới hạn ( nếu có)
    public function getJWTIdentifier() {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims() {
        return [];
    }

}
