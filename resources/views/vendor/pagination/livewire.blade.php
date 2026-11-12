{{--
    The site's pagination (same markup as default.blade.php, which the theme's
    .th-pagination styles expect) for Livewire lists. Livewire otherwise uses
    its own Tailwind view, which this theme does not style.

    Each link still has a real href, so crawlers and middle-click work; a normal
    click is handled by Livewire instead, which keeps the list's filters.
--}}
@php
    $scrollTo = $scrollTo ?? 'body';
    $scrollJs = $scrollTo !== false
        ? "(\$el.closest('{$scrollTo}') || document.querySelector('{$scrollTo}')).scrollIntoView()"
        : '';
    $pageName = $paginator->getPageName();
@endphp

<div>
    @if ($paginator->hasPages())
        <nav aria-label="Pagination">
            <ul class="pagination pagination-wrap justify-content-center flex-wrap mb-0">
                @if ($paginator->onFirstPage())
                    <li class="page-item disabled"><span class="page-link">&laquo;</span></li>
                @else
                    <li class="page-item"><a class="page-link" href="{{ url($paginator->previousPageUrl()) }}" rel="prev" aria-label="Previous page" wire:click.prevent="previousPage('{{ $pageName }}')" x-on:click="{{ $scrollJs }}">&laquo;</a></li>
                @endif

                @foreach ($elements as $element)
                    @if (is_string($element))
                        <li class="page-item disabled"><span class="page-link">{{ $element }}</span></li>
                    @endif

                    @if (is_array($element))
                        @foreach ($element as $page => $url)
                            @if ($page == $paginator->currentPage())
                                <li class="page-item active" aria-current="page" wire:key="paginator-{{ $pageName }}-page-{{ $page }}"><span class="page-link">{{ $page }}</span></li>
                            @else
                                <li class="page-item" wire:key="paginator-{{ $pageName }}-page-{{ $page }}"><a class="page-link" href="{{ url($url) }}" wire:click.prevent="gotoPage({{ $page }}, '{{ $pageName }}')" x-on:click="{{ $scrollJs }}">{{ $page }}</a></li>
                            @endif
                        @endforeach
                    @endif
                @endforeach

                @if ($paginator->hasMorePages())
                    <li class="page-item"><a class="page-link" href="{{ url($paginator->nextPageUrl()) }}" rel="next" aria-label="Next page" wire:click.prevent="nextPage('{{ $pageName }}')" x-on:click="{{ $scrollJs }}">&raquo;</a></li>
                @else
                    <li class="page-item disabled"><span class="page-link">&raquo;</span></li>
                @endif
            </ul>
        </nav>
    @endif
</div>
