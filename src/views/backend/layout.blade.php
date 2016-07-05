<!DOCTYPE HTML>
<html lang="zh">
<head>
    @section('head.meta')
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible"content="IE=edge">
        <meta name="renderer" content="webkit">
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <meta name="author" content="treevc">
        <meta name="csrf-token" content="{{ csrf_token() }}">
    @show
    @section('head.seo')
        <title>@yield('title','众包') - 小树</title>
        <meta name="keywords" content="">
        <meta name="description" content="">
    @show
    @section('head.css')
        <link rel="stylesheet" href="/xiaoshu.admin/css/admin.css">
        <link rel="stylesheet" href="/xiaoshu.admin/css/demo.css">
    @show
    @section('head.js')
        <script type="text/javascript" src="/xiaoshu.admin/js/base/jquery-1.7.1.min.js"></script>
        <script type="text/javascript" src="/xiaoshu.admin/js/base/treevc.js"></script>
    @show
</head>
<body>

<!--页头-->
@yield('header')
<!--内容-->
@yield('content')
<!--页脚-->
@yield('tool')
@yield('footer')
@stack('scripts')
</body>
</html>
