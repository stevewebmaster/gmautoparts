@extends('miniapp.layout')
@section('title', 'Add a part')

@push('styles')
<style>
.photo-upload-options {
    display: flex;
    gap: 0.75rem;
    margin-bottom: 0.75rem;
}
.btn-upload-option {
    flex: 1;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 0.3rem;
    padding: 0.85rem 0.5rem;
    font-size: 0.95rem;
    font-weight: 600;
    background: #f1f5f9;
    border: 2px solid #cbd5e1;
    border-radius: 12px;
    cursor: pointer;
    transition: border-color 0.15s, background 0.15s;
}
.btn-upload-option:active {
    background: #e2e8f0;
    border-color: #94a3b8;
}
.upload-icon {
    font-size: 1.6rem;
    line-height: 1;
}
.photo-preview-strip {
    display: flex;
    flex-wrap: wrap;
    gap: 0.5rem;
    margin-bottom: 0.5rem;
}
.photo-thumb-wrap {
    position: relative;
    width: 72px;
    height: 72px;
    border-radius: 8px;
    overflow: hidden;
    border: 2px solid #cbd5e1;
}
.photo-thumb {
    width: 100%;
    height: 100%;
    object-fit: cover;
    display: block;
}
.photo-thumb-del {
    position: absolute;
    top: 2px;
    right: 2px;
    width: 20px;
    height: 20px;
    background: rgba(0,0,0,0.6);
    color: #fff;
    border: none;
    border-radius: 50%;
    font-size: 0.8rem;
    line-height: 1;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
}
</style>
@endpush

@section('content')
    <div class="card-app">
    <h1 class="page-title">Add a Part</h1>
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
            <label>Photos *</label>
            <div class="photo-upload-options">
                <button type="button" class="btn-upload-option" id="btn-camera">
                    <span class="upload-icon">📷</span> Camera
                </button>
                <button type="button" class="btn-upload-option" id="btn-file">
                    <span class="upload-icon">🖼️</span> File Upload
                </button>
            </div>
            {{-- input-camera has no name so it never submits directly; JS merges into input-file --}}
            <input type="file" id="input-camera" accept="image/*" capture="environment" multiple style="display:none">
            <input type="file" id="input-file"   name="images[]" accept="image/*" multiple style="display:none">
            <div id="photo-preview" class="photo-preview-strip"></div>
            <p class="photo-hint" id="photo-count">No photos selected.</p>
        </div>
        <button type="submit" class="btn-app btn-primary-app">Save part</button>
    </form>
    <a href="{{ route('app.dashboard') }}" class="back-link">← Back</a>
    </div>
@endsection

@push('scripts')
<script>
// Photo upload: camera vs file picker
(function() {
    var allFiles = [];
    var cameraInput = document.getElementById('input-camera');
    var fileInput   = document.getElementById('input-file');
    var preview     = document.getElementById('photo-preview');
    var countLabel  = document.getElementById('photo-count');
    var canDataTransfer = (function() {
        try { new DataTransfer(); return true; } catch(e) { return false; }
    })();

    document.getElementById('btn-camera').addEventListener('click', function() { cameraInput.click(); });
    document.getElementById('btn-file').addEventListener('click', function()   { fileInput.click(); });

    function addFiles(fileList) {
        for (var i = 0; i < fileList.length; i++) {
            allFiles.push(fileList[i]);
        }
        renderPreview();
    }

    function renderPreview() {
        preview.innerHTML = '';
        allFiles.forEach(function(file, idx) {
            var wrap = document.createElement('div');
            wrap.className = 'photo-thumb-wrap';
            var img = document.createElement('img');
            img.src = URL.createObjectURL(file);
            img.className = 'photo-thumb';
            var del = document.createElement('button');
            del.type = 'button';
            del.className = 'photo-thumb-del';
            del.innerHTML = '&times;';
            del.dataset.idx = idx;
            del.addEventListener('click', function() {
                allFiles.splice(parseInt(this.dataset.idx), 1);
                renderPreview();
            });
            wrap.appendChild(img);
            wrap.appendChild(del);
            preview.appendChild(wrap);
        });
        countLabel.textContent = allFiles.length ? allFiles.length + ' photo(s) selected.' : 'No photos selected.';
        if (canDataTransfer) {
            var dt = new DataTransfer();
            allFiles.forEach(function(f) { dt.items.add(f); });
            fileInput.files = dt.files;
        }
    }

    cameraInput.addEventListener('change', function() {
        if (canDataTransfer) {
            addFiles(this.files);
        } else {
            // Fallback: swap camera input to have the name so it submits directly
            cameraInput.name = 'images[]';
            fileInput.name   = '';
            countLabel.textContent = this.files.length + ' photo(s) selected (camera).';
        }
    });
    fileInput.addEventListener('change', function() {
        if (canDataTransfer) {
            addFiles(this.files);
        } else {
            // Fallback: ensure file input has the name and camera doesn't
            fileInput.name   = 'images[]';
            cameraInput.name = '';
            countLabel.textContent = this.files.length + ' photo(s) selected.';
        }
    });
})();

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
