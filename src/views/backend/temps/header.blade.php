<div id="header" class="bg-dark-blue clearfix">
    <a class="logo" href="javascript:;">小树创投</a>
        <div class="top_menu">
            <?php foreach($adminMenu as $node): ?>
                <?php if($node->visible): ?>
                <a  href="{{ $node->url() ? : '' }}"
                    class="color-white {{ $node->active ? 'selected' : '' }}">
                    {{ $node->title }}
                </a>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>
        <div class="user">欢迎您 {{ $loginAdmin->name }} | <a href="{{ route('backend::auth.logout') }}" class="color-white">退出</a></div>
</div>