<?php

/**
 * 萌娘回路
 * http://im.nekonazo.com/wiki/%E9%A6%96%E9%A1%B5
 * User: dray
 * Date: 15/7/27
 * Time: 下午7:01
 */
class Nekonazo extends Base
{
    /**
     * 命令说明
     * Command Description
     * @return string
     */
    public static function desc()
    {
        return array(
            "/neko - talk with nekonazo. http://wiki.oekaki.so/",
            "/萌娘 - 跟萌娘回路聊天.",
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
            "/neko - talk with nekonazo. http://wiki.oekaki.so/",
            "/萌娘 - 跟萌娘回路聊天.",
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
            // '/neko',
            // '/萌娘',
        );
    }

    /**
     * 当命令满足的时候，执行的基础执行函数
     */
    public function run()
    {
        Common::echo_log("执行 Nekonazo run");

    }
}
