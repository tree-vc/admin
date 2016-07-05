@extends('xiaoshu::backend.backend')
@section('contentRight')
    <div class="location clearfix">
        <i></i>角色管理
        <div class="f-right">
            <input type="button" id="refresh" class="btn-grey" value="刷新" onclick="location.reload();">
            <span {{ $adminRoutes['backend::system.authorize.roles.create']->isAuthorized ? '' : 'style="display:none"' }}>
                <a href="{{ route('backend::system.authorize.roles.create') }}"  target="_blank" class="btn-green m_left15">
                    添加角色
                </a>
            </span>
        </div>
    </div>

    <table width="100%" border="0" cellspacing="0" cellpadding="0" id="table_list" name="table_list" class="list-table">
        <thead>
            <tr>
                <th width="20%">角色名</th>
                <th width="35%">权限</th>
                <th width="15%">更新者</th>
                <th width="20%">
                    <a href="{{ request()->fullUrlWithQuery(['order' => request('order','desc') === 'asc' ? 'desc' : 'asc']) }}"
                      class="sort_{{ request('order','desc') === 'asc' ? 'asc' : 'desc' }}">更新时间</a>
                </th>
                <th width="10%">操作</th>
            </tr>
        </thead>
        <tbody>
            @foreach($roles as $role)
            <tr>
                <td>{{ $role->title }}</td>
                <td>
                    @foreach($role->getNodeTree() as $group => $sections)
                       <p>{{ $group }}</p>
                        @foreach($sections as $section => $funcs)
                            <p>&nbsp;&nbsp;&nbsp;&nbsp;{{ $section }}</p>
                            @foreach($funcs as $func => $methods)
                                <p class="r1">{{ $func }} -
                                    @foreach($methods as $method => $nothing)
                                        {{$method}} |
                                    @endforeach
                                </p>
                            @endforeach
                        @endforeach
                    @endforeach
                </td>
                <td>{{ $role->editor_name }}</td>
                <td>{{ $role->created_at}}</td>
                <td class="color_blue operate_box">
                    @if($adminRoutes['backend::system.authorize.roles.edit']->isAuthorized)
                    <span>
                        <a href="{{ route('backend::system.authorize.roles.edit',$role->id) }}" >编辑</a>
                    </span>
                    @endif
                    @if($adminRoutes['backend::system.authorize.roles.edit']->isAuthorized)
                    <span>
                        <a href=""
                           data-xiaoshu="resource"
                           data-method="destroy"
                           data-action="{{ route('backend::system.authorize.roles.destroy',$role->id) }}" >删除</a>
                    </span>
                    @endif
                </td>
            </tr>
            @endforeach

        </tbody>
</table>

<div id="foot_box">
        <div class="page_bg">
        {{ $roles->render() }}
        <div class="pages_right" id="pages">
            <span class="pre_page">上一页</span>&nbsp;<span class="current">1</span>&nbsp;<span class="next_page">下一页</span>&nbsp;&nbsp;共1条
        </div>
    </div>
    </div>

{{--<script src="/js/base/listTable.js"></script>--}}

@endsection
@push('scripts')
@endpush