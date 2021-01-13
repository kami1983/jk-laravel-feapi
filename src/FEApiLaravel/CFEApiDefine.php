<?php

namespace KLib\FEApiLaravel;
/**
 * Description of CFEApiDefine
 * 用来存储对API 接口的定义
 *
 * @author linhai
 */
class CFEApiDefine implements IFEApiExeture
{

    protected $_apiname = [];
    protected $_refarr = null;
    protected $_defineparam_arr = array();
    protected $_simpleresult = null;
    protected $_apidesc = null;
    protected $_resultdesc = null;

    // 主要是为了兼容之前的请求
    protected $_limit_method_arr = ['GET', 'POST'];


    /**
     * 构造方法
     * @return void
     */
    public function __construct()
    {
        // 初始化异常信息
        self::DefineErr();
    }

    /**
     * 返回定义的限制请求方法。
     */
    public function getLimitMethod()
    {
        return $this->_limit_method_arr;
    }

    /**
     * 设置限定方法数组，取值 ['GET','POST','PUT','PATCH']
     * @param array $method_arr 限制方法数组
     * @return CFEApiDefine
     */
    public function setLimitMethod(array $method_arr) {
        $this->_limit_method_arr =$method_arr;
        return $this;
    }

  /**
   * 设置API名称，新版本中将参数升级为数组
   * @param array $apiname
   * @return CFEApiDefine
   */
    public function setApiname(array $apiname)
    {
        $this->_apiname = $apiname;
        return $this;
    }

  /**
   * 添加一个新的API名称
   * @param string $apiname Api名称
   * @return CFEApiDefine
   */
    public function appendApiName(string $apiname) {
      if(!in_array($apiname, $this->_apiname)) {
        // 如果ApiName 不在引用数组中就进行添加，否则不动。
        $this->_apiname [] = $apiname;
      }
      return $this;
    }

    /**
     * 获取API 名称
     * @return array
     */
    public function getApiname()
    {
        return $this->_apiname;
    }

    /**
     * 设置对当前API 定义的描述信息
     * @return CFEApiDefine
     */
    public function setApidesc($desc)
    {
        $this->_apidesc = $desc;
        return $this;
    }

    /**
     * 获取对API 的描述
     * @return string
     */
    public function getApidesc()
    {
        return $this->_apidesc;
    }

    /**
     * 设置结果的描述信息
     * @return CFEApiDefine
     */
    public function setResultdesc($desc)
    {
        $this->_resultdesc = $desc;
        return $this;
    }

    /**
     * 获取结果集合的描述信息
     * @return string
     */
    public function getResultdesc()
    {
        return $this->_resultdesc;
    }


    /**
     * 设置API 引用名称
     * @param array $refarr API 的引用名称 ，（引用对象，调用名称）
     * @return CFEApiDefine
     */
    public function setRefname(array $refarr)
    {
        $this->_refarr = $refarr;
        return $this;
    }

    /**
     * 获取引用名称
     * @return array
     */
    public function getRefname()
    {
        if (!is_array($this->_refarr)) {
            CFEApiDefine::ThrowErr(CFEApiDefine::CONST_NEWERR_找不到引用名称);
//            throw new Exception('找不到引用名称',1809111742);
        }
        return $this->_refarr;
    }

    /**
     * 设置参数定义
     * @param array $param_arr 参数信息
     * @return CFEApiDefine
     */
    public function setParamDefine(array $param_arr = array())
    {
        //对参数定义进行简单的检查
        foreach ($param_arr as $param_key => $param_val) {
            if (!is_array($param_val)) {
                self::ThrowErr2(__CLASS__, CFEApiDefine::CONST_NEWERR_参数定义信息必须是数组);
            }
            if (!array_key_exists('info', $param_val)) {
                self::ThrowErr2(__CLASS__, CFEApiDefine::CONST_NEWERR_参数定义信息中必须包含INFO键值);
            }
        }

        $this->_defineparam_arr = $param_arr;
        return $this;
    }

    /**
     * 获取参数定义
     * @return array
     */
    public function getParamDefine()
    {
        return $this->_defineparam_arr;
    }


    /**
     * 设置样例结果信息
     * @param mixed $simple 样例
     * @return CFEApiDefine
     */
    public function setSampleResult($simple)
    {
        $this->_simpleresult = $simple;
        return $this;
    }

    /**
     * 获取结果样例值
     * @return mixed
     */
    public function getSampleResult()
    {
        return $this->_simpleresult;
    }

    /**
     * 执行函数，返回执行后的结果
     * @param array $paramarr 定义的参数数组
     */
    public function execute(array $paramarr)
    {

        $remake_paramarr = array();

        $paramdef_arr = $this->getParamDefine();
        $regular_check_arr = array();
        //初始化默认参数
        foreach ($paramdef_arr as $key => $value_arr) {

            if (array_key_exists('regular_check', $value_arr) && true == $value_arr['regular_check']) {
                $regular_check_arr[] = $key; //如果存在正则，那么不记录值
            } else {
                if (null === $value_arr['value']) {
                    continue;
                }
                $remake_paramarr[$key] = $value_arr['value'];
            }
        }

        foreach ($paramarr as $key => $value) {
//            echo " RUN A {$key}=>{$value} <br/> ";
//            echo print_r($regular_check_arr);
//            echo '<br/>';
            if (!array_key_exists($key, $paramdef_arr)) {
                //进行规则匹配
                $is_checked = false; //判断是否检查到
                foreach ($regular_check_arr as $regular_str) {
//                    echo "RUN X {$regular_str} {$key} <br/>";
                    if (preg_match($regular_str, $key)) {
                        if (null === $value) {
                            $value = $paramdef_arr[$regular_str]['value'];
                        }
//                        echo "RUN Y <br/>";
                        $is_checked = true;
                        break;
                    }
                }
                if (false === $is_checked) {
//                    CFECreater::ThrowErr(CFECreater::CONST_NEWERR_参数错误, "key={$key}");
                    CFEErr::ThrowErr2(__CLASS__, self::CONST_NEWERR_参数错误);
//                    throw new ExceptionFEApi(CFECreater::GetErrorDesc(CFECreater::CONST_ERROR_UNDEFINED_PARAM_NAME)."key={$key}",CFECreater::CONST_ERROR_UNDEFINED_PARAM_NAME);
                }
            } else {
                if (null === $value) {
                    $value = $paramdef_arr[$key]['value'];
                }
            }
            $remake_paramarr[$key] = $value;
        }


        $refarr = $this->getRefname();

        if (!is_callable(array($refarr[0], $refarr[1]))) {
            CFECreater::ThrowErr(CFECreater::CONST_NEWERR_找不到类方法, "method={$refarr[1]}");
        }
        $back_value_from_method = call_user_func(array($refarr[0], $refarr[1]), $remake_paramarr);
        return $back_value_from_method;
    }


    ##################################
    ########   IHaveErr   ############
    ##################################


    const CONST_NEWERR_找不到引用名称 = 1809201536;
    const CONST_NEWERR_参数定义信息必须是数组 = 1809201537;
    const CONST_NEWERR_参数定义信息中必须包含INFO键值 = 1809201538;
    const CONST_NEWERR_参数错误 = AbsFEApiParamMan::CONST_NEWERR_参数错误;

    /**
     * 捕获手册中所有涉及的异常错误处理API
     * @return void
     */
//    public function fillManErr(){
//        parent::fillManErr();
//        self::DefineErr(); //本API相关
//    }
//
//    /**
//     * 获取错误标识
//     * return string
//     */
//    public static function GetErrSign(){
//        return __CLASS__;
//    }

//    /**
//     * 添加异常信息的定义
//     * @param $errcode string
//     * @param $errmsg string
//     * @param $errsign string
//     * @return void
//    */
//    public static function AddException($errcode, $errmsg, $errsign) {
//        CFEErr::AddException($errcode, $errmsg, $errsign);
//    }


    /**
     * 定义错误
     * return string
     */
    public static function DefineErr()
    {
//        CFEErr::Err()->addException(self::CONST_NEWERR_找不到引用名称,'找不到引用名称', __CLASS__);
//        CFEErr::Err()->addException(self::CONST_NEWERR_参数定义信息必须是数组,'参数定义信息必须是数组', __CLASS__);
//        CFEErr::Err()->addException(self::CONST_NEWERR_参数定义信息中必须包含INFO键值,'参数定义信息中必须包含INFO键值', __CLASS__);
//
//        self::AddException(self::CONST_NEWERR_找不到引用名称,'找不到引用名称', __CLASS__);
//        self::AddException(self::CONST_NEWERR_参数定义信息必须是数组,'参数定义信息必须是数组', __CLASS__);
//        self::AddException(self::CONST_NEWERR_参数定义信息中必须包含INFO键值,'参数定义信息中必须包含INFO键值', __CLASS__);

        CFEErr::AddException(__CLASS__, self::CONST_NEWERR_找不到引用名称, '找不到引用名称');
        CFEErr::AddException(__CLASS__, self::CONST_NEWERR_参数定义信息必须是数组, '参数定义信息必须是数组');
        CFEErr::AddException(__CLASS__, self::CONST_NEWERR_参数定义信息中必须包含INFO键值, '参数定义信息中必须包含INFO键值');
        CFEErr::AddException(__CLASS__, self::CONST_NEWERR_参数错误, '参数错误，可能是传入的参数值并未定义。');
    }

    /**
     * 抛出错误
     * @param $codenum string 异常代码
     * @param $sign string 异常标识
     * @param $attach_msg string 可选的附加值
     * return void
     */
    public static function ThrowErr2($sign, $codenum, $attach_msg = '')
    {
        // 注册这个错误信息到 CFEErr 错误信息管理类中。
        CFEErr::RegisterErr(__CLASS__, function () {
            self::DefineErr();
        });

        // 抛出错误信息
        CFEErr::ThrowErr2($sign, $codenum, $attach_msg);
    }

}
