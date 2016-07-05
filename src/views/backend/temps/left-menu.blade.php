<div id="left_menu_zone" class="left_container">
    <?php if($currRootNode): ?>
    <div class="colL" data-name="left_menu" id="left_menu_index_{{ $currRootNode->title }}" >
        <?php foreach($currRootNode->nodeSons as $section): ?>
        <div class="subNav">
            <b class="arrow {{  $section->active ? 'arrow-bottom' : 'arrow-right'}}"></b>
            {{ $section->title ? : '未命名' }}
        </div>
        <ul class="navContent" style="display:{{ $section->active ? 'block' : 'none' }}">
            <?php foreach($section->nodeSons as $func): ?>
            <?php if($func->visible): ?>
            <li data-name="menu">
                <a href="{{  $func->url() ? : '' }}"
                   class="{{ $func->active ? 'clicked' : '' }}">
                    {{ $func->title }}
                </a>
            </li>
            <?php endif; ?>
            <?php endforeach; ?>
        </ul>
        <?php endforeach; ?>
    </div>
    <?php endif;?>
</div>