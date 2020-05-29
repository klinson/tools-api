<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', $_theme_info['default_title'] ?? 'Cms') | {{ $_theme_info['system_name'] ?? 'klinson' }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="@yield('description', $_theme_info['description'] ?? 'klinson个人站')" />
    <meta name="keyword" content="@yield('keyword', $_theme_info['keyword'] ?? 'klinson,cms')" />
    <meta name="author" content="@yield('keyword', $_theme_info['author'] ?? 'klinson')" />

    <link rel="stylesheet" href="{{ asset($_theme_info['style_root_path'].'/css/bootstrap.css') }}">

</head>
<body>

<div class="container">
    <div class="row">
        <div class="col-sm-12 col-md-12">
            <h3 class="text-center">相似文本查询</h3>
            <hr>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-12 col-md-12">
            <form action="{{ route('nlp.simple') }}" method="post" id="form1">
                {{ csrf_field() }}
                <div class="form-group">
                    <label for="name">文本1</label>
                    <textarea class="form-control" rows="5" name="text1" required></textarea>
                </div>
                <div class="form-group">
                    <label for="name">文本2</label>
                    <textarea class="form-control" rows="5" name="text2" required></textarea>
                </div>
                <div class="form-group">
                    <label for="name">模型</label>
                    <select class="form-control" name="model" required>
                        <option value="BOW">词包</option>
                        <option value="GRNN">循环神经网络</option>
                        <option value="CNN">卷积神经网络</option>
                    </select>
                </div>

                <div>
                    <span class="btn btn-success btn-block" onclick="submitForm('#form1')">查询</span>
                </div>
            </form>
        </div>


    </div>
    <div class="row" style="margin-top: 1em">
        <div class="col-sm-12 col-md-12">
            <div class="well">
                <p>分数：<span id="result"></span></p>
            </div>
        </div>
    </div>
</div>

<script src="{{ asset($_theme_info['style_root_path'].'/js/vendor/jquery-2.2.4.min.js') }}"></script>
<script src="{{ asset($_theme_info['style_root_path'].'/js/vendor/bootstrap.min.js') }}"></script>

<script>
    function submitForm(formSelect) {
        var formObj = $(formSelect);
        console.log(formObj)

        $.ajax({
            type: formObj[0].method,   //提交的方法
            url: formObj[0].action, //提交的地址
            data: formObj.serialize(),// 序列化表单值
            async: false,
            error: function(request) {  //失败的话
                console.log(request)
                alert("请求错误");
            },
            success: function(data) {  //成功
                alert(data);  //就将返回的数据显示出来
            }
        });
    }
</script>

</body>
</html>