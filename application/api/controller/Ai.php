<?php
namespace app\api\controller;

use think\Controller;
use think\Db;

class Ai extends Controller
{

    public function _initialize()
    {
        header('Access-Control-Allow-Origin:*');

    }
    //创建人脸库
    public function face_sets()
    {
        $data = array(
            "face_set_name"     => "2_19_test_face_set",
            "face_set_capacity" => 10000,
            "external_fields"   => array(
                "gender" => array("type" => "string")，
                "famous" => array("type" => "integer"),
            ),
        );
        $obj = new \util\hwcloud();

        $re = $obj->face_sets($data)；
        var_dump($re);
    }
    //搜索人脸
    public function face_search()
    {
        if (!empty($_FILES['file'])) {
            //通过uploadfile上传的临时文件
            if (!in_array($_FILES['file']['type'], array(
                'image/jpg',
                'image/jpeg',
                'image/png',
                'image/bmp',
            ))) {
                apiResponse("0", "图像格式不支持!");
            }
            $baseimg = base64_encode(file_get_contents($_FILES['file']['tmp_name']));

            $data = array(
                "image_base64"  => $baseimg,
                "return_fields" => array(
                    "gender", "userid",
                ),
            );
        } else if (!empty(input('post.'))) {
            $p = input('post.');
            if (!empty($p['image_base64'])) {
                $data = array(
                    "image_base64"  => $p['image_base64'],
                    "return_fields" => array(
                        "gender", "userid",
                    ),
                );
            } else {
                apiResponse("0", "参数有误");
            }
        } else {

            apiResponse("0", "参数有误");
        }

        $obj = new \util\hwcloud();

        $re = $obj->face_search($data);

        $result = json_decode($re, true);

        if (isset($result['error_code'])) {
            apiResponse("0", $result['error_msg'], $result['error_code']);
        } else {
            apiResponse("1", "success", "200", $result);
        }
    }
    //添加人脸
    public function face_add()
    {

        header('Access-Control-Allow-Origin:*');
        if (!empty($_FILES['file']) && !empty(I('post.gender'))) {
            //通过uploadfile上传的临时文件
            if (!in_array($_FILES['file']['type'], array(
                'image/jpg',
                'image/jpeg',
                'image/png',
                'image/bmp',
            ))) {
                apiResponse("0", "图像格式不支持!");
            }
            $baseimg = base64_encode(file_get_contents($_FILES['file']['tmp_name']));

            $data = array(
                "image_base64"    => $baseimg,
                "external_fields" => array(
                    'sex'    => I('post.gender') == "male" ? "male" : "female",
                    'famous' => I('post.famous') ? I('post.famous') : "",
                ),
            );
        } else if (!empty(input('post.')) && !empty(I('post.gender'))) {
            $p = input('post.');
            if (!empty($p['image_base64'])) {
                $data = array(
                    "image_base64"    => $p['image_base64'],
                    "external_fields" => array(
                        'gender' => I('post.gender') == "male" ? "male" : "female",
                    ),
                );
            } else {
                apiResponse("0", "参数有误");
            }
        } else {
            apiResponse("0", "参数有误");
        }

        $obj = new \util\hwcloud();

        $re = $obj->face_add($data, "1_18_famous_face_set");

        $result = json_decode($re, true);

        if (isset($result['error_code'])) {

            apiResponse("0", $result['error_msg'], $result['error_code']);
        } else {
            apiResponse("1", "success", "200", $result);

        }
    }
    // //查询人脸
    public function face_select()
    {
        $obj = new \util\hwcloud();
        if (!empty(input('post.'))) {
            $p = input('post.');
            if (!empty($p['face_id'])) {
                $type   = false;
                $params = $p['face_id'];
                $re     = $obj->face_select($params, 0, false);
            } else if (!empty($p['offset'])) {
                $type   = true;
                $params = $p['offset'];
                $limit  = $p['limit'] ? $p['limit'] : 0;
                $re     = $obj->face_select($params, $limit);
            } else {
                apiResponse("0", "参数有误");
            }
        } else {
            apiResponse("0", "参数有误");
        }

        $result = json_decode($re, true);

        // var_dump($result);die;

        if (isset($result['error_code'])) {
            apiResponse("0", $result['error_msg'], $result['error_code']);
        } else {
            apiResponse("1", "success", "200", $result);

        }
    }

//人脸检测
    public function face_detect()
    {
        header('Access-Control-Allow-Origin:*');
        debug('begin'); // 记录开始标记位
        if (!empty($_FILES['file'])) {
            //通过uploadfile上传的临时文件
            if (!in_array($_FILES['file']['type'], array(
                'image/jpg',
                'image/jpeg',
                'image/png',
                'image/bmp',
            ))) {
                apiResponse("0", "图像格式不支持!");
            }
            $baseimg = base64_encode(file_get_contents($_FILES['file']['tmp_name']));

            $data = array(
                "image_base64" => $baseimg,
                "attributes"   => "0,1,2,4,5",
            );
        } else if (!empty(input('post.'))) {
            $p = input('post.');
            if (!empty($p['image_base64'])) {
                $data = array(
                    "image_base64" => $p['image_base64'],
                    "attributes"   => "0,1,2,4,5",
                );
            } else if (!empty($p['url'])) {

                $data = array(
                    "image_url"  => $p['url'],
                    "attributes" => "0,1,2,4,5",
                );
            } else {
                apiResponse("0", "参数有误");
            }
        } else {
            apiResponse("0", "参数有误");
        }

        $obj = new \util\hwcloud();

        $re = $obj->face_detect($data);

        $result = json_decode($re, true);

        debug('end'); // 记录结束标签位
        $miro = debug('begin', 'end', 6); // 统计区间运行时间 精确到小数后6位

        if (isset($result['error_code'])) {
            $this->save_log($miro, 0, "face_detect");
            apiResponse("0", $result['error_msg'], $result['error_code']);
        } else {
            $this->save_log($miro, 1, "face_detect");
            apiResponse("1", "success", "200", $result);
        }
    }

    public function face_deal2()
    {
        header('Access-Control-Allow-Origin:*');
        debug('begin'); // 记录开始标记位
        if (!empty($_FILES['file'])) {
            //通过uploadfile上传的临时文件
            if (!in_array($_FILES['file']['type'], array(
                'image/jpg',
                'image/jpeg',
                'image/png',
                'image/bmp',
            ))) {
                apiResponse("0", "图像格式不支持!");
            }
            $baseimg = base64_encode(file_get_contents($_FILES['file']['tmp_name']));

            $data = array(
                "image_base64" => $baseimg,
                "attributes"   => "0,1,2,4,5",
            );
        } else if (!empty(input('post.'))) {
            $p = input('post.');
            if (!empty($p['image_base64'])) {
                $data = array(
                    "image_base64" => $p['image_base64'],
                    "attributes"   => "0,1,2,4,5",
                );
            } else if (!empty($p['url'])) {

                $data = array(
                    "image_url"  => $p['url'],
                    "attributes" => "0,1,2,4,5",
                );
            } else {
                apiResponse("0", "参数有误");
            }
        } else {
            apiResponse("0", "参数有误");
        }

        $obj = new \util\hwcloud();

        $re = $obj->face_detect($data);

        $result = json_decode($re, true);

        debug('end'); // 记录结束标签位
        $miro = debug('begin', 'end', 6); // 统计区间运行时间 精确到小数后6位

        if (isset($result['error_code'])) {
            $this->save_log($miro, 0, "face_deal");
            apiResponse("0", $result['error_msg'], $result['error_code']);
        } else {
            $data2 = array(
                "image_base64"  => $baseimg,
                "threshold"     => 0.93,
                "top_n"         => 1,
                "return_fields" => array(
                    "sex",
                ),
            );
            $res     = $obj->face_search($data2, "1_18_attendee_face_set");
            $result2 = json_decode($res, true);
            if (count($result2['faces']) == 0) {
                $result['faces'][0]['attributes']['gender'] = "";
                apiResponse("1", "success", "200", $result);
            } else {
                $male = $result2['faces'][0]['external_fields']['sex'];
                if ($male != $result['faces'][0]['attributes']['gender']) {
                    $result['faces'][0]['attributes']['gender'] = "";
                    apiResponse("1", "success", "200", $result);
                } else {
                    $data3 = array(
                        "image_base64"  => $baseimg,
                        // "threshold"     => 0.03,
                        "top_n"         => 1,
                        "return_fields" => array(
                            "name", "sex",
                        ),
                        "filter"        => "sex:" . $male,
                    );
                    $res     = $obj->face_search($data3, "1_18_famous_face_set");
                    $result3 = json_decode($res, true);

                    if (count($result3['faces']) != 0) {
                        $name                                           = $this->unicodeDecode($result3['faces'][0]['external_fields']['name']);
                        $result['faces'][0]['attributes']['famous']     = $name;
                        $result['faces'][0]['attributes']['similarity'] = $result3['faces'][0]['similarity'];
                    }
                    apiResponse("1", "success", "200", $result);
                }
            }
        }
    }

    public function face_deal()
    {
        header('Access-Control-Allow-Origin:*');
        debug('begin'); // 记录开始标记位
        if (!empty($_FILES['file'])) {
            //通过uploadfile上传的临时文件
            if (!in_array($_FILES['file']['type'], array(
                'image/jpg',
                'image/jpeg',
                'image/png',
                'image/bmp',
            ))) {
                apiResponse("0", "图像格式不支持!");
            }
            $baseimg = base64_encode(file_get_contents($_FILES['file']['tmp_name']));

            $data = array(
                "image_base64" => $baseimg,
                "attributes"   => "0,1,2,4,5",
            );
        } else if (!empty(input('post.'))) {
            $p = input('post.');
            if (!empty($p['image_base64'])) {
                $data = array(
                    "image_base64" => $p['image_base64'],
                    "attributes"   => "0,1,2,4,5",
                );
            } else {
                apiResponse("0", "参数有误");
            }
        } else {
            apiResponse("0", "参数有误");
        }

        $obj = new \util\hwcloud();

        $re = $obj->face_detect($data); //第一步：人脸检测

        $result = json_decode($re, true);

        debug('end'); // 记录结束标签位
        $miro = debug('begin', 'end', 6); // 统计区间运行时间 精确到小数后6位

        if (empty($result['faces'])) {
            $this->save_log($miro, 0, $this->return_img_url($data['image_base64']), "未检测到人脸");
            apiResponse("0", "未检测到人脸", "FRS.0501");
        } else {
            $glasses = $result['faces'][0]['attributes']['dress']['glass']; //眼镜
            $hat     = $result['faces'][0]['attributes']['dress']['hat']; //帽子
            $gender  = $result['faces'][0]['attributes']['gender']; //性别

            $data2 = array(
                "image_base64"  => $data['image_base64'],
                "threshold"     => 0.6,
                "top_n"         => 1,
                "return_fields" => array(
                    "sex", "famous",
                ),
            );
            $res     = $obj->face_search($data2, "1_19_attendee_face_set"); //第二步：人脸搜索
            $result2 = json_decode($res, true);

            $tag_id = 0;
            if (empty($result2['faces'])) {
                //无结果
                $tag_id = $this->set_tag($glasses, $hat, $gender);
                $this->save_log($miro, $tag_id, $this->return_img_url($data['image_base64']), $res);

                $data_add = array(
                    "image_base64"    => $data['image_base64'],
                    "external_fields" => array(
                        "timestamp":12,
                        "id":"home",
                    ),
                    apiResponse("1", "success", "200", $tag_id);
                } else {
                    $gender = $result2['faces'][0]['external_fields']['sex'];
                    $famous = $this->unicodeDecode($result2['faces'][0]['external_fields']['famous']);
                    if ($famous) {
                        $tag    = $this->famous_tag()[$famous];
                        $tag_id = $this->getKey($tag);
                        $this->save_log($miro, $tag_id, $this->return_img_url($data['image_base64']), $res);
                        apiResponse("1", "success", "200", $tag_id);
                    }
                    $tag_id = $this->set_tag($glasses, $hat, $gender);
                    $this->save_log($miro, $tag_id, $this->return_img_url($data['image_base64']), $res);
                    apiResponse("1", "success", "200", $tag_id);

                }
            }
        }

        private function set_tag($glasses, $hat, $gender)
    {
            $tags = $this->getTag();

            if ($glasses == "yes" && $hat == "yes") {
                $tag = array_merge($tags['glasses'], $tags['hat']);
            } else if ($glasses != "none") {
                $tag = $tags['glasses'];
            } else if ($hat != "none") {
                $tag = $tags['hat'];
            } else {
                $tag = $tags['no_feature'];
            }
            if ($gender == "") {
                $gender == "neutral";
            }

            $return_tag = [];
            foreach ($tag as $k => $v) {
                if ($v['sex'] == $gender) {
                    $return_tag = $v['tags'];
                    break;
                }
            }
            $tag_str = array_rand($return_tag);

            // var_dump($return_tag[$tag_str], $this->getKey($return_tag[$tag_str]));

            return $this->getKey($return_tag[$tag_str]);
            // return $return_tag[$tag_str];

        }

        public function return_img_url($base64)
    {
            $url      = "";
            $img_name = uniqid();
            $date     = date('Ymd');
            $path     = "face_img/{$date}/";
            $new_file = $path . "{$img_name}.jpg";

            is_dir($path) || mkdir($path, 0777, true);
            if (base64_encode(base64_decode($base64))) {
                $result = file_put_contents($new_file, base64_decode($base64));
            } else {
                $result = file_put_contents($new_file, $base64);
            }
            if ($result) {
                $http_type = ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https')) ? 'https://' : 'http://';
                $url       = $http_type . $_SERVER['HTTP_HOST'] . __ROOT__ . "/" . $new_file;

            }
            return $url;

        }

        public function famous_tag()
    {
            $arr = [
                '尹子维'             => '轮廓分明如尹子维',
                '冯绍峰'             => '冯绍峰般剑眉电眼',
                '快手阿修'            => '现实版快手阿修',
                '陈数'              => '眼眉轻扬似陈数',
                '宁财神'             => '宁财神般的铜铃明眼',
                '吴昕'              => '甜美可人似吴昕',
                '朱丹'              => '优雅笑容似朱丹',
                'Capital America' => '颜值秒杀美国队长',
                'Tom Cruise'      => '不老男神Tom Cruise',
                'Will Smith'      => '中国版Will Smith',
                '安以轩'             => '精致五官似安以轩',
                '陈柏霖'             => '陈柏霖般阳光帅气',
                '陈赫'              => '笑容灿烂胜陈赫',
                '陈慧娴'             => '似陈慧娴般白净端庄',
                '陈建斌'             => '眼神睿智似陈建斌',
                '陈坤'              => '风度翩翩胜陈坤',
                '陈小艺'             => '翻版陈小艺',
                '陈妍希'             => '颜值在线似陈妍希',
                '董骠'              => '曲眉丰颊似董骠',
                '董浩'              => '模样清秀像董浩',
                '杜海涛'             => '华为杜海涛',
                '姆巴佩'             => '中国姆巴佩',
                '樊少皇'             => '似樊少皇般浓眉大眼',
                '冯巩'              => '转行ICT的冯巩',
                '郭蔼明'             => '颜值媲美郭蔼明',
                '哈林'              => '神采奕奕如哈林',
                '憨豆'              => '亚洲憨豆先生',
                '洪金宝'             => '撞脸洪金宝',
                '胡歌'              => '眉清目秀似胡歌',
                '黄贯中'             => '眉眼神态似黄贯中',
                '黄家驹'             => '迷人魅力黄家驹',
                '黄霑'              => '面善侠客似黄霑',
                '霍金'              => '眉眼深邃似霍金',
                '贾静雯'             => '冻龄女神贾静雯',
                '金城武'             => 'Get到金城武的颜值',
                '克林顿'             => '外貌英俊似克林顿',
                '李开复'             => '如李开复般笑容可掬',
                '李立群'             => '撞脸李立群',
                '李思思'             => '李思思般眉目如画',
                '李维嘉'             => '鼻梁高挺如李维嘉',
                '李小龙'             => '眉眼俊俏似李小龙',
                '李泽楷'             => '文质彬彬李泽楷',
                '林保怡'             => '嘴角散发熟男魅力似林保怡',
                '林忆莲'             => '林忆莲的失散姐妹',
                '刘德华'             => '刘德华般轮廓分明',
                '刘涛'              => '冰肌玉骨似刘涛',
                '六小龄童'            => '目光机灵若六小龄童',
                '龙清泉'             => '被ICT耽误的举重手龙清泉',
                '卢广仲'             => '外貌酷似卢广仲',
                '马景涛'             => '相貌堂堂似马景涛',
                '欧阳振华'            => '慈眉善目似欧阳振华',
                '潘石屹'             => '似潘石屹般目光坚定',
                '胖虎'              => '现实版胖虎',
                '钱钟书'             => '当代钱钟书',
                '任达华'             => '任达华般和蔼可亲',
                '撒贝宁'             => '撒贝宁般阳光帅气',
                '尚格云顿'            => '华为尚格云顿',
                '施瓦辛格'            => 'ICT领域的施瓦辛格',
                '宋丹丹'             => '宋丹丹般的爽朗笑容',
                '宋美龄'             => '眼眉轻扬宋美龄',
                '孙红雷'             => '孙红雷般简单帅',
                '汤镇业'             => '轮廓分明似汤镇业',
                '陶大宇'             => '师奶杀手陶大宇',
                '佟大为'             => '玉面小生佟大为',
                '王宝强'             => '喜眉笑眼王宝强',
                '王健林'             => '眉似远山如王健林',
                '王力宏'             => '王力宏般迷人电眼',
                '王珞丹'             => '王珞丹般美目流盼',
                '王自健'             => '王自健般眉眼俊俏',
                '吴倩莲'             => '吴倩莲般清新丽人',
                '小沈阳'             => '剑眉薄唇如小沈阳',
                '谢依霖'             => '眉语目笑谢依霖',
                '徐静蕾'             => '如徐静蕾般杏脸桃腮',
                '杨坤'              => '成熟魅力似杨坤',
                '杨威'              => '通讯业的杨威',
                '杨钰莹'             => '颜值不输杨钰莹',
                '姚明'              => '眯眼微笑似姚明',
                '俞敏洪'             => '似俞敏洪般神采奕奕',
                '庾澄庆'             => '外貌神似哈林',
                '喻恩泰'             => '似喻恩泰般温文如玉',
                '岳云鹏'             => '小岳岳本人',
                '张飞'              => '豹头环眼似张飞',
                '张嘉译'             => '浓眉大眼酷似张嘉译',
                '张晋'              => '张晋般棱角分明',
                '张绍刚'             => '容光焕发胜张绍刚',
                '张小龙'             => '张小龙般清新俊逸',
                '张一鸣'             => '走失的张一鸣',
                '张怡宁'             => '英气爽朗似张怡宁',
                '张震'              => '鼻梁高挺如张震',
                '郑伊健'             => '帅气赶超郑伊健',
                '钟丽缇'             => '钟丽缇般眸清似水',
                '周鸿祎'             => '清秀更胜周鸿祎',
                '周杰伦'             => '魅力天王周杰伦',
                '邹兆龙'             => '邹兆龙般目若朗星',
                '华少'              => '外形俊朗赛华少',
                '贾玲'              => '贾玲般的爽朗笑容',
                '张靓颖'             => '素颜完胜张靓颖',
                '范玮琪'             => '青春仙女范似范玮琪',
            ];
            return $arr;
        }

        public function getKey($name = "") //通过标签名字获取对应的标签下标

        {
            $ss  = 0;
            $arr = $this->tag();
            foreach ($arr as $k => $v) {
                if ($v == $name) {
                    $ss = $k + 1;
                    break;
                }
            }
            if (in_array($ss, [118, 125, 156, 159, 211])) {
                $ss = mt_rand(166, 178);
            }
            return $ss;
        }

        public function test()
    {
            $ss = 118;
            if (in_array($ss, [118, 125, 156, 159, 211])) {
                $ss = mt_rand(120, 125);
            }
            var_dump($ss);
        }

        public function tag() //一维标签

        {
            $tags = $this->getTag();

            foreach ($tags as $key => $value) {
                foreach ($value as $k => $v) {
                    foreach ($v['tags'] as $a => $b) {
                        $tag[] = $b;
                    }
                }
            }
            $famous     = $this->famous_tag();
            $famous_tag = array_values($famous);
            $tag        = array_merge($tag, $famous_tag);

            return $tag;
        }

        private function getTag() //标签组

        {
            $tags = array(
                'glasses'    => array(
                    array('sex' => 'male', 'tags' => array('文质彬彬真君子', '人形百科全书', '爱奋斗的工作狂', '上进又上镜', '看得准，做的稳', '慧眼如炬，不随大流', '博学才子', '严谨参谋家', '饱读诗书，满腹经纶', '眉清目秀斯文范', '世事洞明学问家', '精益求精细节控', '新观点洞察家'),
                    ),
                    array('sex' => 'female', 'tags' => array('品位精致小姐姐', '眉眼如画俏佳人', '心细如发洞察家', '办公室灵感女王', '才华满到溢出来', '资深御宅', '挡不住的电力魅眼', '滴水不漏细节控', '秀外慧中的才女', '饱读诗书的少女', '文艺鉴赏家', '明眸善睐小姐姐'),
                    ),
                    array('sex' => 'neutral', 'tags' => array('我司最强大脑', '精益求精的智者', '公司风云人物', '眼光不凡的精英', '商业领军人物', '学富五车杰出代表', '满肚子的干货', '百里挑一的精英', '火眼晶晶洞察家', '脚踏实地实干家', '运筹帷幄互联网', '见多识广思想家', '团队意见领袖', '腹有诗书气自华', '杰出商业精英', '有条不紊指挥家', '见识卓越时代精英'),
                    ),

                ),
                'hat'        => array(
                    array('sex' => 'male', 'tags' => array('潮流先锋', '帅就一个字', '谦逊的公认绅士', '斯文儒雅气质佳', '可靠绅士范', '时尚潮流引领者', '享受生活旅行家', '低调奢华有内涵', '天生衣架子', '颜值即正义'),
                    ),
                    array('sex' => 'female', 'tags' => array('穿搭模范', '典雅女神范', '‘帽’美如花，人见人夸', '日常自拍像街拍', '头顶自带女神光环', '天生造型师', '潮流模特', '百变女王', '时尚宠儿', '杂志封面女神'),
                    ),
                    array('sex' => 'neutral', 'tags' => array('全场最佳造型', '人不可‘帽’相', '全司仰慕对象', '时尚元素收割机', '全身都是闪光点', '公司万人迷', '颜值扛把子', '眼光独到，气质非凡', '时尚达人', '自带美颜特效'),
                    ),

                ),
                'no_feature' => array(
                    array('sex' => 'male', 'tags' => array('万人迷男神', '三亿少女的梦', '每天起床被帅醒', '可盐可甜小鲜肉', '惊艳四座的美男子', '24K纯帅男神', '少女心收割机', '颜控福利', '谦谦君子', '器宇轩昂，举世无双', '雪中送炭大暖男', '容颜不老少年郎', '颜值360度无死角', '魅力型熟男', '柔情硬汉', '迷弟成群的大神', '社会主义有为青年', '有求必应好好先生', '帅气阳光大男孩', '呼风唤雨将军范', '柔情电眼男神', '眉眼如画少年郎', '仪表堂堂，绅士风范', '风流倜傥高富帅', '温文尔雅君子风', '玉面郎君', '淑人君子', '品貌非凡完美型男', '行走的荷尔蒙'),
                    ),
                    array('sex' => 'female', 'tags' => array('天生丽质美人骨', '惊艳了岁月', '高岭之花', '天生女神范', '人淡如菊心似水', '古典美女', '橘子味的少女', '笑容比糖还要甜', '治愈系女神', '万人迷女神', '女王驾到', '貌美如花，也能养家', '美若天仙', '西施转世', '甜美胜志玲', '美得动人心弦', '美人在骨不在皮', '纯天然美女', '多情才女', '人群里的小仙女', '出水芙蓉，与众不同', '眉如春水，目似凝黛', '琼鼻杏眼，肌肤胜雪', '办公室里的开心果', '众生皆苦你独甜', '静若处子动若脱兔', '蕙质兰心气质佳', '清新丽人', '才貌双佳', '面若灿桃俏佳人', '微微一笑很倾城', '低调的宝藏女孩', '谪仙般的人儿', '灵秀俏佳人', '女中英豪'),
                    ),
                    array('sex' => 'neutral', 'tags' => array('全能选手', '硬核大牛', '英明果敢，独当一面', '天生领导范', '团队扛把子', '孜孜不倦的劳模', '越挫越勇圣斗士', '情商智商双爆表', '人形搜索引擎', '异想天开脑洞大', '平易近人，厚德载物', 'PPT小达人', '危机救星', '年终奖拿到手软', '披荆斩棘带队者', '勇往直前拓路者', '才高八斗顶梁柱', '逆天完美人设', '理想型另一半', '仗义的老铁', '艰苦奋斗，百折不挠', '德高望重好领导', '三头六臂，身兼数职', '经验丰富好领导', '专注领域的实力派', '公司传奇人物', '乐于助人，有求必应', '国之栋才，企业支柱', '吃不胖的模特身材', '办公室的小太阳', '万里挑一优秀人才', '一丝不苟完美型', '温暖鸡汤供应商', '乘风破浪的舵手', '永远奋斗在前线', '商海浪潮掌舵人', '未来世界领路人', '雄韬伟略灵魂人物', '关键节奏leader', '跨界全能多面手', '终身学习的好榜样', '叱咤商云大佬'),
                    ),
                ),
            );
            return $tags;

        }
        public function get_tags()
    {

            echo json_encode(array('tags' => $this->tag()));
        }

        private function unicodeDecode($encoded_str)
    {
            $byte_str_array = split("0x", $encoded_str);
            $str            = '';
            unset($byte_str_array[0]);
            foreach ($byte_str_array as $k => $v) {
                $str .= chr("0x" . $v);
            }
            return $str;
        }

        private function save_log($stime, $state, $name, $text = "")
    {
            $data['str']        = sp_random_string();
            $data['stime']      = $stime;
            $data['createtime'] = date('Y-m-d H:i:s');
            $data['state']      = $state;
            $data['name']       = $name;
            $data['texts']      = $text;

            return Db::table('event_face')->insert($data);

        }

    }
