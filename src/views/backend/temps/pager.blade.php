<div class="page_bg">
    <div class="pages_right" id="pages">
        {{ with(new \App\Core\Presenters\PagingPresenter($pager))->render() }}
    </div>
</div>