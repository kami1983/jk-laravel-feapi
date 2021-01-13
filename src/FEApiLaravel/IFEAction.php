<?php

namespace KLib\FEApiLaravel;

/**
 * FE 组件Action 共有接口
 *
 * @author linhai
 */
interface IFEAction {

    /**
     * 允许API 的列表
     * @return array
     */
    function setAllowApis(array $list);

    /**
     * 判断某API 是否在允许列表中
     * @return boolean
     */
    function isAllowApis($apiname);
}
