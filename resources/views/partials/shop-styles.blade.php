@once
@push('head_styles')
<style>
    .gm-shop { max-width: 960px; margin: 0 auto; }
    .gm-shop-card { background:#fff; border:1px solid #e5e7eb; border-radius:16px; padding:clamp(1.1rem,3vw,1.9rem); box-shadow:0 10px 30px rgba(15,23,42,.06); }
    .gm-shop-card + .gm-shop-card { margin-top:1rem; }
    .gm-line { display:flex; gap:1rem; padding:1rem 0; border-top:1px solid #e5e7eb; align-items:center; }
    .gm-line:first-of-type { border-top:none; }
    .gm-line-img { width:76px; height:60px; border-radius:10px; object-fit:cover; background:#f3f4f6; flex:0 0 auto; }
    .gm-line-body { flex:1 1 auto; min-width:0; }
    .gm-line-title { margin:0; font-weight:700; color:#0f172a; line-height:1.3; }
    .gm-line-title a { color:inherit; text-decoration:none; }
    .gm-line-meta { margin:.15rem 0 0; font-size:.85rem; color:#6b7280; }
    .gm-line-price { flex:0 0 auto; font-weight:800; color:#0f172a; white-space:nowrap; }
    .gm-totals { margin-top:1rem; border-top:2px solid #e5e7eb; padding-top:1rem; }
    .gm-total-row { display:flex; justify-content:space-between; padding:.35rem 0; }
    .gm-total-row--grand { font-size:1.25rem; font-weight:800; border-top:1px solid #e5e7eb; margin-top:.5rem; padding-top:.75rem; }
    .gm-muted { color:#6b7280; font-size:.9rem; }
    .gm-warn { padding:.85rem 1.05rem; border-radius:10px; background:#fffbeb; border:1px solid #fde68a; color:#78350f; margin-bottom:1rem; }
    .gm-err { padding:.85rem 1.05rem; border-radius:10px; background:#fee2e2; border:1px solid #fecaca; color:#991b1b; margin-bottom:1rem; }
    .gm-ok { padding:.85rem 1.05rem; border-radius:10px; background:#d1fae5; border:1px solid #a7f3d0; color:#065f46; margin-bottom:1rem; }
    .gm-remove { background:none; border:none; color:#b91c1c; font-size:.85rem; cursor:pointer; padding:0; text-decoration:underline; }
    .gm-cart-link { position:relative; }
    .gm-cart-count { display:inline-block; min-width:20px; padding:0 6px; border-radius:999px; background:var(--theme-color,#0f3460); color:#fff; font-size:.72rem; font-weight:800; line-height:20px; text-align:center; margin-left:4px; }
    .gm-fieldset { border:none; padding:0; margin:0 0 1.25rem; }
    .gm-fieldset legend { font-size:1.05rem; font-weight:700; color:#0f172a; margin-bottom:.6rem; padding:0; }
    .gm-radio-row { display:flex; flex-wrap:wrap; gap:.6rem; }
    .gm-radio { flex:1 1 200px; display:flex; gap:.6rem; align-items:flex-start; padding:.9rem 1rem; border:2px solid #e5e7eb; border-radius:12px; cursor:pointer; }
    .gm-radio input { margin-top:.2rem; }
    .gm-radio strong { display:block; color:#0f172a; }
    .gm-radio span { font-size:.85rem; color:#6b7280; }
    .gm-radio:has(input:checked) { border-color:var(--theme-color,#0f3460); background:#f8fafc; }
    .gm-secure { display:flex; align-items:center; gap:.5rem; margin-top:1rem; color:#6b7280; font-size:.85rem; }
</style>
@endpush
@endonce
