<?php
namespace KLib\FEApiLaravel;

/**
 * 错误类接口
 * @author hcl
 */
interface IFEErrControl {




    /**
     * 添加错误
     */
    function addErr($codenum,$errinfo);


    /**
     * 获取错误标识
     * return IFEErrControl
     */
    function getErrSign();

     /**
     * 抛出异常
     * @param int $codenum 错误号
     * return string
     */
    function throwErr($codenum,$attach_msg);

     /**
     * 返回上层错误类
     * return IFEErrControl
     */
    function errParent();



     /**
     * 新建错误类
     * return IFEErrControl
     */
    function createSubErrControl($errsign);




}
