@extends('xiaoshu::backend.backend')
@section('title','系统用户管理')
@section('contentRight')

<!--页头-->
<div class="location clearfix"><i></i>系统用户管理
    <input type="hidden" id="link_url" value="/admin/user/list">
    <span>
        <a data-xiaoshu="toggle"
           data-target="#add_admin_box"
           data-method="toggle"
           class="btn-green m_left15 f-right">添加用户</a>
    </span>
    <input type="button"
           data-xiaoshu="form"
           data-target="#reload"
           data-method="submit"
           class="btn-grey f-right" value="刷新">
</div>


<!--隐藏的get表单-->
<form class="hidden" id="reload" method="GET" action="{{ request()->url() }}">
    <input type="hidden" name="page" value="{{ request('page',1) }}">
    <input type="hidden" name="status" value="{{ request('status','all') }}">
    <input type="hidden" name="orderByName" value="{{ request('orderByName','') }}">
    <input type="hidden" name="orderByRealName" value="{{ request('orderByRealName','') }}">
</form>

<!--列表主体-->
<table width="100%" border="0" cellspacing="0" cellpadding="0" id="table_list" class="list-table">
    <thead>
        <tr>
            <th width="80">
                <a  class="sort_{{ request('orderByName') === 'desc' ? 'desc' : 'asc' }}_grey"
                    data-xiaoshu="form"
                    data-target="#reload"
                    data-method="resetSubmit"
                    data-name="orderByName"
                    data-value="{{ request('orderByName') === 'desc' ? 'asc' : 'desc' }}"
                >用户名</a>
            </th>
            <th width="100">

                <a  class="sort_{{ request('orderByRealName') === 'desc' ? 'desc' : 'asc' }}_grey"
                    data-type="order_by"
                    data-xiaoshu="form"
                    data-target="#reload"
                    data-method="resetSubmit"
                    data-name="orderByRealName"
                    data-value="{{ request('orderByRealName') === 'desc' ? 'asc' : 'desc' }}"
                >姓名</a>
            </th>
            <th width="140">邮箱</th>
            <th>角色</th>
            <th width="100">
                <div class="more">
                    <a href="javascript:;"
                       class="sort_select"
                       data-xiaoshu="dropdown"
                       data-target="#status_list"
                    >
                        状态(<span name="current_filter">{{ $statusOptions[request('status')] or '全部' }}</span>)
                    </a>
                <ul id="status_list"
                    class="more-list more-list-top1"
                    style="display: none;"
                    data-field="status">
                    <li data-dropdown="item"
                        data-xiaoshu="form"
                        data-target="#reload"
                        data-method="resetSubmit"
                        data-name="status"
                        data-value="all"
                        {{ request('status') == 'all' ? 'class="li-bg"' : '' }} >全部</li>
                    @foreach($statusOptions as $value => $option)
                    <li data-dropdown="item"
                        data-xiaoshu="form"
                        data-target="#reload"
                        data-method="resetSubmit"
                        data-name="status"
                        data-value="{{ $value }}"
                        {{ request('status') == $value ? 'class="li-bg"' : '' }} >{{ $option }}</li>
                    @endforeach
                </ul></div>
            </th>
            <th width="80">更新者</th>
            <th width="120">
                <a >更新时间</a>
            </th>
            <th width="120">操作</th>
        </tr>
    </thead>
    <tbody>
        @foreach($admins as $admin)
        <tr id="index_{{ $admin->id }}">
            <td>{{ $admin->name }}</td>
            <td>{{ $admin->real_name }}</td>
            <td>{{ $admin->email }}</td>
            <td>{{ $admin->roles_text }}</td>
            <td>{{ $admin->status_text }}</td>
            <td>{{ $admin->editor_name }}</td>
            <td>{{ $admin->updated_at->format('Y-m-d H:i') }}</td>
            <td name="operate" class="color_blue operate_box">
                <span>
                    <a href="{{ route('backend::system.admin.admins.edit',$admin->id) }}" >编辑</a>
                </span>
                @if($admin->isLocked())
                <span>
                    <a data-xiaoshu="resource"
                       data-confirmation="确定要解锁账号{{ $admin->name }}（{{ $admin->real_name }}）吗？"
                       data-method='put'
                       data-func='unlock'
                       data-action="{{ route('backend::system.admin.admins.update',$admin->id) }}"
                    >解锁</a>
                </span>
                @else
                <span>
                    <a data-xiaoshu="resource"
                       data-confirmation="确定要锁定账号{{ $admin->name }}（{{ $admin->real_name }}）吗？"
                       data-method="put"
                       data-func="lock"
                       data-action="{{ route('backend::system.admin.admins.update',$admin->id) }}"
                    >锁定</a>
                </span>
                @endif
                <span >
                    <a  data-xiaoshu="resource"
                        data-confirmation="确定要删除账号{{ $admin->name }}（{{ $admin->real_name }}) 吗?"
                        data-method="put"
                        data-func="destroy"
                        data-action="{{ route('backend::system.admin.admins.update',['id'=>$admin->id]) }}"
                    >删除</a>
                </span>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>


<div id="foot_box" style="position: fixed; bottom: 0px; left: 164px; right: 26px;">
        <div class="page_bg">
        <div class="pages_right" id="pages">
           <span class="pre_page">上一页</span>&nbsp;<span class="current">1</span>&nbsp;<span class="next_page">下一页</span>&nbsp;&nbsp;共6条
        </div>
    </div>
    </div>



{{-- <script src="/js/base/listTable.js"></script> --}}
@endsection


@section('tool')
<!--添加管理员-->
<div id="add_admin_box" class="add-alter-user center-box well  hidden">
    <form id="add_admin" method="POST" action="{{ route('backend::system.admin.admins.store') }}">
        {{ csrf_field() }}
        <b class="center-hack"></b>
        <div class="center-body ">
            <div class="fb_Box">
                <div class="fb_header">
                    <span id="dialog_title"></span>
                    <img src="/xiaoshu.admin/images/close2.png"
                         width="30"
                         height="30"
                         alt=""
                         class="close"
                         data-xiaoshu="toggle"
                         data-method="close"
                         data-target="#add_admin_box"
                    >
                </div>
                <div id="fb_content2" class="fb_content">
                    <table border="0" cellpadding="0" cellspacing="14" width="100%" class="add-user">
                        <tbody>
                            <tr>
                                <td width="75" nowrap="nowrap" align="right" class="v"><span class="star f-left ">*</span>用户名：</td>
                                <td>
                                    <input type="text" name="name" class="input_text" value="{{ old('name') }}">&nbsp;&nbsp;
                                    <span class="color-grey">4-20字符，支持字母、数字、下划线组合</span>
                                    <div class="errorTip hide"  id="name_error">请输入用户名</div>
                                </td>
                            </tr>

                            <tr>
                                <td align="right" class="v">
                                    <span class="star f-left">*</span>姓名：
                                </td>
                                <td>
                                    <input type="text"
                                           name="real_name"
                                           value="{{ old('real_name') }}"
                                           class="input_text">
                                    &nbsp;&nbsp;<span class="color-grey">最多输入50字符</span>
                                    <div class="errorTip hide" id="nickname_error">最多输入50字符</div>
                                </td>
                            </tr>
                            <tr>
                                <td align="right" class="v">
                                    <span class="star f-left">*</span>密码：
                                </td>
                                <td>
                                    <input type="password"
                                           name="password"
                                           class="input_text">
                                </td>
                            </tr>
                            <tr>
                                <td align="right" class="v">
                                    <span class="star f-left">*</span>确认密码：
                                </td>
                                <td>
                                    <input type="password"
                                           name="password_confirmation"
                                           class="input_text">
                                </td>
                            </tr>
                            <tr>
                                <td align="right" class="v">
                                    <span class="star f-left">*</span>邮箱：
                                </td>
                                <td>
                                    <input type="text" name="email" class="input_text" value="{{ old('email') }}">
                                    <div class="errorTip hide" id="email_error">邮箱格式错误，请重新输入</div>
                                </td>
                            </tr>
                            <tr>
                                <td align="right" valign="top" style=" padding-top: 2px;"><span class="star f-left">*</span>用户角色：</td>
                                <td class="user-role">
                                    <div style="  word-break: break-all; white-space: normal; width: 564px;">
                                        @foreach($backendRoles as $id => $title)
                                        <span>
                                            <input type="checkbox"
                                                   value="{{ $id }}"
                                                   name="role_ids[]"
                                                    {{ $id === 0 ? 'disabled':'' }}
                                                   {{ in_array($id,old('role_ids[]',[])) ? 'checked' : '' }}
                                            >&nbsp;{{ $title }}
                                        </span>
                                        @endforeach
                                    </div>
                                    <div class="errorTip hide" id="role_error">邮箱格式错误，请重新输入</div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="fb_footer">
                    <input type="button"
                           id="confirm"
                           class="btn-green"
                           data-xiaoshu="form"
                           data-method="ajaxSubmit"
                           data-target="#add_admin"
                           value="确定">
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                    <input type="button"
                           class="btn-grey"
                           value="取消"
                           data-xiaoshu="toggle"
                           data-method="close"
                           data-target="#add_admin_box"
                    >
                </div>
            </div>
        </div>
    </form>
</div>

@endsection

