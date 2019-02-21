<?php

namespace app\index\model;

use think\Db;
use think\Model;

class Api_info extends Model
{
    protected $connection    = 'db1';
    protected $resultSetType = 'collection';
    public function scopeThinkphp($query)
    {
        return $query->where('name', 'thinkphp')->field('id,name');
    }

    public function scopeAge($query)
    {
        return $query->where('age', '>', 20)->limit(10);
    }
    public function sel()
    {
        $result    = $this->where('id', '>', 20)->select();
        return $re = $result->toArray();
    }
    public function tt1()
    {
        $userQuery = Db::table("ei_errorcode_pc");
        $re        = $userQuery->where('id<6 and date_time>20181210')->fetchSql()->select();
        return $re;
    }
    public function getInfoData()
    {
        $data['api_name']  = "content";
        $data['error_msg'] = "test";
        return Db::table('ei_errorcode_pc')->fetchSql(true)->delete(2);
        // ->update([
        //     'date_time' => ['exp', 'now()'],
        //     'num'       => ['exp', 'num+1'],
        // ]);
    }

}
