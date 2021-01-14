## jk-laravel-feapi

* 一个简单的支持RESTful接口的前后端组件模块，从JKGlib框架下移植到Laravel框架。
* 支持一个简单的自动在线API接口文档，方便前后端开发人员进行对接。
* 该组件7.x版本支持Laravel5.5.* 版本

## 版本

* v7.0.1 基础版本，刚刚从JKGLib 框架中移植过来。
* v7.0.2 新增README.md 新增 CFEApiViewProvider 类。

## 安装

* 通过composer 进行安装 `composer require klib/jk-laravel-feapi` 。
* 安装后需要进行简单的配置。

### 创建 config/feapi.php 配置文件
* 这里面定义的是 AbsFEApiParamMan 的子类，也就是具体实现类。
* 该文件内容如下：（距离中只有一个CFEApiExample）
```
<?php

return [

    'apps' => [
        # 类名称 => 具体位置，CFEApiExample 是一个测试样例
        'example' => 'KLib\FEApiLaravel\CFEApiExample',
    ],

];

```

### 注入视图提供者
* 修改Laravel 项目的 config/app.php
```
# 找到 providers 数组定义区块，新增条目，只有这样手册的视图才能被正常解析。
KLib\FEApiLaravel\Providers\CFEApiViewProvider::class,

```

### 新增接口路由
* 因为当前版本组件并没有整合路由（稍后会整合），所以还需要在 routes/web.php 中添加路由才可以使用接口。

```
// API 程序入口，URL 中需要传入3个参数，分别是 pid【平台id】, ic【对应的API名称】, im 【对应的方法名称】
// for example: /api?ic=example&im=additive&fps[a]=5&fps[b]=6
Route::any('/api', function (Request $request, Application $app) {
    return CFEApiScheduler::GetResult($request, $app);
})->name('feapi');


Route::any('/man', function (Request $request, Application $app) {
    $feobj = CFEApiScheduler::CreateApi($request, $app);

    if($request->isMethod('post')) {
        // 如果有post 值，有可能是提交了手册的密码
        $inpwd = $request->input('verfiypassword');
        // 登录
        $feobj->signinVerifyPassword($inpwd);

    }

    return view('feapi::man', ['api_obj' => $feobj, 'api_routername'=>'feapi']);
})->name("feman");

```

## 使用

* AbsFEApiParamMan 接口子类，所有接口子类格式大致如下：

```
<?php

namespace App;

use KLib\FEApiLaravel\AbsFEApiParamMan;
use KLib\FEApiLaravel\IHaveErr;

class CFEExamApi extends AbsFEApiParamMan implements IHaveErr
{

    /**
     * 进行API 定义
     * $this->createApi('apiname', 'refname', 'Describe.')->setParamDefine(['key1' => ['info1' => 'desc1', 'value1' => 'default value' ], 'key2' => ['info1' => 'desc1', 'value1' => null ] ]);
     * @return AbsFEApiParamMan
     */
    public function apiDefine()
    {
        $param_arr = [];
        $param_arr['a'] = ['info' => 'desc1', 'value' => 0 ];
        $param_arr['b'] = ['info' => 'desc2', 'value' => 0 ];

        $this->createApi('tryname', 'realname', '这只是一个测试用的API.')
            ->setLimitMethod(['GET', 'POST'])
            ->setParamDefine($param_arr);

    }

    /**
     * 设置文档登录的密码
     * @return string
    */
    public function getPasswordOfMan()
    {
        return 'abc123';
    }

    /**
     * 定义手册信息
     * @return AbsFEApiParamMan
    */
    public function manDefine() {
        parent::manDefine();
        $this->getApiDefineObj('tryname')->setSampleResult($this->realname([5,6]))->setResultdesc('这只是一个测试');
    }

    /**
     * 这是一个测试用的Api接口
     * @param $param_arr API参数数组
     * @return mixed
     */
    public function realname($param_arr) {
        return $param_arr;
    }
}

```
