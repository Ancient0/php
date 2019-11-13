<?php
if (!function_exists('get_ip')) {
    function get_ip()
    {
        static $ip = false;
        if ($ip !== false) return $ip;
        foreach (array('HTTP_CLIENT_IP', 'HTTP_INCAP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_FORWARDED', 'HTTP_FORWARDED_FOR', 'HTTP_FORWARDED', 'REMOTE_ADDR') as $aah) {
            if (!isset($_SERVER[$aah])) continue;
            $cur2ip = $_SERVER[$aah];
            $curip = explode('.', $cur2ip);
            if (count($curip) !== 4) break; // If they've sent at least one invalid IP, break out
            foreach ($curip as $sup)
                if (($sup = intval($sup)) < 0 or $sup > 255)
                    break 2;
            $curip_bin = $curip[0] << 24 | $curip[1] << 16 | $curip[2] << 8 | $curip[3];
            foreach (array(
                         //hexadecimal ip  ip mask
                         array(0x7F000001, 0xFFFF0000), // 127.0.*.*
                         array(0x0A000000, 0xFFFF0000), // 10.0.*.*
                         array(0xC0A80000, 0xFFFF0000), // 192.168.*.*
                     ) as $ipmask) {
                if (($curip_bin & $ipmask[1]) === ($ipmask[0] & $ipmask[1])) break 2;
            }
            return $ip = $cur2ip;
        }
        return $ip = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : "";
    }
}


/**
 * 写日志
 * @param String/Array $params 要记录的数据
 * @param String $file 文件名.
 * @param String $dir 文件夹.
 * @param Int $fsize 文件大小M为单位.默认为1M
 * @param bool $only 只写一个文件
 * @return null
 */
if (!function_exists('writeDebug')) {
    function writeDebug($params, $filename = 'debug', $dir = false, $fsize = 1, $only = false)
    {
        is_scalar($params) or ($params = var_export($params, true)); //是简单数据
        if (!$params) {
            return false;
        }
        clearstatcache();

        $dir = $dir ? dirname(__DIR__) . '/temp/log/' . $dir : dirname(__DIR__) . '/temp/log/';
        if (!is_dir($dir)) {

            @mkdir($dir, 0777, true); //创建文件夹
            @chmod($dir, 0777);
        }
        if (!$only) {

            $date = date('Ymd');
            $file = $dir . DIRECTORY_SEPARATOR . $filename . '.log';
            $size = file_exists($file) ? @filesize($file) : 0;
            $flag = $size < max(1024, $fsize) * 1024 * 1024; //标志是否附加文件.文件控制在1024M大小
            if (!$flag) {
                rename($file, $dir . DIRECTORY_SEPARATOR . $filename . $date . '-' . time() . ".log");
            }
        } else {
            $flag = true;
            $file = $dir . $filename . '.log';
            $size = file_exists($file) ? @filesize($file) : 0;
        }
        $prefix = '';
        ($size == 0) && $prefix = <<<EOD
＃LOG \n
EOD;
        @file_put_contents($file, $prefix . $params . "\n", $flag ? FILE_APPEND : null);
    }
}

/**
 * 格式文件大小单位
 * @param $filesize
 * @return float|int|string
 */
function sizecount($filesize)
{//转换filesize单位
    if ($filesize >= 1073741824) {
        $filesize = round($filesize / 1073741824 * 100) / 100;
        $filesize = $filesize . "gb";
    } elseif ($filesize >= 1048576) {
        $filesize = round($filesize / 1048576 * 100) / 100;
        $filesize = $filesize . "mb";
    } else {
        $filesize = round($filesize / 1024 * 100) / 100;
        $filesize = $filesize . "kb";
    }
    return $filesize;
}

if ( ! function_exists('getChar'))
{
    /**
     * @param $len
     * @return array
     * 获取excel字母列
     */
    function getChar($len) {
        $st = 65; //A
        $charArr = array();
        for ($i = 0; $i <= $len-1; $i++) {
            $charArr[] = chr($st+$i);
        }
        return $charArr;
    }
}