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


    <form method="POST" action="{{ route('backend::system.authorize.roles.update',$role->id) }}">
        {!! csrf_field() !!}
        <input type="hidden" name="_method" value="PUT">
        <table border="0" cellpadding="0" cellspacing="14" width="100%" class="add-table">
            <tbody>
            <tr>
                <td nowrap="" align="right" width="63" class="v">角色名：</td>
                <td>
                    <input type="text" name="title" value="{{ $role->title }}" class="input_text">&nbsp;&nbsp;
                    <span class="color-grey">最多输入20字符</span>
                    <div class=" td_error hidden" name="error" id="name_error">请输入角色名</div>
                </td>
            </tr>
            <tr>
                <td nowrap="" align="right" valign="top">菜单权限：</td>
                <td>
                    <div class="nowrap">
                        <?php $i = 0 ; ?>
                        @foreach($adminMenu as $group)
                            <span name="menu_tab"
                                  class="role1 {{ $i ? '' : 'bg-grey1' }}">{{ $group->title }}</span>
                            <?php $i ++; ?>
                        @endforeach
                    </div>
                    <?php $i = 0 ; ?>
                    @foreach($adminMenu as $group)
                        <div class="jqxx"
                             name="secondary_menu"
                             id="menu_index_{{ $group->title }}"
                             style="display:{{ $i ? 'none' : 'block' }};">
                            @foreach($group->nodeSons as $section)
                                <div class="first-role" id="first-role-{{ $group->title }}">
                                <span>
                                   @if($section->isRoute)
                                        <input type="checkbox"
                                               name="nodes[]"
                                               value="{{ $section->routeName }}"
                                               {{ in_array($section->routeName , $role->nodes) ? 'checked':'' }}
                                               data-target="#second-role-{{ $section->title }}"
                                               data-xiaoshu="node"
                                        >
                                    @endif
                                    &nbsp;{{ $section->title }}
                                </span>
                                </div>
                                @foreach($section->nodeSons as $func)
                                    <div class="second-role" id="second-role-{{ $section->title }}">
                                        <span>
                                           @if($func->isRoute)
                                                <input type="checkbox"
                                                       name="nodes[]"
                                                       data-xiaoshu="node"
                                                       data-target="#third-role-{{$func->title}}"
                                                       value="{{ $func->routeName }}"
                                                       {{ in_array($func->routeName , $role->nodes) ? 'checked':'' }}
                                                >
                                            @endif
                                            &nbsp;{{ $func->title }}
                                        </span>
                                        <div class=" third-role" id="third-role-{{$func->title}}">
                                            @foreach($func->nodeSons as $method)
                                                <span>
                                                    <input type="checkbox"
                                                           name="nodes[]"
                                                           value="{{ $method->routeName }}"
                                                           {{ in_array($method->routeName , $role->nodes) ? 'checked':'' }}
                                                    >
                                                &nbsp;{{ $method->title }}
                                                </span>
                                            @endforeach
                                        </div>
                                    </div>
                                @endforeach
                            @endforeach
                        </div>
                        <?php $i ++; ?>
                    @endforeach
                </td>
            </tr>
            <tr>
                <td></td>
                <td><input type="submit" class="btn-green" value="保存"></td>
            </tr>
            </tbody>
        </table>
    </form>

@endsection
@push('scripts')
@endpush
