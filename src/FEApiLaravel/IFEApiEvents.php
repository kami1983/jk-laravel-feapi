<?php
namespace KLib\FEApiLaravel;

/**
 * FE接口事件支持接口
 * @author linhai
 */
interface IFEApiEvents {

    /**
     * （过时的方法）调用触发器方法
     * @param string $callmethod 被调用方法
     * @param array &$paramarr 被调用方法带入的参数
     * @return mixed
     */
    function beforeCallTrigger( $callmethod,array &$paramarr);

    /**
     * （过时的方法）调用触发器方法之后
     * @param string $callmethod 被调用方法
     * @param array $paramarr 被调用方法带入的参数
     * @param mixed $result 返回的结果
     * @return mixed
     */
    function afterCallTrigger( $callmethod,array $paramarr, $result);

    /**
     * 调用触发器方法
     * @param string $callmethod 被调用方法
     * @param array &$paramarr 被调用方法带入的参数
     * @return mixed
     */
    function eventBeforeCallApi(CFEApiDefine $apidefineobj,array &$paramarr);

    /**
     * 调用触发器方法之后
     * @param string $callmethod 被调用方法
     * @param array &$paramarr 被调用方法带入的参数
     * @param mixed $result 返回的结果
     * @return mixed
     */
    function eventAfterCallApi(CFEApiDefine $apidefineobj,array &$paramarr, $result);

}
