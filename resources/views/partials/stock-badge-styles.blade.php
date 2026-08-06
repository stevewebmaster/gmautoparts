@once
@push('head_styles')
<style>
    /* Inventory status badges — shared by the parts grid and part detail page. */
    .gm-stock-badge {
        display: inline-block;
        vertical-align: middle;
        margin-left: 0.5rem;
        padding: 0.2em 0.65em;
        border-radius: 999px;
        font-size: 0.7rem;
        font-weight: 700;
        letter-spacing: 0.04em;
        text-transform: uppercase;
        line-height: 1.5;
        white-space: nowrap;
    }
    .gm-stock-badge--hold { background: #fef3c7; color: #92400e; }
    .gm-stock-badge--sold { background: #fee2e2; color: #991b1b; }

    .gm-stock-notice {
        display: flex;
        align-items: flex-start;
        gap: 0.75rem;
        padding: 1rem 1.15rem;
        border-radius: 12px;
        margin-bottom: 1.5rem;
        background: #fef2f2;
        border: 1px solid #fecaca;
        color: #7f1d1d;
    }
    .gm-stock-notice--hold {
        background: #fffbeb;
        border-color: #fde68a;
        color: #78350f;
    }
    .gm-stock-notice .gm-stock-notice-title {
        margin: 0 0 0.2rem;
        font-size: 1.05rem;
        font-weight: 700;
        color: inherit;
    }
    .gm-stock-notice p { margin: 0; font-size: 0.95rem; color: inherit; }
</style>
@endpush
@endonce
