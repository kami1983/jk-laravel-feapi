<?php


namespace KLib\FEApiLaravel;

use KLib\FEApiLaravel\ExceptionFEApi;
use PhpParser\Node\Expr\Array_;

/**
 * CFEErr
 * @author hcl
 */
class CFEErr implements IHaveErr
{

    const CONST_NEWERR_NOT_DEFINED_ERRORCODE = 1809181507;

    // 这个变量用来判断是否有缓存值
    protected static $HasCached = False;
    // 这个变量用来记录某个类的异常值是否存在全局数组直送，也就是某个标识是否调用过他的 DefineErr
    protected static $IsCachedArr = [];
    // 这个变量用来存储所有定义的错误信息数组
    protected static $ErrInfo = array();

    ##################################
    ########   IHaveErr   ############
    ##################################

    /**
     * 清除错误定义信息
     *
    */
    public static function CleanDefineErr () {
        self::$HasCached = false;
        self::$IsCachedArr = [];
        self::$ErrInfo = [];
    }

    /**
     * 用来决定是否应该调用某个错误定义类的 DefineErr
     * 一旦调用异常信息将被缓存到$ErrInfo中
     * @param $errclass CFEErr 一个静态类的名称
     * @param $sigin string 标识名称
    */
    public static function RegisterErr ( $sigin, $calldefined ) {
        if(!array_key_exists($sigin, self::$IsCachedArr)) {
            //如果不等于true 就表示没有缓存
            $calldefined();
            self::$IsCachedArr[$sigin] = true;
        }
    }

    /**
     * 添加异常信息的定义
     * @param $errcode string
     * @param $errmsg string
     * @param $errsign string
     * @return void
     */
    public static function AddException($errsign, $errcode, $errmsg )
    {
//        echo 'RUN 1.1';
        $arrkey = "{$errsign}::{$errcode}";
        if (array_key_exists($errsign, self::$ErrInfo)) {
            // 不能重复添加
//            throw new
        }
        //添加动作会导致，缓存类的变量标记失效，这时候运行的时候也也意味着对应DefineErr方法会触发
        self::$HasCached = false;
        self::$IsCachedArr[$errsign] = false;

        // 添加数组
        self::$ErrInfo[$arrkey] = [
            'code' => $errcode,
            'msg' => $errmsg,
            'sign' => $errsign,
        ];
    }

    /**
     * 获取所有定义的异常列表信息
     * @return array
    */
    public static function GetErrList(){
        if(false == self::$HasCached) {
            // 当前前是否已经调用过Definee
            self::DefineErr();
            self::$HasCached = false;
        }
        return self::$ErrInfo;
    }

    /**
     * 获取某个特定的异常信息
     * @param $sign string 异常的标识信息，一般来讲是类名。
     * @param $codenum string 异常代码编号
     * @return array 格式是 [code => str, msg =>str, sign =>str ]
    */
    public static function GetErrInfo($sign, $codenum) {
        $err_list = self::GetErrList();
        $err_key = "{$sign}::{$codenum}";
        if(!array_key_exists("{$sign}::{$codenum}", $err_list)) {
            self::ThrowErr2(__CLASS__, self::CONST_NEWERR_NOT_DEFINED_ERRORCODE, "{$err_key} 不存在。");
        }
        return $err_list[$err_key];
    }

//    /**
//     * 捕获手册中所有涉及的异常错误处理API
//     * @return void
//     */
//    public function fillManErr(){
//        parent::fillManErr();
//        self::DefineErr(); //本API相关
//    }

    /**
     * 获取错误标识
     * return string
     */
//    public static function GetErrSign(){
//        return self::class;
//    }

    /**
     * 定义错误
     * return string
     */
    public static function DefineErr()
    {

//        CFEErr::Err()->addException(self::CONST_NEWERR_NOT_DEFINED_ERRORCODE, '不存在错误', __CLASS__);
        self::AddException(__CLASS__, self::CONST_NEWERR_NOT_DEFINED_ERRORCODE, '错误码未定，错误码格式是sign::code');
    }

    /**
     * 抛出错误
     * return IFEErrControl
     */
//    public static function ThrowErr($codenum, $attach_msg, $errsign = __CLASS__)
//    {
//
//        $exception = CFEErr::Err()->getException($codenum, $errsign);
//        $exception->setAttachMsg($attach_msg);
//        throw $exception;
//    }

    /**
     * 抛出错误
     * @param $codenum string 异常代码
     * @param $sign string 异常标识
     * @param $attach_msg string 可选的附加值
     * @deprecated 该方法已过期，请用 ThrowErr2 代替，新函数符合 sing,code 的参数排列顺序
     * return void
     */
    public static function ThrowErr($codenum, $sign, $attach_msg){
        self::ThrowErr2($sign, $codenum, $attach_msg);
    }

    /**
     * 抛出错误
     * @param $codenum string 异常代码
     * @param $sign string 异常标识
     * @param $attach_msg string 可选的附加值
     * return void
     */
    public static function ThrowErr2($sign, $codenum, $attach_msg = ''){
        $exception_info = self::GetErrInfo($sign, $codenum);
        $exception_obj = ExceptionFEApi::CreateErr2($exception_info['sign'], $codenum, $exception_info['msg'] );
        $exception_obj->setAttachMsg($attach_msg);
        throw $exception_obj;
    }

}
