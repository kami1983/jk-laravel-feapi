<?php

namespace KLib\FEApiLaravel;

/**
 * 错误类接口
 * @author hcl
 */
interface IHaveErr {

//    /**
//     * 捕获手册中所有涉及的异常错误处理API,实际上是调用自己的 DefineErr 方法
//     * @return void
//     */
//    function fillManErr();
//
//    /**
//     * 获取错误标识
//     * return string
//     */
//    static function GetErrSign();

    /**
     * 定义错误
     * return string
     */
    static function DefineErr();

//
//    /**
//     * 抛出错误
//     * return IFEErrControl
//     */
//    public static function ThrowErr($codenum,$attach_msg,$errsign=__CLASS__);
//


}
