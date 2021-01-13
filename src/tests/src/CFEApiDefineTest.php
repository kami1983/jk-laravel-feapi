<?php

namespace Tests\src;

use App\JkFEApiLaravel\CFEApiDefine;
use App\JkFEApiLaravel\ExceptionFEApi;
use Tests\TestCase;

class CFEApiDefineTest extends TestCase
{
    /**
     * 测试前端API 实例
     * @return void
     */
    public function testCFEApiDefine()
    {
        #参数

        #参数 END
        #主题

        $oper = new CFEApiDefine();
        $this->assertCount(0, $oper->getApiname());
        $this->assertContains('GET', $oper->getLimitMethod(), '判断是否包含GET，的请求限制');
        $this->assertContains('POST', $oper->getLimitMethod(), '判断是否包含POST，的请求限制');
        $this->assertNotContains('PUT', $oper->getLimitMethod(), '判断是否包含PUT，的请求限制，默认上不包含');

        $this->assertInstanceOf(CFEApiDefine::class, $oper->setLimitMethod(['PUT']), '设置PUT限制参数，这样保证该定义仅仅在PUT上生效');

        $this->assertInstanceOf(CFEApiDefine::class, $oper->setApiname(['myapi']));
        $this->assertEquals('myapi', $oper->getApiname()[0]);

        $this->assertInstanceOf(CFEApiDefine::class, $oper->setRefname(array($oper, 'getApiname')));

        $ref_arr = $oper->getRefname();
        $this->assertCount(2, $ref_arr);
        $this->assertEquals('getApiname', $ref_arr[1]);

        $this->assertCount(0, $oper->getParamDefine()); //没有任何参数定义

        #主题 END
    }

    /**
     * 测试设置参数定义时没有设置info 的情况
     */
    public function testCFEApiDefineExcepitonOfNoInfo()
    {
        $this->expectExceptionCode(CFEApiDefine::CONST_NEWERR_参数定义信息中必须包含INFO键值);
        $oper = new CFEApiDefine();
        $oper->setParamDefine(array('a' => array('discrip' => '两数相加的a 参数', 'value' => null,),));
    }


}
