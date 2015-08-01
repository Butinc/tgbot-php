<?php

/**
 * User: dray
 * Date: 15/7/30
 * Time: 下午3:43
 */
class Bot extends Base {

    //目前备选的机器人
    const BOT_TULING123 = 1;
    const BOT_CLEVER    = 2;

    static $BOT_MAP = array(
        self::BOT_TULING123 => 'tuling123',
        self::BOT_CLEVER    => 'cleverbot',
    );

    static function desc() {
        return "/Bot - say to bot...";
    }

    static function usage() {
        return array(
            "/Bot set: set default bot.",
            "/Bot - say to bot...",
        );
    }

    /**
     * 得到当前设置的机器人
     * @param $user_id
     * @return Base|null
     */
    static function get_my_bot($user_id) {
        $bot_id = (int) Db::get_redis()->hGet('bot_index', $user_id);

        if (!isset(self::$BOT_MAP[$bot_id])) {
            return NULL;
        }

        return Process::get_class(self::$BOT_MAP[$bot_id]);
    }

    /**
     * 设置用户的机器人
     * @param $user_id
     * @param $bot_id
     * @return int|null
     */
    static function set_my_bot($user_id, $bot_id) {
        if (!isset(self::$BOT_MAP[$bot_id])) {
            return NULL;
        }

        return Db::get_redis()->hSet('bot_index', $user_id, $bot_id);
    }

    /**
     * 不管什么情况都会执行的函数
     */
    public function pre_process() {
        //如果有调用参数，那么跳过
        if (isset($this->parms[0])) {
            return;
        }

        //如果是私聊，那么机器人接管
        if ($this->chat_id > 0) {
            $bot = self::get_my_bot($this->from_id);
            if ($bot) {
                $bot->text = $this->parm;
                $bot->run();
            }
        }
    }

    /**
     * 有人回复我
     */
    public function msg_reply_me() {
        //群组聊天的时候，开启这个模式，方式跟私聊的冲突
        if ($this->chat_id < 0) {
            $bot = self::get_my_bot($this->from_id);
            if ($bot) {
                $bot->text = $this->parm;
                $bot->run();
            }
        }
    }

    /**
     * 当命令满足的时候，执行的基础执行函数
     */
    public function run() {
        CFun::echo_log("Bot run 执行");

        //如果是需要回掉的请求
        if (empty($this->text)) {
            $this->set_reply();

            return;
        }

        $is_set  = false;
        $set_arr = array('s', 'set');
        $set_flg = false;

        $bot_id = false;

        $parms = array();
        foreach ($this->parms as $k => $v) {
            if ($bot_id = array_search(strtolower($v), self::$BOT_MAP)) {
                continue;
            }

            if (false == $set_flg) {
                if (in_array($v, $set_arr)) {
                    $is_set  = true;
                    $set_flg = true;
                    continue;
                }
            }

            $parms[] = $v;
        }

        if ($is_set && $bot_id) {
            self::set_my_bot($this->from_id, $bot_id);

            //发送
            Telegram::singleton()->send_message(array(
                'chat_id'             => $this->chat_id,
                'text'                => '机器人已经设置好了，亲！',
                'reply_to_message_id' => $this->msg_id,
            ));
        } else {
            $bot = self::get_my_bot($this->from_id);
            if ($bot) {
                //调用机器人
                $bot->text = $this->parm;
                $bot->run();
            } else {
                //发送
                Telegram::singleton()->send_message(array(
                    'chat_id'             => $this->chat_id,
                    'text'                => '请选择你要使用的机器人！',
                    'reply_to_message_id' => $this->msg_id,
                    'reply_markup'        => array(
                        'keyboard'          => array(
                            self::$BOT_MAP,
                        ),
                        'resize_keyboard'   => true,
                        'one_time_keyboard' => true,
                        'selective'         => true,
                    ),
                ));
            }
        }
    }

}
