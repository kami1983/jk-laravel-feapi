<?php
namespace KLib\FEApiLaravel;

/**
 * 判断操作
 * @author hcl
 */
interface IFEApiExeture {

    /**
     * 判断Api名称和执行
     * @param array $paramarr 定义的参数数组
     * return mixed
     */
    function execute(array $paramarr);

}
