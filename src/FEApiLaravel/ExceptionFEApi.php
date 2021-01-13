<?php

namespace KLib\FEApiLaravel;

/**
 * Description of ExceptionFEApi
 * 异常继承实现类
 *
 * @author linhai
 */
class ExceptionFEApi extends \Exception {



    protected $_sign = null;
    protected $_code = null;
    protected $_attach_msg=null;

    /**
     * 已过期的方法
     * @deprecated 请使用 CreateErr2 方法替代
    */
    public static function CreateErr($message, $code, $sign=''){
        // 这个方法未来会作废。
        return self::CreateErr2($sign, $code, $message);
    }

    /**
     * 根据最新版本的Err 处理方法，sign，与 code 必须同时出现，且 sign 是更大范围的分组所以将该参数前置。
     * @param $sign string 异常的标识信息
     * @param $code string 异常的代码信息
     * @param $message string 异常的提示消息
     */
    public static function CreateErr2($sign, $code, $message ) {
        $err=new ExceptionFEApi($message, $code);
        $err->setSign($sign);
        return $err;
    }

//    /**
//     * 设置code
//     * @param string $code
//     * @return ExceptionFEApi
//    */
//    public function setCode ($code) {
//      $this->_code = $code;
//      return $this;
//    }
//
//    /**
//     * 获取code
//     * @return string
//    */
//    public function getCode() {
//      return $this->_code;
//    }


     /**
     * 设置错误标识
     * @return int
     */
    public function setSign($sign){
        $this->_sign=$sign;
    }

    /**
     * 获取错误标识
     * @return int
     */
    public function getSign(){
        return $this->_sign;
    }
    /**
     * 获取错误信息
     * @return int
     */
    public function getAttachMsg(){
        return $this->_attach_msg;
    }

    /**
     * 获取错误信息
     * @return int
     */
    public function setAttachMsg($attach_msg){
        $this->_attach_msg = $attach_msg;

    }




}
