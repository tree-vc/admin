@extends('xiaoshu::backend.layout')
@section('header')
    @include('xiaoshu::backend.temps.header')
@endsection
@section('content')
 <div id="container">
     <div id="left" class="bg-dark-blue">
         @include('xiaoshu::backend.temps.left-menu')
     </div>
     <div id="middle">
         <div class="relative">
             <a href="javascript:;" class="leftMenuBtnHide show"></a>
             <a href="javascript:;" class="leftMenuBtnShow hide"></a>
         </div>
     </div>
     <div id="right">
         <div class="p-bottom16">
         @section('contentRight')
             {{ $message or '' }}
         @show
         </div>
     </div>
 </div>
@endsection
@push('scripts')
<script src="/xiaoshu.admin/js/xiaoshu.js"></script>
<script src="/xiaoshu.admin/js/common/container.js"></script>
<script src="/xiaoshu.admin/js/common/right.js"></script>
<script src="/xiaoshu.admin/js/common/detail.js"></script>
<script src="/common/regionjs"></script>
<script src="/xiaoshu.admin/js/base/regionSelector.js"></script>
<script src="/xiaoshu.admin/js/base/SingleImageUploader.js"></script>
<script src="/xiaoshu.admin/js/base/plugins/jquery/webuploader/webuploader.js"></script>
<script type="text/javascript" src="/js/base/plugins/laydate/laydate.js"></script>
@endpush

