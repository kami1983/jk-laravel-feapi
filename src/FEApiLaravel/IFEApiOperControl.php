<?php
namespace KLib\FEApiLaravel;

/**
 * API 操作控制接口，里面定义了所有API 操作
 * @author linhai
 */
interface IFEApiOperControl {

    /**
     * 查找某个API的别名是否存在
     * @param string $api_aliases_name API别名
     * @return boolean
     */
    function isApiExists($api_aliases_name, $method='GET');

    /**
     * Call 函数用来整合调用方法，从而支持触发器。
     * @param string $apiname 被调用的API 名称
     * @param array $paramarr 参数名称
     * @return mixed
     */
    function callApi($apiname,array $paramarr=array());

    /**
     * 创建一个API 定义对象
     * @param string $apiname API名称
     * @param mixed $refname 引用名称，有可能是string，也有可能是数组
     * @return CFEApiDefine
     */
    function createApi($apiname,$refname=null,$desc=null);

    /**
     * 获取API 定义数组
     * @return CFEApiDefine[]
     */
    function getApiDefineArr();

    /**
     * 获取一个API 定义对象
     * @return CFEApiDefine
     */
    function getApiDefineObj($apiname);

    /**
     * 获取参数数组
     * @return array
     */
    function getParamArr();

    /**
     * Set param arr with array
     * @param string $method
     * @param array $paramarr
     * @return IFEApiOperControl
     */
    function setParamArr($method,array $paramarr);

    /**
     * 获取所有的参数值，数组
     * @param string $method 被调用的参数名称
     * @return array
     */
    function getAllParamValArr($method);

    /**
     * 获取某个参数的值
     * @param string $method
     * @param string $paramname
     * @return string
     */
    function getParamVal($method,$paramname);
}
