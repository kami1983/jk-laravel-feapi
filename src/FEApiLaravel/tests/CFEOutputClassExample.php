<?php
namespace App\JkFEApiLaravel\tests;

use App\JkFEApiLaravel\IFEOutputClass;

/**
 * Description of CFEOutputClassExample
 * 测试用的输出类实例
 * 
 * @author linhai
 */
class CFEOutputClassExample implements IFEOutputClass {
    
    public $public_value="public value.";
    private $_private_value="private value.";


    /**
     * 选择性质输出
     * @return mixed
     */
    public function outputFEClass(){
        return array('publicval'=>$this->public_value,'privateval'=>$this->_private_value,);
    }
    
    
}
