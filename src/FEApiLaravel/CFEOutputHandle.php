<?php

namespace KLib\FEApiLaravel;

/**
 * Description of CFEOutputHandle
 *
 * 输出助理，该API 用于 辅助转换一个数组进行数组内的对象输出转换工作。
 *
 * @author linhai
 */
class CFEOutputHandle {


    /**
     * 从一个对象中进行输出
     * @return string
     */
    public static function OutputFromObj($obj){

        //如果是标准的输出接口对象
        if($obj instanceof IFEOutputClass){
            return $obj->outputFEClass();
        }

        if(is_object($obj)){
            return get_object_vars($obj);
        }

        return  $obj; //否则直接返回
    }

    /**
     * 从一个数组中进行格式化输出
     * RUN 1 RUN 3 RUN 5 RUN 1 RUN 2 RUN 3 RUN 5
     * @return array
     */
    public static function OutputFromArr(array $arr){
        $result_arr=array();
        foreach($arr as $key=>$value){
            $tmpval=$value;

            if(is_object($value)){
                $tmpval=self::OutputFromObj($value);
            }

            //如果是数组则进行递归
            if(is_array($value)){
                $tmpval=self::OutputFromArr($value);
            }
            $result_arr[$key]=$tmpval;
        }

        return $result_arr;
    }

}
