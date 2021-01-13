<?php
namespace KLib\FEApiLaravel;

/**
 * 前后端访问控制接口
 * @author linhai
 */
interface IFEApiAssessControl {

    /**
     * 验证手测查看密码是否正确
     * @return boolean
     */
    function verifyPasswordOfMan();

    /**
     * 获取API 手册的查看密码
     * @return null 为null 是表示忽略密码功能
     */
    function getPasswordOfMan();

    /**
     * 签入验证的密码信息用于之后的验证。
     * @return IFEApiAssessControl
     */
    function signinVerifyPassword($password);

    /**
     * 对某域名是否开启CORS 功能
     * @param string $url 来源地址，http://www.baidu.com/a.html
     * @return boolean
     */
    function isOpenCORS($url);

    /**
     * 对某域名是否开启CORS 功能
     * @param string $url 来源地址，http://www.baidu.com/a.html
     * @return boolean
     */
    function isOpenJSONP($url);

    /**
     * Ref url 过滤器如果出现问题则直接异常
     * @param string $ip 来源URL
     * @return AbsFEApiParamMan
     */
    function filterIP($ip);

    /**
     * Ref url 过滤器如果出现问题则直接异常
     * @return IFEApiAssessControl
     */
    function filterRule();

    /**
     * Ref url 过滤器如果出现问题则直接异常
     * @param string $refurl 来源URL
     * @return IFEApiAssessControl
     */
    function filterRef($refurl);


    /**
     * 返回应用的上线时间戳，如果不设定上线时间戳返回 -1
     * 否则，这个时间应该大于0，以避免错误。
     * @return int
     */
    function getTimeStampOfUp();

    /**
     * 设置应用的上线时间戳，如果不设定上线时 timestamp 设定为 -1
     * 否则，这个时间应该大于0，以避免错误。
     * @param int $timestamp 时间戳，带入-1 标识不设定上线时间
     * @return IFEApiAssessControl
     */
    function setTimeStampOfUp($timestamp);

    /**
     * 返回应用的下线时间戳，如果不设定下线时间戳返回 -1
     * 否则，这个时间应该大于0，以避免错误。
     * @return int
     */
    function getTimeStampOfDown();

    /**
     * 设定应用的下线时间戳，
     * @param int $timestamp 时间戳，带入-1 标识不设定下线时间
     * @return IFEApiAssessControl
     */
    function setTimeStampOfDown($timestamp);


    /**
     * 根据正则对参数进行分组
     * @return array
     */
    function getParamValArrByRegular($method,$regular_str);





}
