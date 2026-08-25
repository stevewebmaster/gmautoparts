{{--
    schema.org Product + BreadcrumbList for a part page.

    Emitted as JSON-LD, which Google reads without any Merchant Center account.
    Values come from PartPresenter — the same source the Merchant feed uses — so
    the landing page and the feed item always agree.
--}}
@push('head_scripts')
<script type="application/ld+json">
{!! json_encode(
    \App\Services\ProductStructuredData::for($part),
    JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT
) !!}
</script>
<script type="application/ld+json">
{!! json_encode(
    \App\Services\ProductStructuredData::breadcrumbs($part),
    JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT
) !!}
</script>
@endpush
