<?php

namespace KLib\FEApiLaravel;


use Illuminate\Http\Response;
use KLib\FEApiLaravel\AbsFEApiParamMan;
use KLib\FEApiLaravel\CFEApiDefine;
use KLib\FEApiLaravel\CFEErr;
use Illuminate\Http\Request;


/**
 * Description of CFEApiExample2
 * 大转盘游戏支持接口
 *
 * @author ranran
 */
class CFEApiExample extends AbsFEApiParamMan
{


  protected $_param_arr = array();

  /**
   * CFEApiExample 构造方法.
   * @param Request|null $request 请求对象，为了兼容变成可选参数
   */
  public function __construct(Request $request = null)
  {
    // 调用父类构造方法
    parent::__construct($request);
    // 设置请求对象到全局
    if ($request instanceof Request) {
      $this->setRequest($request);
    }
  }

  /**
   * 捕获手册中所有涉及的异常错误处理API
   * @return void
   */
//  public function fillManErr()
//  {
//    parent::fillManErr();
//    self::DefineErr(); //本API相关
//  }

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
    $callmethod = $apidefineobj->getRefname();
    if ('testEvents' == $callmethod[1] && 'linhai' == $paramarr['name']) {
      $paramarr['name'] = '林海';
    }
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


  ###########

  /**
   * 设置手册密码
   * @return string
   */
  public function getPasswordOfMan()
  {
    return '123';
  }

  /**
   * 是否开启JSONP
   * @return boolean
   */
  public function isOpenJSONP($url)
  {
    return true; //无条件允许json 调用
  }

  /**
   * 是否开启CORS
   * @return boolean
   */
  public function isOpenCORS($url)
  {
    return true;
  }


  /**
   * Ref url 过滤器如果出现问题则直接异常
   * @param string $ip 来源URL
   * @return AbsFEApiParamMan
   */
  public function filterIP($ip)
  {
    parent::filterIP($ip);

    if ('255.255.255.1' == $ip) {
//      CFECreater::ThrowErr(CFECreater::CONST_NEWERR_授权错误, "A1 ");
      CFEErr::ThrowErr2(__CLASS__, self::CONST_NEWERR_授权错误);
//            throw new ExceptionFEApi($this->getErrorInfo(CFECreater::CONST_ERROR_AUTH).' A1 ',CFECreater::CONST_ERROR_AUTH);
    }
    return $this;
  }


  /**
   * Ref url 过滤器如果出现问题则直接异常
   * @return AbsFEApiParamMan
   */
  public function filterRule()
  {
    $cookie_val = CJKApp::GetWebApp()->webParam()->getCookieValue('test_rule_cookie');

    if ('1006' == $cookie_val) {
      return $this;
    }
    CFECreater::ThrowErr(CFECreater::CONST_NEWERR_授权错误, "A2 ");
//        throw new ExceptionFEApi($this->getErrorInfo(CFECreater::CONST_ERROR_AUTH).' A2 ',CFECreater::CONST_ERROR_AUTH);
  }

  /**
   * 过滤器
   * @param string $refurl 来源URL
   * @return CFEApiExample
   */
  public function filterRef($refurl)
  {
    parent::filterRef($refurl);


    if (preg_match('/\.cancanyou\.com/iU', $refurl) ||
      preg_match('/\.kaitianad\.com/iU', $refurl) ||
      preg_match('/^http:\/\/1251212175\.cdn\.myqcloud\.com*/iU', $refurl)) {
      return $this;
    }
    CFECreater::ThrowErr(CFECreater::CONST_NEWERR_授权错误, "A3");
//        throw new ExceptionFEApi($this->getErrorInfo(CFECreater::CONST_ERROR_AUTH).' A3 ',CFECreater::CONST_ERROR_AUTH);
  }

  /**
   * 用户分享增加一个级别，级别到3可以有权利抽奖励
   * @return string
   */
  public function additive(array $paramarr)
  {
//        $method_name=__FUNCTION__;
//        //填补定义参数值
////        $this->setParamArr($method_name,$paramarr);
//        $value_a=$this->getParamVal($method_name, 'a');
//        $value_b=$this->getParamVal($method_name, 'b');

    $value_a = $paramarr['a'];
    $value_b = $paramarr['b'];

//    $this->getRequest()->isMethod('get');

    //------------------------

    return $value_a + $value_b;
  }

  /**
   * 用户分享增加一个级别，级别到3可以有权利抽奖励
   * @return string
   */
  public function getsession(array $paramarr)
  {
    $method_name = __FUNCTION__;
    //填补定义参数值
    $this->setParamArr($method_name, $paramarr);
    //------------------------
    $session_name = 'test_session';
//    $session_val = CJKApp::GetWebApp()->webParam()->getSessionValue($session_name);

    $this->getRequest()->session()->get($session_name);

    if (!isset($session_val)) {
//      CJKApp::GetWebApp()->webParam()->setSessionValue($session_name, rand(111111, 999999));
      $this->getRequest()->session()->put($session_name, rand(111111, 999999));
    }

//    return array('SESSION_ID' => session_id(), $session_name => CJKApp::GetWebApp()->webParam()->getSessionValue($session_name),);
    return array('SESSION_ID' => session_id(), $session_name => $this->getRequest()->session()->get($session_name),);
  }


  /**
   * 用户分享增加一个级别，级别到3可以有权利抽奖励
   * @return string
   */
  public function getcookie(array $paramarr)
  {
    $method_name = __FUNCTION__;
    //填补定义参数值
    $this->setParamArr($method_name, $paramarr);
    //------------------------
    $cookie_name = 'test_cookie';
//    $cookie_val = CJKApp::GetWebApp()->webParam()->getCookieValue($cookie_name);
    $cookie_val = $this->getRequest()->cookie($cookie_name);

    if (!isset($cookie_val)) {
//      CJKApp::GetWebApp()->webParam()->setCookieValue($cookie_name, rand(111111, 999999));
//      $cookie = cookie($cookie_name, rand(111111, 999999),100);
//      $response = new Response();
//      $response->withCookie($cookie);
//      $response
    }

    return array($cookie_name => $this->getRequest()->cookie($cookie_name),);
  }

  /**
   * 测试该事件是否执行成功
   * @return string 事件如果执行成功输入linhai 则会返回中文的林海
   */
  public function testEvents(array $paramarr)
  {
    $method_name = __FUNCTION__;
    //填补定义参数值
    $this->setParamArr($method_name, $paramarr);
    $name = $this->getParamVal($method_name, 'name');
    //------------
    return $name;
  }

  /**
   * 用户分享增加一个级别，级别到3可以有权利抽奖励
   * @return string
   */
  public function getserverinfo(array $paramarr)
  {
    $method_name = 'getserverinfo';
    //填补定义参数值
    $this->setParamArr($method_name, $paramarr);
    //------------------------

    return array('HTTP_REFERER' => $this->getRequest()->headers->get('referer') ,
      'REMOTE_ADDR' => $this->getRequest()->headers->get('addr'),);
  }


  /**
   * 设置学生信息，用来测试泛参数名的支持
   * @return array
   */
  public function setStudyInfo($paramarr)
  {
    $method_name = __FUNCTION__;
    //填补定义参数值
    $this->setParamArr($method_name, $paramarr);
    $class_name = $this->getParamVal($method_name, 'class');
    $school_name = $this->getParamVal($method_name, 'school');
    $any_studycode_arr = $this->getParamValArrByRegular($method_name, '/any_studycode_.*/iU');
    $any_studyname_arr = $this->getParamValArrByRegular($method_name, '/any_studyname_.*/iU');
    $any_studyiden_arr = $this->getParamValArrByRegular($method_name, '/any_studyiden_.*/iU');
    //------------------------

    return array('class' => $class_name,
      'school' => $school_name,
      'study_code' => $any_studycode_arr,
      'study_name' => $any_studyname_arr,
      'study_iden' => $any_studyiden_arr,);

  }

  ################################
  ############ 手册支持 ###########
  ################################


  /**
   * 手册定义方法
   * @return void
   */
  public function manDefine()
  {
    parent::manDefine();

    $this->getApiDefineObj('additive')->setSampleResult($this->additive(array('a' => 7, 'b' => 9,)))->setResultdesc('非对象，数值。');
    $this->getApiDefineObj('redefadditive')->setSampleResult($this->additive(array('a' => 7, 'b' => 9,)))->setResultdesc('非对象，数值。');
    $this->getApiDefineObj('getsession')->setSampleResult($this->getsession(array()));
    $this->getApiDefineObj('getcookie')->setSampleResult($this->getcookie(array()));
    $this->getApiDefineObj('getserverinfo')->setSampleResult($this->getserverinfo(array()));
    $this->getApiDefineObj('test_events')->setSampleResult('林海')->setResultdesc('测试事件机制是否工作正常如果正常输入linhai，会返回中文的林海，而输入其他的原样返回');

    $this->getApiDefineObj('set_study_info')
      ->setSampleResult(array('class' => '大学组', 'study_code' => array(), 'study_name' => array(),))
      ->setResultdesc('COOKIES 写入成功返回true，否则返回false。');

  }

  /**
   * API 定义方法
   * $this->createApi('apiname', 'refname', 'Describe.')->setParamDefine(['key1' => ['info1' => 'desc1', 'value1' => 'default value' ], 'key2' => ['info1' => 'desc1', 'value1' => null ] ]);
   *
   * @return void
   */
  public function apiDefine()
  {
    $additive_param_arr = array();
    $additive_param_arr['a'] = array('info' => '两数相加的a 参数', 'value' => 0,);
    $additive_param_arr['b'] = array('info' => '两数相加的b 参数', 'value' => 0,);
    $this->createApi('additive', 'additive', '进行 a + b 运算的接口。')->setParamDefine($additive_param_arr);
    $this->createApi('redefadditive', 'additive', '进行 a + b 运算的接口。')->setParamDefine($additive_param_arr);
    $this->createApi('getsession', 'getsession')->setApidesc('获取Session 信息');
    $this->createApi('getcookie', 'getcookie');
    $this->createApi('getserverinfo', 'getserverinfo', '获取服务器信息');
    $this->createApi('test_events', 'testEvents', '测试事件机制是否工作正常如果正常输入linhai，会返回中文的林海，而输入其他的原样返回')
      ->setParamDefine(array('name' => array('info' => '输入的英文名称', 'value' => null,),));

    $additive_param_arr = array();
    $additive_param_arr['class'] = array('info' => '级别', 'value' => "大学组",);
    $additive_param_arr['school'] = array('info' => '级别', 'value' => null,);
    $additive_param_arr['/any_studycode_.*/iU'] = array('info' => '用户的代码', 'value' => -1, 'regular_check' => true);
    $additive_param_arr['/any_studyname_.*/iU'] = array('info' => '用户的姓名', 'value' => "无名氏", 'regular_check' => true);
    $additive_param_arr['/any_studyiden_.*/iU'] = array('info' => '用户的身份证', 'value' => null, 'regular_check' => true);
    $this->createApi('set_study_info', 'setStudyInfo', '设置学生信息。')->setParamDefine($additive_param_arr);

  }

  public function definedDesc()
  {
    return 'This is a example FEAPI 3.x class.'; // 第二代接口被迫兼容第一代接口，今后书写规范应参考二代接口。
  }

  /**
   * 定义API 的错误信息
   * @return array
   */
  public function getDefineErrors()
  {
    $error_arr = parent::getDefineErrors(); //继承父类异常信息
    //这里可以扩展本API 的异常信息
    return $error_arr;
  }


}
