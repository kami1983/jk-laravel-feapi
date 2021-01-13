<?php

namespace Tests\JkFEApiLaravel;

use App\JkFEApiLaravel\ExceptionFEApi;
use phpDocumentor\Reflection\Types\False_;
use Tests\TestCase;

class ExceptionFEApiTest extends TestCase
{
    /**
     * 假定当调用Call方法时，对应的ic名称不存在时的错误
     * @return ExceptionFEApi
     */
    public function testCeateFEException()
    {
        $oper = ExceptionFEApi::CreateErr("测试异常", "2012232236", "TEST_SIGN");
        $this->assertEquals("2012232236", $oper->getCode() );
        $this->assertEquals("测试异常", $oper->getMessage());
        $this->assertEquals("TEST_SIGN", $oper->getSign());

        return $oper;
    }

    /**
     * 测试异常抛出
     * @depends testCeateFEException
    */
    public function testExcepitonThrow(\Exception $exception) {

        $this->expectException(ExceptionFEApi::class);

        $this->assertEquals("2012232236", $exception->getCode() );
        $this->assertEquals("测试异常", $exception->getMessage());
        $this->assertEquals("TEST_SIGN", $exception->getSign());

        throw $exception;
    }

}
