<?php

namespace KLib\FEApiLaravel;

use Illuminate\Http\Request;
use phpDocumentor\Reflection\Types\ClassString;

/**
 * Description of AbsFEApiParamMan
 * 用来生成API 手册
 * @author linhai
 */
abstract class AbsFEApiParamMan implements IFEApiAssessControl, IFEApiOperControl, IFEApiEvents, IHaveErr
{


  protected $_param_arr = array();
//    protected $_is_show_simple=false;
  protected $_errorinfo_arr = array();

  /* @var $_apidefine_arr CFEApiDefine[] *///API 定义
  protected $_apidefine_arr = array();

  /* @var $_ref_apidefine_arr CFEApiDefine[] *///API 定义
  protected $_ref_apidefine_arr = array();

  /* @var $_timestamp_up int *///上线的时间戳
  protected $_timestamp_up = self::CONST_TIMESTAMP_UP_UNLIMIT;

  /* @var $_timestamp_down int *///下线的时间戳
  protected $_timestamp_down = self::CONST_TIMESTAMP_DOWN_UNLIMIT;

  /**
   * @var $_request Request
   */
  protected $_request = null;

  protected $_apiname = __CLASS__;

  protected $_meta_arr = [];

  /**
   * 构造方法
   */
  public function __construct(Request $request = null)
  {
    $this->init();
    // 设置请求对象到全局
    if ($request instanceof Request) {
      $this->setRequest($request);
    }
  }

  /**
   * 初始化函数，这是构造方法调用的第一个函数
   * @return AbsFEApiParamMan
   */
  public function init()
  {
    // 初始化静态错误信息
    self::DefineErr();
    // 初始化Api接口信息
    $this->apiDefine();
    return $this;
  }

  /**
   * 设置Meta信息
   * @param array $meta 设置的Meta 值
   * @return AbsFEApiParamMan
   */
  public function setMeta(array $meta) {
    $this->_meta_arr= $meta;
  }

  /**
   * 获取Meta 值。
   * @return array
   */
  public function getMeta() {
    return $this->_meta_arr;
  }

  /**
   * 设置ApiName，如果不设置默认的ApiName是类名称
   * @return AbsFEApiParamMan
   */
  public function setApiName($apiname)
  {
    $this->_apiname = $apiname;
    return $this;
  }

  /**
   * 获取ApiName，默认是当前类名称，可以通过setApiName 方法改变这里的值
   * @return string
   */
  public function getApiName()
  {
    return $this->_apiname;
  }

  /**
   * 查找某个API的别名是否存在
   * @param string $api_aliases_name API别名
   * @return boolean
   */
  public function isApiExists($api_aliases_name, $method = 'GET')
  {
    $apidefine_arr = $this->getApiDefineArr();
    if (isset($apidefine_arr[$api_aliases_name]) && isset($apidefine_arr[$api_aliases_name][$method])) {
      if ($this->_apidefine_arr[$api_aliases_name][$method] instanceof CFEApiDefine) {
        return true;
      }
    }

    return false;
  }

  /**
   * 捕获手册中所有涉及的异常错误处理API
   * @return void
   */
  public function fillManErr()
  {
    CFEErr::DefineErr();
//    CFECreater::DefineErr(); //使用的异常信息
    self::DefineErr(); //本API相关
  }

  /**
   * Call 函数用来整合调用方法，从而支持触发器。
   * @param string $apiname 被调用的API 名称
   * @param array $paramarr 参数名称
   * @return mixed
   */
  public function callApi($apiname, array $paramarr = array())
  {

//    echo $this->getRequest()->method();
    $defineObj = $this->getApiDefineObj($apiname, $this->getRequest()->method());
    /* @var $defineObj CFEApiDefine */
    $refarr = $defineObj->getRefname();
    $this->eventBeforeCallApi($defineObj, $paramarr);
    $this->beforeCallTrigger($refarr[1], $paramarr); //（过时的方法）下次重构删除
    $back_value_from_method = $defineObj->execute($paramarr);
    $this->eventAfterCallApi($defineObj, $paramarr, $back_value_from_method);
    $this->afterCallTrigger($refarr[1], $paramarr, $back_value_from_method); //（过时的方法）下次重构删除

    return $back_value_from_method;
  }

  /**
   * 进行API 定义
   * $this->createApi('apiname', 'refname', 'Describe.')->setParamDefine(['key1' => ['info' => 'desc1', 'value' => 'default value' ], 'key2' => ['info' => 'desc1', 'value' => null ] ]);
   * @return AbsFEApiParamMan
   */
  abstract function apiDefine();


  /** 手册定义方法
   * @return void
   */
  public function manDefine()
  {
    $this->fillManErr(); //填充手册所需要的错误信息
  }

  ###########################
  ####### IFEApiEvents ######
  ###########################

  /**
   * 调用触发器方法
   * @param string $callmethod 被调用方法
   * @param array &$paramarr 被调用方法带入的参数
   * @return mixed
   */
  public function beforeCallTrigger($callmethod, array &$paramarr)
  {

  }

  /**
   * 调用触发器方法之后
   * @param string $callmethod 被调用方法
   * @param array $paramarr 被调用方法带入的参数
   * @param mixed $result 返回的结果
   * @return mixed
   */
  public function afterCallTrigger($callmethod, array $paramarr, $result)
  {

  }

  /**
   * 调用触发器方法
   * @param CFEApiDefine $apidefineobj 被调用方法
   * @param array &$paramarr 被调用方法带入的参数
   * @return mixed
   */
  public function eventBeforeCallApi(CFEApiDefine $apidefineobj, array &$paramarr)
  {
  }

  /**
   * 调用触发器方法之后
   * @param CFEApiDefine $apidefineobj 被调用方法
   * @param array &$paramarr 被调用方法带入的参数
   * @param mixed $result 返回的结果
   * @return mixed
   */
  public function eventAfterCallApi(CFEApiDefine $apidefineobj, array &$paramarr, $result)
  {
  }

  ##################################
  ####### IFEApiAssessControl ######
  ##################################

  /**
   * 设置Request 对象，如果 getPasswordOfMan 返回非null 的值，那么需要设置request 对象
   * 以保证密码可以正确的存储到session 中
   * @return IFEApiAssessControl
   */
  public function setRequest(Request $request)
  {
    $this->_request = $request;
  }

  /**
   * 返回set 方法设置的Request 对象
   * @return Request
   */
  public function getRequest()
  {
    return $this->_request;
  }

  /**
   * 验证手测查看密码是否正确
   * @return boolean
   */
  public function verifyPasswordOfMan()
  {
    if (null === $this->getPasswordOfMan()) {
      return true;
    }

    if (null === $this->getRequest()) {
      // 如果没有request 对象直接返回密码错误，因为根本没有登录动作。
      return false;
    }

    //否则访问SESSION 进行查询
//        $sessionvalue = CJKApp::GetWebApp()->webParam()->getSessionValue(md5(__FILE__));
    $sessionvalue = $this->getRequest()->session()->get(md5(basename(__FILE__)));
    if ($sessionvalue === $this->getPasswordOfMan()) {
      return true;
    }

    return false;
  }


  /**
   * 获取API 手册的查看密码
   * @return null 为null 是表示忽略密码功能
   */
  public function getPasswordOfMan()
  {
    return null;
  }

  /**
   * 签入验证的密码信息用于之后的验证。
   * @return IFEApiAssessControl
   */
  public function signinVerifyPassword($password)
  {
//        CJKApp::GetWebApp()->webParam()->setSessionValue(md5(__FILE__), $password);

    if (null === $this->getRequest()) {
      // 如果没有request 对象会报错并提示
      CFEErr::ThrowErr2(__CLASS__, self::CONST_NEWERR_MAN_SINGIN_ERROR, '登录错误，登录需要调用 setRequest() 方法设置 Illuminate\Http\Request 对象。');
    }
    // 设置密码
    $this->getRequest()->session()->put(md5(basename(__FILE__)), $password);
    return $this;
  }

  /**
   * 对某域名是否开启CORS 功能
   * @param string $url 来源地址，http://www.baidu.com/a.html
   * @return boolean
   */
  public function isOpenCORS($url)
  {
    return false; //默认关闭CORS
  }

  /**
   * 对某域名是否开启CORS 功能
   * @param string $url 来源地址，http://www.baidu.com/a.html
   * @return boolean
   */
  public function isOpenJSONP($url)
  {
    return false; //对任何域名都不启JSONP 功能
  }

  /**
   * Ref url 过滤器如果出现问题则直接异常
   * @param string $ip 来源URL
   * @return AbsFEApiParamMan
   */
  public function filterIP($ip)
  {
    return $this;
  }

  /**
   * Ref url 过滤器如果出现问题则直接异常
   * @return AbsFEApiParamMan
   */
  public function filterRule()
  {
    return $this;
  }

  /**
   * Ref url 过滤器如果出现问题则直接异常
   * @param string $refurl 来源URL
   * @return AbsFEApiParamMan
   */
  public function filterRef($refurl)
  {
    return $this;
  }

  /**
   * 返回应用的上线时间戳，如果不设定上线时间戳返回 -1
   * 否则，这个时间应该大于0，以避免错误。
   * @return int
   */
  public function getTimeStampOfUp()
  {
    return $this->_timestamp_up;//CFECreater::CONST_TIMESTAMP_UP_UNLIMIT;
  }

  /**
   * 设置应用的上线时间戳，如果不设定上线时 timestamp 设定为 -1
   * 否则，这个时间应该大于0，以避免错误。
   * @param int $timestamp 时间戳，带入-1 标识不设定上线时间
   * @return IFEApiAssessControl
   */
  public function setTimeStampOfUp($timestamp)
  {
    if (null == $timestamp || 0 == $timestamp) {
//            throw new Exception('ERROR CODE::AbsFEApiParamMan_1019 CONTENT::设定上线时间戳设定值需要大于0 或者等于-1');
//      self::ThrowErr(self::CONST_NEWERR_设定上线时间戳设定值需要大于0);
      CFEErr::ThrowErr2(__CLASS__, self::CONST_NEWERR_设定上线时间戳设定值需要大于0);
    }
    $this->_timestamp_up = $timestamp;
    return $this;
  }

  /**
   * 返回应用的下线时间戳，如果不设定下线时间戳返回 -1
   * 否则，这个时间应该大于0，以避免错误。
   * @return int
   */
  public function getTimeStampOfDown()
  {
    return $this->_timestamp_down;//CFECreater::CONST_TIMESTAMP_DOWN_UNLIMIT;
  }

  /**
   * 设定应用的下线时间戳，
   * @param int $timestamp 时间戳，带入-1 标识不设定下线时间
   * @return IFEApiAssessControl
   */
  public function setTimeStampOfDown($timestamp)
  {
    if (null == $timestamp || 0 == $timestamp) {
//            throw new Exception('ERROR CODE::AbsFEApiParamMan_1021 CONTENT::设定下线时间戳设定值需要大于0 或者等于-1');
//      self::ThrowErr(self::CONST_NEWERR_设定下线时间戳设定值需要大于0);
      CFEErr::ThrowErr2(__CLASS__, self::CONST_NEWERR_设定下线时间戳设定值需要大于0);
    }
    $this->_timestamp_down = $timestamp;
    return $this;
  }



  ################ IFEApiAssessControl 接口定义 END

  ############# 新增功能

  ################ IFEApiOperControl 接口实现

  /**
   * 定义转接方法
   * @return mixed
   */
//    public function __call($name, $arguments) {
//        $defobj=$this->getApiDefineObj($name);
//        if($defobj instanceof CFEApiDefine && method_exists($this, $defobj->getRefname())){
//            $result= call_user_func_array(array($this, $defobj->getRefname()), $arguments);
//            return $result;
//        }
//        throw new ExceptionFEApi($this->getErrorInfo(CFECreater::CONST_ERROR_UNDEFINED_METHOD).__METHOD__,CFECreater::CONST_ERROR_UNDEFINED_METHOD);
//    }


  /**
   * 创建一个API 定义对象
   * @param string $apiname API名称
   * @param mixed $refname 引用名称，有可能是string，也有可能是数组
   * @return CFEApiDefine
   */
  public function createApi($apiname, $refname = null, $desc = null)
  {
    $apiname = trim($apiname);
    if ('' == $apiname) {
//      self::ThrowErr(self::CONST_NEWERR_要创建的接口名称不能为空);
//            throw new Exception('ERROR CODE::AbsFEApiParamMan_1641 CONTENT::要创建的接口名称不能为空');
      CFEErr::ThrowErr2(__CLASS__, self::CONST_NEWERR_要创建的接口名称不能为空);
    }

    if (null == $refname) {
      $refname = $apiname;
    }

    $ref_arr = $refname;
    if (!is_array($refname)) {
      $ref_arr = array($this, $refname);
    }

    $apidefine_obj = new CFEApiDefine();

    // 先检查RefName 是不是已经存在，如果存在则先取出并赋值
    if(isset($this->_ref_apidefine_arr[$ref_arr[1]])) {
      // 取出旧的值
      $apidefine_obj = $this->_ref_apidefine_arr[$ref_arr[1]];
    }else{
      // 新值需要将信息添加到 _ref_apidefine_arr 全局中。
      $this->_ref_apidefine_arr[$ref_arr[1]] = $apidefine_obj;
      // 添加引用值
      $apidefine_obj->setRefname($ref_arr);
    }

    // 追加ApiName
    $apidefine_obj->appendApiName($apiname);
    // 描述信息会被覆盖掉，如果之前有
    $apidefine_obj->setApidesc($desc);

    // 因为添加了 Restful的风格支持，这个引用对象被迫进行了改造
//        $this->_apidefine_arr[$apidefine_obj->getApiname()] = $apidefine_obj; //创建对象引用
//    $refarr = $apidefine_obj->getRefname();
//    $this->_ref_apidefine_arr["{$refarr[1]}{$apiname}"] = $apidefine_obj; //创建对象引用
//    $this->_ref_apidefine_arr["{$refarr[1]}"] =  $apidefine_obj; //创建对象引用
//    $this->_ref_apidefine_arr["{$refarr[1]}"]["{$apiname}"] = $apidefine_obj;

    // 需要重置定义数组，这样get函数会重新生成该数组【重要】
    $this->_apidefine_arr = [];

    // 返回定义对象
    return $apidefine_obj;
  }

  /**
   * 获取API 定义数组
   * @return CFEApiDefine[]
   */
  public function getApiDefineArr()
  {
//        return $this->_apidefine_arr;
    if (count($this->_apidefine_arr) > 0) {
      // 表示有缓存值，直接返回即可。
      return $this->_apidefine_arr;
    }

    // 否则需要重新计算
    $result = [];
    // 循环引用定义数组
    foreach ($this->_ref_apidefine_arr as $refobj) {
      // 第一次循环就可以活动额引用对象数组
        /** @var CFEApiDefine $refobj */
        foreach($refobj->getApiname() as $apiname) {
          if(!isset($result[$apiname])) {
            $result[$apiname] = []; // 建立一个REF对应的数组
          }
          foreach ($refobj->getLimitMethod() as $method) {
            $result[$apiname][$method] = $refobj;
          }
        }
    }

    // 给入缓存值，并且返回
    return $this->_apidefine_arr = $result;

  }

  /**
   * @param $apiname API的名称，注意一个API名称可能对应多个不同的引用 比如 apiname-GET，apiname-PUT，apiname-DELETE
   * @param string $method 可选参数默认是GET
   * @return CFEApiDefine|mixed
   */
  public function getApiDefineObj($apiname, $method = 'GET')
  {

    $defined_arr = $this->getApiDefineArr();

    if (!isset($defined_arr[$apiname]) || !isset($defined_arr[$apiname][$method])) {
      CFEErr::ThrowErr2(__CLASS__, self::CONST_NEWERR_方法名称未定义, 'apidefine 数组中找不到值。');
    }

    $result = $defined_arr[$apiname][$method];
    if (!$result instanceof CFEApiDefine) {
      $result = $this->_ref_apidefine_arr[$apiname]; //如果没有找到引用名称的API 那么返回最终API值
    }

    if (!$result instanceof CFEApiDefine) {
      CFEErr::ThrowErr2(__CLASS__, self::CONST_NEWERR_方法名称未定义, '定义对象不是 CFEApiDefine。');
    }
    return $result;
  }

  ################ IFEApiOperControl END

  ############# 新增功能 END

  /**
   * 定义API 描述
   * @return string
   */
  public function definedDesc()
  {
    return 'Developers is lazy, No API description information!';
  }


  /**
   * 定义API 的错误信息
   * @return array
   */
  public function getDefineErrors()
  {
    $this->_errorinfo_arr = CFEErr::GetErrList();
    return $this->_errorinfo_arr;
  }

  /**
   * 获取定义的错误信息
   * @return string
   */
  public function getErrorInfo($erorcode)
  {
    $error_arr = $this->getDefineErrors();
    return $error_arr[$erorcode];
  }


  /**
   * 定义结果正确时返回的结果
   * @return array
   */
  public function getSimpleResult($method)
  {
    $definobj = $this->getApiDefineObj($method);
    if ($definobj instanceof CFEApiDefine) {
      return $definobj->getSampleResult();
    }
    return null;
  }


  /**
   * 获取参数数组，主要用于生成文档用
   * @return array
   */
  public function getParamArr()
  {


    //将该对象转换为输出
    $result_arr = array();
    foreach ($this->getApiDefineArr() as $apikey => $method_arr) {

      if(!isset($result_arr[$apikey])) {
        $result_arr[$apikey] = [];
      }
      foreach ($method_arr as $method => $apidefine_obj) {
        /* @var $apidefine_obj CFEApiDefine */
        $result_arr[$apikey][$method]['refer_desc'] = get_class($apidefine_obj->getRefname()[0]). ' # '.$apidefine_obj->getRefname()[1];
        $result_arr[$apikey][$method]['api_desc'] = $apidefine_obj->getApidesc();
        $result_arr[$apikey][$method]['params'] = $apidefine_obj->getParamDefine();
        $result_arr[$apikey][$method]['sample'] = $apidefine_obj->getSampleResult();
        $result_arr[$apikey][$method]['result_desc'] = $apidefine_obj->getResultdesc();
      }
    }
    return $result_arr;
  }

  /**
   * 设置参数数组，实际上已经作废
   * @param string $method
   * @param array $paramarr
   * @return AbsFEApiParamMan
   */
  public function setParamArr($method, array $paramarr)
  {

    // 如果没有定义的配置对象数组直接报错
    if (!isset($this->_ref_apidefine_arr[$method])) {
      CFEErr::ThrowErr2(__CLASS__, self::CONST_NEWERR_方法名称未定义, '_ref_apidefine_arr');
    }

    //获取参数的引用对象
    $apidefobj = $this->_ref_apidefine_arr[$method];
    if (!$apidefobj instanceof CFEApiDefine) {
      CFEErr::ThrowErr2(__CLASS__, self::CONST_NEWERR_方法名称未定义, '定义类不是 CFEApiDefine 子类');
//      CFECreater::ThrowErr(CFECreater::CONST_NEWERR_方法名称未定义);
//            throw new ExceptionFEApi($this->getErrorInfo(CFECreater::CONST_ERROR_UNDEFINED_METHOD).' '.__METHOD__,CFECreater::CONST_ERROR_UNDEFINED_METHOD);
    }

    //参数检查
    $this->_param_arr[$method] = array();

    $paramdef_arr = $apidefobj->getParamDefine();
    $regular_check_arr = array();
    //初始化默认参数
    foreach ($paramdef_arr as $key => $value_arr) {

      if (array_key_exists('regular_check', $value_arr) && true == $value_arr['regular_check']) {
        $regular_check_arr[] = $key; //如果存在正则，那么不记录值
      } else {
        if (null === $value_arr['value']) {
          continue;
        }
        $this->_param_arr[$method][$key] = $value_arr['value'];
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
//          CFECreater::ThrowErr(CFECreater::CONST_NEWERR_参数错误);
          CFEErr::ThrowErr2(__CLASS__, self::CONST_NEWERR_参数错误);
//                    throw new ExceptionFEApi($this->getErrorInfo(CFECreater::CONST_ERROR_UNDEFINED_PARAM_NAME)."key={$key}",CFECreater::CONST_ERROR_UNDEFINED_PARAM_NAME);
        }
      } else {
        if (null === $value) {
          $value = $paramdef_arr[$key]['value'];
        }
      }
      $this->_param_arr[$method][$key] = $value;
    }

    return $this;
  }


  /**
   * 获取URL 调用参数，用于提供健全文档
   * @param string $method
   * @return string
   */
//  public function getUrlGetParam($apiname, $method)
//  {
//    $param_arr = $this->getParamArr();
//    $result_str = '';
//    $getparam_result_arr = [];
//    foreach($param_arr[$apiname] as $method => $param_arr){
//      $getparam_result_arr[$method]='';
//      foreach($param_arr as $param => $value){
//        $value = urlencode(null === $value ? 'your_param' : $value);
//        $result_str .= "&fps[{$param}]={$value}";
//      }
//    }
//    foreach ($param_arr[$method] as $param_key => $param_valarr) {
//      $value = urlencode(null === $param_valarr['value'] ? 'your_param' : $param_valarr['value']);
//      $result_str .= "&fps[{$param_key}]={$value}";
//    }
//    return $getparam_result_arr;
//  }

  /**
   * 根据正则对参数进行分组
   * @return array
   */
  public function getParamValArrByRegular($method, $regular_str)
  {
    //首先获取所有的参数
    $all_param = $this->getAllParamValArr($method);
    $result_arr = array();
    foreach ($all_param as $param_key => $param_val) {
      if (preg_match($regular_str, $param_key)) {
        $result_arr[$param_key] = $param_val;
      }
    }
    return $result_arr;
  }

  /**
   * 获取所有的参数值，数组
   * @param string $method 被调用的参数名称
   * @return array
   */
  public function getAllParamValArr($method)
  {
    if (!isset($this->_param_arr[$method])) {
      return array();
    }

    return $this->_param_arr[$method];
  }

  /**
   * 获取某个参数的值，实际上已经作废
   * @param string $method
   * @param string $paramname
   * @return string
   */
  public function getParamVal($method, $paramname)
  {

    if (!isset($this->_param_arr[$method])) {
      $this->setParamArr($method, array());
    }

    $param_arr = $this->_param_arr;

    if (!is_array($param_arr[$method])) {
      CFEErr::ThrowErr2(__CLASS__, self::CONST_NEWERR_参数错误, " getParamVal - method={$method}");
//      CFECreater::ThrowErr(CFECreater::CONST_NEWERR_参数错误, " getParamVal - method={$method}");
//      throw new ExceptionFEApi($this->getErrorInfo(CFECreater::CONST_ERROR_UNDEFINED_PARAM_NAME)." getParamVal - method={$method}",CFECreater::CONST_ERROR_UNDEFINED_PARAM_NAME);
    }

    if (array_key_exists($paramname, $param_arr[$method])) {
      return $param_arr[$method][$paramname];
    }

    CFEErr::ThrowErr2(__CLASS__, self::CONST_NEWERR_参数错误, " getParamVal - method={$paramname}");
//      CFECreater::ThrowErr(CFECreater::CONST_NEWERR_参数错误, " getParamVal - method={$paramname}");
//      throw new ExceptionFEApi($this->getErrorInfo(CFECreater::CONST_ERROR_UNDEFINED_PARAM_NAME)." getParamVal - paramname={$paramname}",CFECreater::CONST_ERROR_UNDEFINED_PARAM_NAME);
  }

  ##################################
  ########   IHaveErr   ############
  ##################################


  //AbsFEApiParamMan
  const CONST_ERROR_DB_OPER = '1100851'; // CONST_ERROR_DB_OPER_1000851 - CONST_ERROR_DB_OPER
  const CONST_ERROR_UNDEFINED_PARAM_NAME = '1100852'; // CONST_ERROR_UNDEFINED_PARAM_NAME_1000852 - CONST_ERROR_UNDEFINED_PARAM_NAME
  const CONST_ERROR_UNDEFINED_METHOD = '1100853'; // CONST_ERROR_UNDEFINED_METHOD_1000853 - CONST_ERROR_UNDEFINED_METHOD
  const CONST_ERROR_AUTH = '1100854'; // CONST_ERROR_AUTH_1000854 - CONST_ERROR_AUTH

  const CONST_ERROR_CLASS_NOTFOUND = '1000854';
  const CONST_ERROR_CREATE_NEED_PREFIX_AND_NAME = '1000855';
  const CONST_ERROR_CREATE_CLASS_INHERITED = '1000856';
  const CONST_ERROR_CREATE_CLASS_IS_BANED = '1000857';

  const CONST_ERROR_TIMESTAMP_OF_UP_NOT_ON = '1000858';
  const CONST_ERROR_TIMESTAMP_OF_DOWN_NOT_ON = '1000859';

  const CONST_RESULT_STATUS_FAILURE = 'failure';
  const CONST_RESULT_STATUS_SUCCESS = 'success';

  const CONST_TIMESTAMP_UP_UNLIMIT = -1;
  const CONST_TIMESTAMP_DOWN_UNLIMIT = -1;


  const CONST_NEWERR_上线时间戳格式错误 = 1809191807;
  const CONST_NEWERR_设定上线时间戳设定值需要大于0 = 1809201453;
  const CONST_NEWERR_设定下线时间戳设定值需要大于0 = 1809201456;
  const CONST_NEWERR_要创建的接口名称不能为空 = 1809201457;
  const CONST_NEWERR_MAN_SINGIN_ERROR = 2001062223;

  const  CONST_NEWERR_数据库操作错误 = self::CONST_ERROR_DB_OPER;
  const  CONST_NEWERR_参数错误 = self::CONST_ERROR_UNDEFINED_PARAM_NAME;
  const  CONST_NEWERR_方法名称未定义 = self::CONST_ERROR_UNDEFINED_METHOD;
  const  CONST_NEWERR_找不到类方法 = self::CONST_ERROR_CLASS_NOTFOUND;
  const  CONST_NEWERR_需要API前缀 = self::CONST_ERROR_CREATE_NEED_PREFIX_AND_NAME;
  const  CONST_NEWERR_API继承错误 = self::CONST_ERROR_CREATE_CLASS_INHERITED;
  const  CONST_NEWERR_动作被禁止 = self::CONST_ERROR_CREATE_CLASS_IS_BANED;
  const  CONST_NEWERR_未到上线时间 = self::CONST_ERROR_TIMESTAMP_OF_UP_NOT_ON;
  const  CONST_NEWERR_已是离线时间 = self::CONST_ERROR_TIMESTAMP_OF_DOWN_NOT_ON;
  const  CONST_NEWERR_授权错误 = self::CONST_ERROR_AUTH;


  /**
   * 定义错误
   * return string
   */
  public static function DefineErr()
  {

    // 添加异常信息到全局对象中
    CFEErr::AddException(__CLASS__, self::CONST_NEWERR_上线时间戳格式错误, '上线时间格式错误');
    CFEErr::AddException(__CLASS__, self::CONST_NEWERR_设定上线时间戳设定值需要大于0, '设定上线时间戳设定值需要大于0');
    CFEErr::AddException(__CLASS__, self::CONST_NEWERR_设定下线时间戳设定值需要大于0, '设定下线时间戳设定值需要大于0');
    CFEErr::AddException(__CLASS__, self::CONST_NEWERR_要创建的接口名称不能为空, '要创建的接口名称不能为空');
    CFEErr::AddException(__CLASS__, self::CONST_NEWERR_MAN_SINGIN_ERROR, '手册登录错误，可能Requet类并没有注入到Api中，可以在构造方法里尝试注入 Illuminate\Http\Request');

    CFEErr::AddException(__CLASS__, self::CONST_NEWERR_数据库操作错误, 'The database operation error.');
    CFEErr::AddException(__CLASS__, self::CONST_NEWERR_参数错误, 'The param name undefined.');
    CFEErr::AddException(__CLASS__, self::CONST_NEWERR_方法名称未定义, 'The method name undefined.');
    CFEErr::AddException(__CLASS__, self::CONST_NEWERR_找不到类方法, 'The action class is not found.');
    CFEErr::AddException(__CLASS__, self::CONST_NEWERR_需要API前缀, 'Need class name and api prefix.');
    CFEErr::AddException(__CLASS__, self::CONST_NEWERR_API继承错误, 'The API inherited error..');
    CFEErr::AddException(__CLASS__, self::CONST_NEWERR_动作被禁止, 'The action class is baned..');
    CFEErr::AddException(__CLASS__, self::CONST_NEWERR_未到上线时间, 'Not to the on-line time..');
    CFEErr::AddException(__CLASS__, self::CONST_NEWERR_已是离线时间, 'Offline time is passed.');
    CFEErr::AddException(__CLASS__, self::CONST_NEWERR_授权错误, '授权错误');

  }


}
