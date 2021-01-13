<?php

namespace Tests\JkFEApiLaravel;

use App\JkFEApiLaravel\AbsFEApiParamMan;

use App\JkFEApiLaravel\CFEApiDefine;
use App\JkFEApiLaravel\CFEApiScheduler;
use App\JkFEApiLaravel\CFEResult;
use App\JkFEApiLaravel\CFEResultSuccess;
use App\JkFEApiLaravel\ExceptionFEApi;
use App\JkFEApiLaravel\IFEApiAssessControl;
use App\JkFEApiLaravel\IFEApiEvents;
use App\JkFEApiLaravel\IFEApiOperControl;
use App\JkFEApiLaravel\IHaveErr;
use App\JkFEApiLaravel\tests\CFEApiExample;

//use Illuminate\Support\Facades\Request;
use Illuminate\Http\Request;
use Illuminate\Session\SessionManager;
use Illuminate\Session\Store;
use Illuminate\Support\Facades\Session;
use phpDocumentor\Reflection\Types\False_;

use Tests\TestCase;

class AbsFEApiParamManTest extends TestCase
{

  public function testCreateApiMan()
  {

//    $oper = new CFEApiExample();
    $oper = $this->app->make(CFEApiExample::class);
    $this->assertInstanceOf("App\JkFEApiLaravel\AbsFEApiParamMan", $oper);
    //IFEApiAssessControl, IFEApiOperControl, IFEApiEvents,IHaveErr
    $this->assertInstanceOf("App\JkFEApiLaravel\IFEApiOperControl", $oper);
    $this->assertInstanceOf("App\JkFEApiLaravel\IFEApiAssessControl", $oper);
    $this->assertInstanceOf("App\JkFEApiLaravel\IFEApiEvents", $oper);
    $this->assertInstanceOf("App\JkFEApiLaravel\IHaveErr", $oper);

    // 返回测试类实例，给其他测试方法使用。
    return $oper;
  }


  /**
   * 测试CallApi 方法调用
   * @depends testCreateApiMan
   */
  public function testCallApi(AbsFEApiParamMan $feapi)
  {
    $this->assertEquals('林海', $feapi->callApi('test_events', array('name' => 'linhai',)));
  }


  /**
   * 测试通过Get 表单调用API 执行
   */
  public function test通过Get_Request触发Api调用()
  {
    #参数

    #参数 END
    #主题

    //设置名称
//        CJKApp::GetWebApp()->webParam()->setGetValue('ic','example');
//        CJKApp::GetWebApp()->webParam()->setGetValue('im','test_events'); //调用不存在的别名，不应该出错
//        CJKApp::GetWebApp()->webParam()->setGetValue('fps',array('name'=>'linhai',));

    $get_param = [
      'ic' => 'example',
      'im' => 'test_events',
      'fps' => ['name' => 'linhai']
    ];

//        $this->server->get('REQUEST_URI');

    // ['HTTP_REFERER'=>'http://jk-frame.git.cancanyou.com/test_index.php?r=testing/frontendapi', ]
    // 创建一个请求变量
    $request = new Request($get_param);
    $result_json = CFEApiScheduler::GetResult($request, $this->app);
    $result_obj = json_decode($result_json);
    $this->assertEquals(CFEResult::CONST_RESULT_STATUS_SUCCESS, $result_obj->result);
    $this->assertEquals('林海', $result_obj->back_value);

    #主题 END
  }

  /**
   * 测试通过Get 表单调用API 执行
   */
  public function test通过Post_Request触发Api调用()
  {
    #参数

    #参数 END
    #主题

    $post_param = [
      'ic' => 'example',
      'im' => 'test_events',
      'fps' => ['name' => 'linhai']
    ];

//        $this->server->get('REQUEST_URI');

    // ['HTTP_REFERER'=>'http://jk-frame.git.cancanyou.com/test_index.php?r=testing/frontendapi', ]
    // 创建一个请求变量
    $request = new Request([], $post_param);
    $result_json = CFEApiScheduler::GetResult($request, $this->app);
    $result_obj = json_decode($result_json);
    $this->assertEquals(CFEResult::CONST_RESULT_STATUS_SUCCESS, $result_obj->result);
    $this->assertEquals('林海', $result_obj->back_value);

    #主题 END
  }

  /**
   *
   */
  public function testCFEApiExample_密码功能测试支持()
  {
    #参数

    #参数 END
    #主题

    /** @var $store \Illuminate\Session\Store */// 构造一个存储器
    $store = $this->app->make("Illuminate\Session\Store");
    $request = new Request();
    $request->setLaravelSession($store);
    $request->session()->put(['name' => "linhai"]);


    $oper = new CFEApiExample($request);
    // 通过自动注入方式创建的Api，会有点问题，Request 中没有Session.
//    $oper = $this->app->make(CFEApiExample::class);

    /* @var $oper \App\JkFEApiLaravel\tests\CFEApiExample */
    // 必须要设置Request 对象，如果不设置后面signin 是会出现错误
    $this->assertEquals("456", $oper->getPasswordOfMan(), '判断手册中定义的密码');
    $this->assertFalse($oper->verifyPasswordOfMan(), '因为根本没有填写密码'); //验证失败
    $this->assertInstanceOf(CFEApiExample::class, $oper->signinVerifyPassword("123")); //错误的密码设置
    $this->assertFalse($oper->verifyPasswordOfMan(), "验证失败，因为密码错误，密码是456"); //
    $this->assertInstanceOf(CFEApiExample::class, $oper->signinVerifyPassword("456")); //正确的密码设置
    $this->assertTrue($oper->verifyPasswordOfMan()); //验证成功

    #主题 END
  }

  /**
   * 测试前端API 实例
   * @return void
   */
  public function testCFEApiExample_扩展API定义新特性()
  {
    #参数

    #参数 END
    #主题

//    $oper = new CFEApiExample();
    $oper = $this->app->make(CFEApiExample::class);
    $param_arr = array();
    $param_arr['convert'] = array('info' => '要进行转换为大写的文字', 'value' => null,);
    $this->assertInstanceOf(CFEApiDefine::class, $oper->createApi('testapi')->setParamDefine($param_arr)->setSampleResult('七'));

    $apiparam_arr = $oper->getParamArr();
//    print_r($apiparam_arr);
    $this->assertTrue(is_array($apiparam_arr['testapi']));
    $this->assertTrue(is_array($apiparam_arr['testapi']['GET']['params']['convert']));
    $this->assertEquals('要进行转换为大写的文字', $apiparam_arr['testapi']['GET']['params']['convert']['info']);
    $this->assertNull($apiparam_arr['testapi']['GET']['params']['convert']['value']);

    //進行模擬設置
    $this->assertInstanceOf(CFEApiExample::class, $oper->setParamArr('testapi', array('convert' => '6',)));
    $this->assertEquals('6', $oper->getParamVal('testapi', 'convert'));

    //模擬ref 參數
    $param_arr = array();
    $param_arr['convert'] = array('info' => '要进行转换为拼音的字符', 'value' => '拼',);
    $this->assertInstanceOf(CFEApiDefine::class, $oper->createApi('convert_pinyin', 'convertPinYin')->setParamDefine($param_arr)->setSampleResult('qi'));
    $apiparam_arr = $oper->getParamArr();
    $this->assertTrue(is_array($apiparam_arr['convert_pinyin']));
    $this->assertTrue(is_array($apiparam_arr['convert_pinyin']['GET']['params']['convert']));
    $this->assertEquals('要进行转换为拼音的字符', $apiparam_arr['convert_pinyin']['GET']['params']['convert']['info']);
    $this->assertEquals('拼', $apiparam_arr['convert_pinyin']['GET']['params']['convert']['value']);

    $this->assertEquals('拼',$oper->getParamVal('convertPinYin', 'convert'));

    return $oper;


//


    #主题 END


  }

  /**
   * 测试前端API 实例
   * @return void
   * @depends testCFEApiExample_扩展API定义新特性
   */
  public function testCFEApiExample不可以通过引用参数获取参数(CFEApiExample $oper) {
    $this->expectException(ExceptionFEApi::class);
    $this->expectExceptionCode(CFEApiExample::CONST_NEWERR_方法名称未定义);
    $oper->getParamVal('convert_pinyin', 'convert'); //錯誤的原因是在進行參數取值的操作時，函數調用的是引用定義對象。
  }

  /**
   * 测试前端API 实例
   * @return void
   * @depends testCFEApiExample_扩展API定义新特性
   */
  public function testCFEApiExample调用不存在的方法会抛出异常(CFEApiExample $oper) {
    $this->expectException(ExceptionFEApi::class);
    $this->expectExceptionCode(CFEApiExample::CONST_NEWERR_方法名称未定义);
    $oper->callApi('addnum', array('a' => 1, 'b' => 2));
  }

  /**
   * 测试前端API 实例
   * @return void
   * @depends testCFEApiExample_扩展API定义新特性
   */
  public function testCFEApiExample故意搞错一个参数让其出现异常(CFEApiExample $oper) {

    $this->expectException(ExceptionFEApi::class);
    $this->expectExceptionCode(CFEApiDefine::CONST_NEWERR_参数错误);

    // 对于addnum 可以进行关联定向让其生效
    $additive_param_arr = array();
    $additive_param_arr['a'] = array('info' => '两数相加的a 参数', 'value' => 0,);
    $additive_param_arr['b'] = array('info' => '两数相加的b 参数', 'value' => 0,);
    // 添加 addnum 方法
    $this->assertInstanceOf(CFEApiDefine::class, $oper->createApi('addnum', 'additive')->setParamDefine($additive_param_arr));

    //故意搞错一个参数，实际上并没有定义参数c，只有参数a、b
    $this->assertEquals(3, $oper->callApi('addnum', array('a' => 1, 'c' => 2,)));//

  }

  /**
   * 测试前端API 实例
   * @return void
   * @depends testCFEApiExample_扩展API定义新特性
   */
  public function testCFEApiExample新增AddNum方法(CFEApiExample $oper) {

    // 对于addnum 可以进行关联定向让其生效
    $additive_param_arr = array();
    $additive_param_arr['a'] = array('info' => '两数相加的a 参数', 'value' => 0,);
    $additive_param_arr['b'] = array('info' => '两数相加的b 参数', 'value' => 0,);
    $this->assertInstanceOf(CFEApiDefine::class, $oper->createApi('addnum', 'additive')->setParamDefine($additive_param_arr));
    $this->assertEquals(3, $oper->callApi('addnum', array('a' => 1, 'b' => 2,)));

  }


//
//    public function testApiDefine()
//    {
//
//    }
//
//    public function testGetPasswordOfMan()
//    {
//
//    }
//
//    public function testGetErrorInfo()
//    {
//
//    }
//
//    public function testSetTimeStampOfDown()
//    {
//
//    }
//
//    public function testGetTimeStampOfDown()
//    {
//
//    }
//
//    public function testEventBeforeCallApi()
//    {
//
//    }
//
//    public function testVerifyPasswordOfMan()
//    {
//
//    }
//
//    public function testBeforeCallTrigger()
//    {
//
//    }
//
//    public function testManDefine()
//    {
//
//    }
//
//    public function testGetSimpleResult()
//    {
//
//    }
//
//    public function testGetUrlGetParam()
//    {
//
//    }
//
//    public function testGetParamValArrByRegular()
//    {
//
//    }
//
//    public function testFilterRule()
//    {
//
//    }
//
//    public function testDefinedDesc()
//    {
//
//    }
//
//    public function testFillManErr()
//    {
//
//    }
//
//    public function testEventAfterCallApi()
//    {
//
//    }
//
//    public function testGetTimeStampOfUp()
//    {
//
//    }
//
//    public function testFilterIP()
//    {
//
//    }
//
//    public function testGetErrSign()
//    {
//
//    }
//
//    public function testDefineErr()
//    {
//
//    }
//
//    public function testGetApiDefineObj()
//    {
//
//    }
//
//    public function testSetParamArr()
//    {
//
//    }
//
//    public function testGetDefineErrors()
//    {
//
//    }
//
//    public function testSigninVerifyPassword()
//    {
//
//    }
//
//    public function testAfterCallTrigger()
//    {
//
//    }
//
//    public function testIsOpenCORS()
//    {
//
//    }
//
//    public function testSetTimeStampOfUp()
//    {
//
//    }
//
//    public function testCreateApi()
//    {
//
//    }
//
//    public function testCallApi()
//    {
//
//    }
//
//    public function testGetApiDefineArr()
//    {
//
//    }
//
//    public function testGetAllParamValArr()
//    {
//
//    }
//
//    public function testGetParamArr()
//    {
//
//    }
//
//    public function testInit()
//    {
//
//    }
//
//    public function testIsOpenJSONP()
//    {
//
//    }
//
//    public function testGetParamVal()
//    {
//
//    }
//
//    public function testFilterRef()
//    {
//
//    }
//
//    public function testIsApiExists()
//    {
//
//    }
//
//    public function testThrowErr()
//    {
//
//    }
}
