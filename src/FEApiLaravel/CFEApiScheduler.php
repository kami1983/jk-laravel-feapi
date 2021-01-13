<?php


namespace KLib\FEApiLaravel;


use Illuminate\Http\Request;
use Illuminate\Foundation\Application;
use phpDocumentor\Reflection\DocBlock\DescriptionFactory;

class CFEApiScheduler
{

    const CONST_ERROR_CLASS_NOTFOUND = '1000854';
    const CONST_ERROR_UNDEFINED_METHOD = '1100853';


    /**
     * 单件类
     */
    protected function __construct()
    {

    }

    /**
     * 获取配置文件数组
     * @return array
     */
    public static function GetConfigArr()
    {
        // 尝试从配置文件中加载中间件
        $apps_arr = config('feapi.apps');
        if (!is_array($apps_arr)) {
            // 如果不是数组，整理成数组
            $apps_arr = [];
        }
        return $apps_arr;
    }

    /**
     * 创建Api 对象。
     * @param Request $request
     * @param Application $app
     * @return AbsFEApiParamMan
     */
    public static function CreateApi(Request $request, Application $app) {
        CFEErr::AddException(__CLASS__, self::CONST_ERROR_CLASS_NOTFOUND, 'Need ic param.');
        CFEErr::AddException(__CLASS__, self::CONST_ERROR_UNDEFINED_METHOD, 'Need im param.');

        $ic = trim($request->get('ic', ''));

        if ('' == $ic) {
            CFEErr::ThrowErr2(__CLASS__, self::CONST_ERROR_CLASS_NOTFOUND, 'Need ic param.');
        }

        // 获取配置App的数组
        $apps_arr = self::GetConfigArr();
        if (!array_key_exists($ic, $apps_arr)) {
            CFEErr::ThrowErr2(__CLASS__, self::CONST_ERROR_CLASS_NOTFOUND, 'You can try to add the configuration of IC interface in `config/feapi.php`.');
        }

        $feapi_name = $apps_arr[$ic];
        // 初始化中间件类。
//        $feapi_reflection = new \ReflectionClass($feapi_name);
        // 初始化API
//        $feapi_obj = $feapi_reflection->newInstance();

        // 通过注入方式创建API
        $feapi_obj = $app->make($feapi_name);
        $feapi_obj->setApiName($ic);
        return $feapi_obj;
    }

    /**
     * 用来执行调度的入口方法，接收一个 $call_param 可变参数数组，来决定Api具体执行的类。
     * @param array $call_param
     */
    public static function Call(Request $request, Application $app)
    {

        $feapi_obj = self::CreateApi($request, $app) ;
        $im = trim($request->get('im', ''));
        $fps = $request->get('fps', []);

        /* @var $feapi_obj AbsFEApiParamMan */
        return $feapi_obj->callApi($im, $fps);
    }

    /**
     * 获取API返回结果这个结果是一个JSON数据
     * @param array $call_param
     */
    public static function GetResult(Request $request, Application $app)
    {

        try {
            $result = self::Call($request, $app);
            $resultobj = new CFEResultSuccess($result, $request->method());
//            $resultobj->setMethod($request->method());
            return json_encode($resultobj);

        } catch (ExceptionFEApi $exception) {
            $resultobj = new CFEResultFailure($exception, $request->method());
//            $resultobj->setMethod($request->method());
            return json_encode($resultobj);
        }
    }
}
