{{-- Freight size picker with a box-measuring helper. Expects $selected (a
     ShippingBand value, or null). Options are in size order, smallest first,
     which the helper relies on to pick the smallest box that fits. --}}
@php($labels = \App\Enums\ShippingBand::optionsWithRates())
<div class="form-group">
    <label for="shipping_band">Freight size</label>
    <select id="shipping_band" name="shipping_band">
        <option value="">Not measured yet — not sold online</option>
        @foreach(\App\Enums\ShippingBand::cases() as $size)
            <option value="{{ $size->value }}"
                    @if($size->cube()) data-cube="{{ $size->cube() }}" data-kg="{{ $size->maxKg() }}" @endif
                    {{ $selected === $size->value ? 'selected' : '' }}>
                {{ $labels[$size->value] }}
            </option>
        @endforeach
    </select>
    <p class="photo-hint">The satchel or box it goes in, same as in eDespatchIT. Sets what the customer pays for freight.</p>
</div>

<div class="form-group freight-measure">
    <label>Or measure the box (cm, kg)</label>
    <div class="freight-measure-row">
        <input type="number" inputmode="decimal" min="0" step="any" placeholder="L" data-measure="l" aria-label="Length in cm">
        <input type="number" inputmode="decimal" min="0" step="any" placeholder="W" data-measure="w" aria-label="Width in cm">
        <input type="number" inputmode="decimal" min="0" step="any" placeholder="H" data-measure="h" aria-label="Height in cm">
        <input type="number" inputmode="decimal" min="0" step="any" placeholder="kg" data-measure="kg" aria-label="Weight in kg">
    </div>
    <p class="photo-hint" id="freight-measure-result">Picks the smallest box that fits. Small enough for a satchel? Choose E20 or E60 above instead.</p>
</div>

@once
@push('styles')
<style>
    .freight-measure-row { display: grid; grid-template-columns: repeat(4, 1fr); gap: 0.4rem; }
    .freight-measure-row input { min-width: 0; }
</style>
@endpush

@push('scripts')
<script>
    (function () {
        var select = document.getElementById('shipping_band');
        var result = document.getElementById('freight-measure-result');
        var fields = {};
        document.querySelectorAll('[data-measure]').forEach(function (el) {
            fields[el.dataset.measure] = el;
            el.addEventListener('input', pick);
        });

        function pick() {
            var l = parseFloat(fields.l.value), w = parseFloat(fields.w.value), h = parseFloat(fields.h.value);
            var kg = parseFloat(fields.kg.value) || 0;

            if (!(l > 0 && w > 0 && h > 0)) return;

            var cube = l * w * h / 1000000;
            var fits = Array.prototype.find.call(select.options, function (option) {
                return option.dataset.cube && cube <= parseFloat(option.dataset.cube) && kg <= parseFloat(option.dataset.kg);
            });

            if (fits) {
                select.value = fits.value;
                result.textContent = cube.toFixed(3) + ' m³ → ' + fits.text.trim();
            } else {
                select.value = @json(\App\Enums\ShippingBand::QuoteOnly->value);
                result.textContent = cube.toFixed(3) + ' m³' + (kg > 25 ? ', over 25 kg' : '') + ' — too big for courier. Set to truck freight.';
            }
        }
    })();
</script>
@endpush
@endonce
