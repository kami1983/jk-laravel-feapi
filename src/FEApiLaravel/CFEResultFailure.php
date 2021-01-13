<?php


namespace KLib\FEApiLaravel;

/**
 * 结果定义Api
 */
class CFEResultFailure extends CFEResult
{
    //AbsFEApiParamMan
//    const CONST_ERROR_DB_OPER = '1100851'; // 数据库错误
//    const CONST_ERROR_UNDEFINED_PARAM_NAME = '1100852'; // 未定义的参数
//    const CONST_ERROR_UNDEFINED_METHOD = '1100853'; // 未定义的方法
//    const CONST_ERROR_AUTH = '1100854'; // 授权错误
//
//    const CONST_ERROR_CLASS_NOTFOUND = '1000854'; // 未找到操作类
////    const CONST_ERROR_CREATE_NEED_PREFIX_AND_NAME = '1000855';
//    const CONST_ERROR_CREATE_CLASS_INHERITED = '1000856';
//    const CONST_ERROR_CREATE_CLASS_IS_BANED = '1000857'; // 创建API被禁止
//    const CONST_ERROR_TIMESTAMP_OF_UP_NOT_ON = '1000858';
//    const CONST_ERROR_TIMESTAMP_OF_DOWN_NOT_ON = '1000859';

    public $result = self::CONST_RESULT_STATUS_FAILURE;
    public $sign = '';
    public $error_code = '';
    public $error_info = '';
    public $error_attachmsg = '';

    /**
     * CFEResultFailure constructor.
     * @param ExceptionFEApi $exception
     * @param null $method 可选参数可以是 GET\POST\PUT\PATCH
     */
    public function __construct(ExceptionFEApi $exception, $method=null)
    {
        parent::__construct();
        $this->sign = $exception->getSign();
        $this->error_code = $exception->getCode();
        $this->error_info = $exception->getMessage();
        $this->error_attachmsg = $exception->getAttachMsg();
        $this->setMethod($method);
    }

}
