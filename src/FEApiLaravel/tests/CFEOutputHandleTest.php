<?php

namespace Tests\JkFEApiLaravel;

use App\JkFEApiLaravel\CFEOutputHandle;
use App\JkFEApiLaravel\tests\CFEOutputClassExample;
use Tests\TestCase;

class CFEOutputHandleTest extends TestCase
{
    public function testOutputHandle() {
        #参数

        #参数 END
        #主题

        $outputarr= CFEOutputHandle::OutputFromObj(new CFEOutputClassExample());
        $this->assertEquals('public value.', $outputarr['publicval']);
        $this->assertEquals('private value.', $outputarr['privateval']);

        $tmpobj=new \stdClass();
        $tmpobj->name='linhai';
        $tmpobj->age='32';

//        $tmpobj = ["name"=>"linhai", "age"=>'32'];

        $outputarr= CFEOutputHandle::OutputFromObj($tmpobj);
        $this->assertEquals('linhai', $outputarr['name']);
        $this->assertEquals('32', $outputarr['age']);

        $mycare=new \stdClass();
        $mycare->price=120000;
        $mycare->name='C4';

        $tmparr=array();
        $tmparr['tablename']='user table.';
        $tmparr['personal']=$tmpobj;
        $tmparr['other']=array('location'=>'live in beijing','car'=>$mycare,);

        /**
         *  [0] => user table.
        [1] => Array
        (
        [name] => linhai
        [age] => 32
        )

        [2] => Array
        (
        [year] => live in beijing
        [car] => Array
        (
        [price] => 120000
        [name] => C4
        )

        )
         */

        $result_arr=CFEOutputHandle::OutputFromArr($tmparr);
        $this->assertEquals('user table.', $result_arr['tablename']);
        $this->assertEquals('linhai', $result_arr['personal']['name']);
        $this->assertEquals('32', $result_arr['personal']['age']);
        $this->assertEquals('live in beijing', $result_arr['other']['location']);
        $this->assertEquals('120000', $result_arr['other']['car']['price']);
        $this->assertEquals('C4', $result_arr['other']['car']['name']);

        //DEBUG 测试 数值输入后出错
        $this->assertEquals(19, CFEOutputHandle::OutputFromObj(19));
        $this->assertEquals('ABC', CFEOutputHandle::OutputFromObj('ABC'));

        #主题 END
    }
}
