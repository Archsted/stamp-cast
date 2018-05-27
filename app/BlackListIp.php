<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class BlackListIp extends Model
{
    protected $fillable = [
        'user_id', 'ip'
    ];

    protected $appends = [
        'created_display',
        'masked_ip',
    ];

    public function getCreatedDisplayAttribute()
    {
        $createdDt = Carbon::parse($this->attributes['created_at']);

        $date = $createdDt->format('Y/m/d');
        $time = $createdDt->format('H:i:s');
        $day = $createdDt->format('N'); // ISO-8601 形式  1（月曜日）から 7（日曜日）

        $days = [
            1 => '月',
            2 => '火',
            3 => '水',
            4 => '木',
            5 => '金',
            6 => '土',
            7 => '日',
        ];

        return $date . '(' . $days[$day] . ') ' . $time;
    }

    public function getMaskedIpAttribute()
    {
        $ip = $this->attributes['ip'];

        $ips = explode('.', $ip);

        return '***.***.***.' . $ips[count($ips) - 1];
    }
}
