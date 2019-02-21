<?php
namespace app\hcie\model;

use think\Db;
use think\Model;

class User extends Model
{

    public function getPrize($step) //大转盘抽奖

    {
        $num = mt_rand(0, 10000) / 10000;

        switch ($step) {
            case '1':
                $id      = 1;
                $arr     = Db::name('goods')->where('id=' . $id)->find();
                $prob    = $arr['prob']; //查看当前奖项中奖概率
                $library = $arr['library']; //查看当前奖项奖品库存
                break;
            case '2':
                $arr     = Db::name('goods')->where('id=2 or id=3')->select();
                $i       = mt_rand(0, 1);
                $library = $arr[$i]['library'];
                $prob    = $arr[$i]['prob'];
                $id      = $arr[$i]['id'];
                break;
            case '3':
                $id      = 4;
                $arr     = Db::name('goods')->where('id=' . $id)->find();
                $prob    = $arr['prob']; //查看当前奖项中奖概率
                $library = $arr['library']; //查看当前奖项奖品库存
                break;
        }

        if ($num < $prob) {
            if ($library > 0) {
                return $id; //中奖,返回中奖id
            } else {
                return 0; //库存为0
            }

        } else {
            return 0;
        }

    }

    public function canPlay($openid)
    {
        $data = Db::name('user')->where('openid="' . $openid . '"')->find();

        if ($data['goodstype'] == '0') {
            return true;
        } else {
            return false;
        }

    }

    public function checkUser($openid)
    {

        $data = Db::name('user')->where('openid="' . $openid . '"')->find(); //查看数据库是否有该用户信息

        if (empty($data)) {
            return true;
        } else {
            return false;
        }

    }

    public function checkLuckUser($openid, $actid = '200')
    {

        $db = Db::connect('', 'luckdraw_', 'mysql://root:Huawei$123#_@117.78.47.100:3306/event#utf8');

        $data = $db->query('select * from luckdraw_user where openid="' . $openid . '" and actid=' . $actid); //查看数据库是否有该用户信息

        var_dump($data);die;

        if (empty($data)) {
            return true;
        } else {
            return false;
        }

    }

}
