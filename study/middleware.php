<?php
/**
 * Created by PhpStorm.
 * User: think
 * Date: 31/07/2017
 * Time: 10:30 PM
 */
interface Middleware
{
    public static function handle(Closure $next);
}
class VerifyCsrfToken implements Middleware
{
    public static function handle(Closure $next)
    {
        echo "验证 Csrf-Token"."<br>";
        $next();
    }
}
class ShareErrorsFromSession implements Middleware
{
    public static function handle(Closure $next)
    {
        echo "如果session中有'errors'变量，则共享它".'<br>';
        $next();
    }
}
class StartSession implements Middleware
{
    public static function handle(Closure $next)
    {
        echo "开启session，获取数据".'<br>';
        $next();
        echo "保存数据，关闭session".'<br>';
    }
}
class AddQueuedCookiesToResponse implements Middleware
{
    public static function handle(Closure $next)
    {
        $next();
        echo "添加下一次请求需要的cookie".'<br>';
    }
}
class EncryptCookies implements Middleware
{
    public static function handle(Closure $next)
    {
        echo "对输入请求的cookies进行解密".'<br>';
        $next();
        echo "对输出响应的cookies进行加密".'<br>';
    }
}
class CheckForMaintenanceMode implements Middleware
{
    public static function handle(Closure $next)
    {
        echo "确定当前程序是否处于维护状态".'<br>';
        $next();
    }
}
function getSlice()
{
    return function($stack, $pipe)
    {
        return function() use ($stack, $pipe)
        {
            return $pipe::handle($stack);
        };
    };
}
function then()
{
    $pipes = [
        "CheckForMaintenanceMode",
        "EncryptCookies",
        "AddQueuedCookiesToResponse",
        "StartSession",
        "ShareErrorsFromSession",
        "VerifyCsrfToken"
    ];
    $firstSlice = function() {
        echo "请求路由器传递，返回响应".'<br>';
    };
    $pipes = array_reverse($pipes);
    call_user_func(
        array_reduce($pipes,getSlice(),$firstSlice)
    );

}
then();