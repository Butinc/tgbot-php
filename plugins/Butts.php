<?php

/**
 * 发送巨臀美图
 * User: dray
 * Date: 15/7/10
 * Time: 下午3:43
 */
class Butts extends Base
{
    /**
     * 命令说明
     * Command Description
     * @return string
     */
    public static function desc()
    {
        return array(
            "/butts - Get a butts NSFW image. ",
            '/美臀 - 来一张美臀图(前方高能，请注意体位！',
        );
    }

    /**
     * 命令操作详解
     * Detailed command operation
     * @return array
     */
    public static function usage()
    {
        return array(
            "/butts - Get a butts NSFW image. ",
            "/butts num - Get a lot of butts NSFW image. ",
            '/美臀 - 来一张美臀图(前方高能，请注意体位！',
            '/美臀 num - 来N张美臀图(强撸伤身！',
        );
    }

    /**
     * 插件的路由配置
     * plugin matching rules
     * @return array
     */
    public static function router()
    {
        //匹配的命令
        return array(
            '/butts',
            '/美臀',
        );
    }

    /**
     * 当命令满足的时候，执行的基础执行函数
     */
    public function run()
    {
        Common::echo_log("执行 Butts run");

        //图片数量
        $send_image_num = 1;
        if (count($this->parms) == 2) {
            if (is_numeric($this->parms[1])) {
                $send_image_num = $this->parms[1];
            }
        }

        $url = "http://api.obutts.ru/noise/{$send_image_num}";
        $res = Common::curl($url);

        $res_str = null;
        if (!isset($res) || !isset($res[0]['preview'])) {
            $res_str = 'Cannot get that boobs, trying another one...';
        } else {
            foreach ($res as $v) {
                $res_str = 'http://media.obutts.ru/' . $v['preview'];
                //回复消息
                Telegram::singleton()->send_message(array(
                    'chat_id' => $this->from_id,
                    'text' => $res_str,
                    // 'reply_to_message_id' => $this->msg_id,
                ));
            }
        }

        if (empty($res_str)) {
            $res_str = 'Cannot get that boobs, trying another one...';
            //回复消息
            Telegram::singleton()->send_message(array(
                'chat_id' => $this->from_id,
                'text' => $res_str,
                // 'reply_to_message_id' => $this->msg_id,
            ));
        }
    }
}
