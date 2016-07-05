@extends('xiaoshu::backend.backend')
@section('title','系统用户管理')
@section('contentRight')
<!--页头-->
<div class="location clearfix"><i></i>编辑系统用户
</div>


<!--编辑管理员-->
<form id="edit_admin" method="POST" action="{{ route('backend::system.admin.admins.update',$id)}}">
    {{ csrf_field() }}
    <input type="hidden" name="_method" value="PATCH">
    <b class="center-hack"></b>
    <div class="center-body ">
        <div class="fb_Box">
           <div id="fb_content2" class="fb_content">
                <table border="0" cellpadding="0" cellspacing="14" width="100%" class="add-user">
                    <tbody>
                    <tr>
                        <td width="75" nowrap="nowrap" align="right" class="v"><span class="star f-left ">*</span>用户名：</td>
                        <td>
                            <input type="text"
                                   name="name"
                                   class="input_text"
                                   disabled
                                   value="{{ $admin->name }}">&nbsp;&nbsp;
                            <span class="color-grey">不可修改</span>
                        </td>
                    </tr>

                    <tr>
                        <td align="right" class="v">
                            <span class="star f-left">*</span>姓名：
                        </td>
                        <td>
                            <input type="text"
                                   name="real_name"
                                   value="{{ $admin->real_name }}"
                                   class="input_text">
                            &nbsp;&nbsp;<span class="color-grey">不可修改</span>
                        </td>
                    </tr>
                    <tr>
                        <td align="right" class="v">
                            重置密码：
                        </td>
                        <td>
                            <input type="password"
                                   name="password"
                                   class="input_text">
                        </td>
                    </tr>
                    <tr>
                        <td align="right" class="v">
                            确认密码：
                        </td>
                        <td>
                            <input type="password"
                                   name="password_confirmation"
                                   class="input_text">
                        </td>
                    </tr>
                    <tr>
                        <td align="right" class="v">
                            邮箱：
                        </td>
                        <td>
                            <input type="text" name="email" class="input_text" value="{{ old('email',$admin->email) }}">
                            <div class="errorTip hide" id="email_error">邮箱格式错误，请重新输入</div>
                        </td>
                    </tr>
                    <tr>
                        <td align="right" valign="top" style=" padding-top: 2px;">用户角色：</td>
                        <td class="user-role">
                            <div style="  word-break: break-all; white-space: normal; width: 564px;">
                                @foreach($statusTexts as $status => $text)
                                <span>
                                    <input type="radio"
                                           value="{{ $status }}"
                                           name="status"
                                           {{ $admin->status === $status ? 'checked' : '' }}
                                    >&nbsp;{{ $text }}
                                </span>
                                @endforeach
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td align="right" valign="top" style=" padding-top: 2px;">用户角色：</td>
                        <td class="user-role">
                            <div style="  word-break: break-all; white-space: normal; width: 564px;">
                                @foreach($backendRoles as $id => $title)
                                    <span>
                                        <input type="checkbox"
                                               value="{{ $id }}"
                                               name="roles[]"
                                               {{ $id === 0 ? 'disabled':'' }}
                                               {{ isset($admin->roles_arr[$id]) ? 'checked' : '' }}
                                        >&nbsp;{{ $title }}
                                    </span>
                                @endforeach
                            </div>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
            <div class="fb_footer">
                <input type="submit"
                       id="confirm"
                       class="btn-green"
                       value="确定">
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                <input type="button"
                       class="btn-grey"
                       value="返回"
                       onclick="location.replace('{{ route('backend::system.admin.admins.index') }}');"
                >
            </div>
        </div>
    </div>
</form>

@endsection
