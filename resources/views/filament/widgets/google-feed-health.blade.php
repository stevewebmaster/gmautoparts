<x-filament-widgets::widget>
    <x-filament::section
        icon="heroicon-o-shopping-cart"
        icon-color="primary"
    >
        <x-slot name="heading">Google Shopping</x-slot>

        <x-slot name="description">
            What is being sent to Google Merchant Center, and what is being left out.
        </x-slot>

        <div class="space-y-6">
            {{-- Headline: what is actually going to Google right now. --}}
            <div class="flex flex-wrap items-end gap-x-8 gap-y-4">
                <a href="{{ $inFeedUrl }}" class="group">
                    <div class="text-3xl font-semibold tabular-nums text-success-600 dark:text-success-400 group-hover:underline">
                        {{ number_format($inFeed) }}
                    </div>
                    <div class="text-sm text-gray-500 dark:text-gray-400">
                        {{ \Illuminate\Support\Str::plural('part', $inFeed) }} in the feed
                    </div>
                </a>

                <div>
                    <div @class([
                        'text-3xl font-semibold tabular-nums',
                        'text-warning-600 dark:text-warning-400' => $needsAttention > 0,
                        'text-gray-400 dark:text-gray-500' => $needsAttention === 0,
                    ])>
                        {{ number_format($needsAttention) }}
                    </div>
                    <div class="text-sm text-gray-500 dark:text-gray-400">
                        {{ $needsAttention === 1 ? 'part is' : 'parts are' }} missing something fixable
                    </div>
                </div>

                <div>
                    <div class="text-3xl font-semibold tabular-nums text-gray-400 dark:text-gray-500">
                        {{ number_format($total) }}
                    </div>
                    <div class="text-sm text-gray-500 dark:text-gray-400">
                        parts in the catalogue
                    </div>
                </div>
            </div>

            @if ($inFeed === 0 && $total > 0)
                <p class="rounded-lg bg-danger-50 p-3 text-sm text-danger-700 dark:bg-danger-400/10 dark:text-danger-400">
                    <strong>Nothing is going to Google.</strong>
                    Every part is excluded for one of the reasons below. Until at least one part
                    qualifies, Merchant Center has nothing to list.
                </p>
            @endif

            {{-- The worklist. Each row is a filtered Parts view, one click away. --}}
            @if (count($rows))
                <div class="divide-y divide-gray-100 overflow-hidden rounded-lg border border-gray-200 dark:divide-white/5 dark:border-white/10">
                    @foreach ($rows as $row)
                        <a
                            href="{{ $row['url'] }}"
                            class="flex items-start gap-4 p-3 transition hover:bg-gray-50 dark:hover:bg-white/5"
                        >
                            <span class="w-12 shrink-0 text-right text-lg font-semibold tabular-nums text-gray-950 dark:text-white">
                                {{ number_format($row['count']) }}
                            </span>

                            <span class="min-w-0 flex-1">
                                <span class="flex flex-wrap items-center gap-2">
                                    <x-filament::badge :color="$row['status']->color()">
                                        {{ $row['status']->label() }}
                                    </x-filament::badge>

                                    @if ($row['status']->isFixable())
                                        <span class="text-xs font-medium text-warning-600 dark:text-warning-400">
                                            needs attention
                                        </span>
                                    @endif
                                </span>

                                <span class="mt-1 block text-sm text-gray-500 dark:text-gray-400">
                                    {{ $row['status']->description() }}
                                </span>
                            </span>

                            <x-filament::icon
                                icon="heroicon-m-chevron-right"
                                class="mt-1 h-5 w-5 shrink-0 text-gray-400"
                            />
                        </a>
                    @endforeach
                </div>
            @elseif ($total > 0)
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    Every part in the catalogue is in the feed.
                </p>
            @else
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    There are no parts in the catalogue yet.
                </p>
            @endif

            {{-- Provenance: where the feed is, and whether Google is taking it. --}}
            <div class="flex flex-wrap items-center gap-x-6 gap-y-2 border-t border-gray-100 pt-4 text-sm dark:border-white/5">
                <span class="text-gray-500 dark:text-gray-400">
                    Last collected:
                    <span class="font-medium text-gray-950 dark:text-white">
                        @if ($lastFetchedAt)
                            {{ $lastFetchedAt->diffForHumans() }}
                        @else
                            not since the site last restarted
                        @endif
                    </span>
                </span>

                <a
                    href="{{ $feedUrl }}"
                    target="_blank"
                    rel="noopener"
                    class="font-medium text-primary-600 hover:underline dark:text-primary-400"
                >
                    View the feed
                </a>
            </div>

            <p class="text-xs leading-relaxed text-gray-400 dark:text-gray-500">
                This shows what the website sends Google. Whether Google <em>approves</em> each
                product — and any policy warnings — is only visible in Merchant Center itself.
            </p>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
