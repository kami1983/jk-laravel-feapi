<?php

namespace Tests\src;




use KLib\FEApiLaravel\CFEErr;
use Tests\TestCase;

class CFEErrTest extends TestCase
{

  public static function setUpBeforeClass()
  {
    // 清除异常定义信息
    CFEErr::CleanDefineErr();
  }

  /**
   * 测试异常列表数组，断言基础异常信息正确
   * @return void
   */
  public function testGetErrList()
  {

    $errlist = CFEErr::GetErrList();
    $this->assertCount(1, $errlist, '默认只有一个数组元素也就是未定义的异常信息的异常');

    // 获取数组的第一个元素，也是唯一的元素进行验证判断
    $first_cell = array_shift($errlist);

    $this->assertEquals(CFEErr::CONST_NEWERR_NOT_DEFINED_ERRORCODE, $first_cell["code"]);
    $this->assertEquals('错误码未定，错误码格式是sign::code', $first_cell["msg"]);
    $this->assertEquals(CFEErr::class, $first_cell["sign"]);

    // 测试添加一个，异常信息
    CFEErr::AddException(__CLASS__, '2012282227', '在测试中临时添加一个异常信息');
    $errlist = CFEErr::GetErrList();
    $this->assertCount(2, $errlist, '算上异常类自己定义的异常，已经有两个异常值了');

  }

  /**
   * 测试抛出一个异常信息
   * 这里就抛出上面 testGetErrList 定义的 2012282227 异常
   * @depends testGetErrList
   */
  public function testThrowException()
  {
//      print_r(get_class($this->app));

    $this->expectExceptionCode('2012282227');
    CFEErr::ThrowErr2(__CLASS__, '2012282227', '附加信息');
  }
}
