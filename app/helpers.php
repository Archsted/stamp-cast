<?php

if (!function_exists('room_path')) {

    /**
     * スタンプルームのデフォルトパスを返す。
     * タグが指定されているURLや、エイリアスURLが指定された場合も対応
     *
     * @return string
     */
    function room_path()
    {
        $paths = explode('/', $_SERVER["REQUEST_URI"]);

        return '/' . $paths[1];
    }
}
