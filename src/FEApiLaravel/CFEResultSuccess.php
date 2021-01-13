<?php


namespace KLib\FEApiLaravel;

/**
 * 结果定义Api
 */
class CFEResultSuccess extends CFEResult
{
    public $result = self::CONST_RESULT_STATUS_SUCCESS;
    public $back_value = '';

    /**
     * CFEResultSuccess constructor.
     * @param mixed $back_value  成功的结果信息
     * @param null $method  可选参数可以是 GET\POST\PUT\PATCH
     */
    public function __construct($back_value, $method=null)
    {
        parent::__construct();
        $result = null;
        if(is_array($back_value)) {
            $result = CFEOutputHandle::OutputFromArr($back_value);
        }else{
            $result = CFEOutputHandle::OutputFromObj($back_value);
        }

        $this->back_value = $result;
        $this->setMethod($method);
    }

}
