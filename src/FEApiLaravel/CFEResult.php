<?php


namespace KLib\FEApiLaravel;

/**
 * 结果定义Api
 */
class CFEResult
{
    // 标识是成功的结果状态
    const CONST_RESULT_STATUS_FAILURE = 'failure';
    // 标识是失败的结果状态
    const CONST_RESULT_STATUS_SUCCESS = 'success';

    public $backtime = null;
    public $method = null;

    /**
     * 构造方法，构造方法直送会配置 backtime
    */
    public function __construct() {
        $this->backtime = time();

    }

    /**
     * @param $method 设置结果对象的请求参数
     */
    public function setMethod($method) {
        $this->method = $method;
    }
}
