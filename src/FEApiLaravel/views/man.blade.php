<?php
use KLib\FEApiLaravel\CFEErr;
use KLib\FEApiLaravel\AbsFEApiParamMan;
use KLib\FEApiLaravel\CFEOutputHandle;

################################ VIEW 必须有如下注释，请不要删除并及时维护其与Controller 层的关联性
/* @var $api_obj AbsFEApiParamMan */


/* @var $api_routername string *///API 所在的路由名称
/* @var $api_routerparam array *///API 所在的参数数组

if(!is_array($api_routerparam)) {
  $api_routerparam= [];
}

$api_name = $api_obj->getApiName();//$this->webparamObj->getGetValue('api_name');
$error_arr = $api_obj->getDefineErrors();
$route_name = $api_routername;
// 初始化手册定义
$api_obj->manDefine();

//获取异常信息的数组
$exception_feapi_arr = CFEErr::GetErrList();

?>
<html>
<head>
  <title>FEAPI - </title>
  <!-- 最新版本的 Bootstrap 核心 CSS 文件 -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@3.3.7/dist/css/bootstrap.min.css"
        integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@3.3.7/dist/js/bootstrap.min.js"
          integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa"
          crossorigin="anonymous"></script>

  <style>

  </style>
</head>
<body>

<div class="container-fluid">

  <?php if($api_obj->verifyPasswordOfMan()): ?>

  <h2>Api - <?php echo ucfirst($api_name);?></h2>
  <div class="alert alert-success" role="alert">
    <?php echo $api_obj->definedDesc(); ?>
    当前请求头：<?php echo htmlentities(route($route_name, $api_routerparam)); ?>
  </div>
  <div class="alert alert-warning" role="alert">
    默认情况下接口不开启JSONP 和CORS ，如果要使用这些功能请与开发人员沟通，另外仅fps 参数支持GET/POST 请求，其余参数仅支持GET 请求，但是JSONP 并不支持POST 请求，也请注意CORS
    请求方式的浏览器兼容问题。
  </div>
<!--<script>
    $.getJSON("http://douzhanshen.dev.cancanyou.com/test_load_index.php?r=<?php echo $api_routername;?>&iv=jsonp&ic=dzp&im=getdone&fps[open_id]=oECUds_8NqEpPTDQsCVnvWKNcvKU&callback=?",function(data){
        alert(data['result']);
    });
</script>-->
  <hr/>
  <h2>Api list</h2>
  <table class="table table-bordered">

    <?php foreach($api_obj->getParamArr() as $method=>$param_arr): $first_td = true; ?>

    <?php if($first_td) : $first_td = false; ?>
    <?php
    // 计算表格行高，用来展示表格
    //
    $table_row_num = count($param_arr) * 5;
    $table_hight_num = 0;
//    echo $table_row_num;
    ?>
    <?php foreach ($param_arr as $sub_params) : ?>
          <?php $table_hight_num += count($sub_params['params']); ?>
          <?php endforeach;   ?>
    <tr>
      <td valign="top" style="text-align: center;" rowspan="<?php echo $table_row_num + $table_hight_num + 1; ?>"><h3
          style="color:#a94442;"><?php echo $method ?></h3>
      </td>
      <td colspan="3"><strong><?php echo $method ?> 对的应请求方法 </strong></td>
    </tr>
    <?php endif; ?>

    <?php foreach($param_arr as $param_name=>$param_info_arr): ?>
    <?php

    $param_str = '';

    foreach ($param_info_arr['params'] as $param_key => $param_value) {
//        echo "$param_key=>{$param_value['value']}";
      $value = urlencode(null === $param_value['value'] ? 'YourParam' : $param_value['value']);
      $param_str .= "&fps[{$param_key}]={$value}";
    }
    ?>
    <tr>
      <td class="bg-danger" colspan="3">
        <strong><?php echo $param_name; ?></strong>
        <span class="btn-sm"><?php echo $param_info_arr['refer_desc']; ?></span>
      </td>
    </tr>

    <tr>
      <td class="warning" colspan="3">
        <div>
          调用地址：<span
            style="color:#a94442;"><?php echo route($route_name, []); ?></span>?ic=<?php echo $api_name; ?>&AMP;im=<?php echo $method; ?>
          &nbsp;&nbsp;
          请求参数：<?php echo $param_str; ?>
        </div>
      </td>
    </tr>

    <tr>
      <th><strong>参数名称：</strong></th>
      <th>默认值</th>
      <th>功能描述</th>
    </tr>

    <?php foreach($param_info_arr['params'] as $param_key=>$param_value) : ?>
    <tr>
      <td>fps[<strong style="color: #a94442;"><?php echo $param_key ?></strong>]</td>
      <td><?php echo null === $param_value['value'] ? '（必填）' : $param_value['value']; ?></td>
      <td><?php echo $param_value['info'];  ?></td>

    </tr>
    <?php endforeach; ?>

    <tr>
      <td colspan="3" class="success">Result Json：<?php

        $back_val = $param_info_arr['sample'];
        if (null === $back_val) {
          echo '<span style="color:RED;">无结果样例</span>';
        } else {

          if (is_array($back_val)) {
            $output_value = CFEOutputHandle::OutputFromArr($back_val);
          } else {
            $output_value = CFEOutputHandle::OutputFromObj($back_val);
          }

          $result_arr['result'] = 'success';
          $result_arr['back_value'] = $output_value;

          echo json_encode($result_arr);
        }
        ?></td>
    </tr>
    <tr>
      <td class="success"
          colspan="3"><?php echo '' != $param_info_arr['result_desc'] ? htmlspecialchars($param_info_arr['result_desc']) : '----'; ?></td>
    </tr>

    <?php endforeach; ?>
    <?php endforeach; ?>
  </table>

  <hr/>
  <h2>Error list</h2>
  <table class="table table-bordered">
    <tr>
      <th>Sign</th>
      <th>Code</th>
      <th>Msg</th>
      <th>Simple</th>
    </tr>


    <?php foreach($exception_feapi_arr as $exception_feapi_obj): $result_arr = array();?>

    <tr>
      <td class="danger"><?php echo $exception_feapi_obj['sign']; ?></td>
      <td class="danger"><?php echo $exception_feapi_obj['code']; ?></td>
      <td><?php echo htmlspecialchars($exception_feapi_obj['msg']); ?></td>
      <td>
        <div style="word-break:break-all;">
          <?php

          $result_arr['result'] = 'failure';
          $result_arr['error_sign'] = $exception_feapi_obj['sign'];
          $result_arr['error_code'] = $exception_feapi_obj['code'];
          $result_arr['error_info'] = $exception_feapi_obj['msg'];
          echo json_encode($result_arr);

          ?>
        </div>
      </td>
    </tr>
    <?php    endforeach;?>
  </table>


  <?php else: ?>
  <h2>Api - <?php echo ucfirst($api_name);?> </h2>
  <div class="alert alert-success" role="alert">
    <?php echo $api_obj->definedDesc(); ?>
    当前文档需要登录后才能查看。
  </div>
  <h2>Input password</h2>
  <form action="<?php echo route('feman', ['ic' => $api_name]); ?>" method="POST">
    {{ csrf_field() }}
    <table class="table table-bordered">
      <tr>
        <td>
          登录密码：
        </td>
      </tr>
      <tr>
        <td>
          <input name="verfiypassword" type="password" value=""/>
        </td>
      </tr>
      <tr>
        <td>
          <input name="verfiysubmit" class="btn btn-primary" type="submit" value="验证"/>
        </td>
      </tr>
    </table>
  </form>
  <?php endif ?>

</div>

</body>
</html>
