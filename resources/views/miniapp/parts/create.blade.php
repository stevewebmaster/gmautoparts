@extends('miniapp.layout')
@section('title', 'Add a part')

@section('content')
    <h1 style="font-size: 1.35rem; margin-bottom: 1rem;">Add a part</h1>
    <form method="post" action="{{ route('app.parts.store') }}" enctype="multipart/form-data">
        @csrf
        <div class="form-group">
            <label for="title">Part name / title *</label>
            <input type="text" id="title" name="title" value="{{ old('title') }}" required placeholder="e.g. Alternator">
        </div>
        <div class="form-group">
            <label for="part_category_id">Category *</label>
            <select id="part_category_id" name="part_category_id" required>
                <option value="">Select category</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat->id }}" {{ old('part_category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="form-group" id="subcategory-wrap">
            <label for="part_subcategory_id">Subcategory</label>
            <select id="part_subcategory_id" name="part_subcategory_id">
                <option value="">Select subcategory (optional)</option>
            </select>
        </div>
        <div class="form-group">
            <label for="description">Description</label>
            <textarea id="description" name="description" placeholder="Condition, notes...">{{ old('description') }}</textarea>
        </div>
        <div class="form-group">
            <label for="price">Price ($)</label>
            <input type="number" id="price" name="price" value="{{ old('price') }}" min="0" step="0.01" placeholder="Leave blank for Enquire">
        </div>
        <div class="form-group">
            <label for="condition">Condition</label>
            <input type="text" id="condition" name="condition" value="{{ old('condition') }}" placeholder="e.g. Used, Refurbished">
        </div>
        <div class="form-group">
            <label for="make">Make (vehicle fit)</label>
            <input type="text" id="make" name="make" value="{{ old('make') }}" placeholder="e.g. Holden">
        </div>
        <div class="form-group">
            <label for="model">Model</label>
            <input type="text" id="model" name="model" value="{{ old('model') }}" placeholder="e.g. Commodore">
        </div>
        <div class="form-group">
            <label for="year">Year</label>
            <input type="text" id="year" name="year" value="{{ old('year') }}" placeholder="e.g. 2010">
        </div>
        <div class="form-group">
            <label for="stock_number">Stock number</label>
            <input type="text" id="stock_number" name="stock_number" value="{{ old('stock_number') }}">
        </div>
        <div class="form-group">
            <label for="vehicle_id">From vehicle (Now Dismantling)</label>
            <select id="vehicle_id" name="vehicle_id">
                <option value="">None</option>
                @foreach($vehicles as $v)
                    <option value="{{ $v->id }}" {{ old('vehicle_id') == $v->id ? 'selected' : '' }}>{{ $v->display_name }}</option>
                @endforeach
            </select>
        </div>
        <div class="form-group">
            <label for="images">Photos *</label>
            <input type="file" id="images" name="images[]" accept="image/*" capture="environment" multiple>
            <p class="photo-hint">Take or select at least one photo. On phone you can use camera.</p>
        </div>
        <button type="submit" class="btn-app btn-primary-app">Save part</button>
    </form>
    <p style="margin-top: 1rem;"><a href="{{ route('app.dashboard') }}" style="color: #666; font-size: 0.9rem;">← Back</a></p>
@endsection

@push('scripts')
<script>
document.getElementById('part_category_id').addEventListener('change', function() {
    var id = this.value;
    var sub = document.getElementById('part_subcategory_id');
    sub.innerHTML = '<option value="">Loading...</option>';
    if (!id) { sub.innerHTML = '<option value="">Select subcategory (optional)</option>'; return; }
    fetch('{{ route("app.subcategories", ["category" => "__ID__"]) }}'.replace('__ID__', id), {
        credentials: 'same-origin',
        headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
    }).then(function(r) { return r.json(); }).then(function(data) {
        sub.innerHTML = '<option value="">Select subcategory (optional)</option>';
        data.forEach(function(s) { sub.innerHTML += '<option value="' + s.id + '">' + s.name + '</option>'; });
    }).catch(function() { sub.innerHTML = '<option value="">Select subcategory (optional)</option>'; });
});
var catId = document.getElementById('part_category_id').value;
if (catId) document.getElementById('part_category_id').dispatchEvent(new Event('change'));
</script>
@endpush
