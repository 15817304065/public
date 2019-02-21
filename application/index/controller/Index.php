<?php
namespace app\index\controller;

use app\index\model\Api_info;
use think\Controller;

class Index extends Controller
{
    public function index()
    {
        return $this->fetch(':index');
        // return view(':index');
    }

    public function Ocr()
    {

        $base_img = input('post.image');
        $data     = array(
            "image"            => $base_img,
            "url"              => "",
            "detect_direction" => false,
        );

        $obj = new \Util\ei();

        $re = $obj->general_text($data);

        $result = json_decode($re, true);

        var_dump($result);die;

        if (isset($result['result'])) {
            foreach ($result['result']['words_block_list'] as $k => $v) {
                $list_str .= ($k + 1) . '、' . $v['words'] . '</br>';
            }
            $this->ajaxReturn(array('msg' => 'success', 'state' => 1, 'data' => $list_str));
        } else {
            $this->ajaxReturn(array('msg' => 'error', 'state' => 0, 'data' => '网络错误!请稍后再试!'));
        }
    }
    public function test()
    {
        $model = new Api_info;

        $user = $model->getInfoData();
        var_dump($user);die;
        var_dump($model->scopeAge('select * from ei_info'));
    }
}
