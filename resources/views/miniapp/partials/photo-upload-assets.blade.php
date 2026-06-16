{{-- Styles + script for the shared photo-upload control. Include once per page. --}}
@push('styles')
<style>
.photo-upload-options { display: flex; gap: 0.75rem; margin-bottom: 0.75rem; }
.btn-upload-option {
    flex: 1; display: flex; flex-direction: column; align-items: center; gap: 0.3rem;
    padding: 0.85rem 0.5rem; font-size: 0.95rem; font-weight: 600;
    background: #f1f5f9; border: 2px solid #cbd5e1; border-radius: 12px;
    cursor: pointer; transition: border-color 0.15s, background 0.15s;
}
.btn-upload-option:active { background: #e2e8f0; border-color: #94a3b8; }
.upload-icon { font-size: 1.6rem; line-height: 1; }
.photo-preview-strip { display: flex; flex-wrap: wrap; gap: 0.5rem; margin-bottom: 0.5rem; }
.photo-thumb-wrap { position: relative; width: 72px; height: 72px; border-radius: 8px; overflow: hidden; border: 2px solid #cbd5e1; }
.photo-thumb { width: 100%; height: 100%; object-fit: cover; display: block; }
.photo-thumb-del {
    position: absolute; top: 2px; right: 2px; width: 20px; height: 20px;
    background: rgba(0,0,0,0.6); color: #fff; border: none; border-radius: 50%;
    font-size: 0.8rem; line-height: 1; cursor: pointer;
    display: flex; align-items: center; justify-content: center;
}
</style>
@endpush

@push('scripts')
<script>
(function() {
    var preview     = document.getElementById('photo-preview');
    var countLabel  = document.getElementById('photo-count');
    var extra       = document.getElementById('photo-extra-inputs');
    var cameraInput = document.getElementById('input-camera');
    var fileInput   = document.getElementById('input-file');
    var form        = cameraInput.closest('form');

    function forEach(list, fn) { Array.prototype.forEach.call(list, fn); }
    function setCount(n) { countLabel.textContent = n ? n + ' photo(s) selected.' : 'No photos selected.'; }

    // Can this browser actually have files assigned to an input? (Samsung/Safari may not.)
    var canSetFiles = (function() {
        try {
            var dt = new DataTransfer();
            dt.items.add(new File(['x'], 't.txt', { type: 'text/plain' }));
            var i = document.createElement('input'); i.type = 'file'; i.files = dt.files;
            return i.files.length === 1;
        } catch (e) { return false; }
    })();

    document.getElementById('btn-camera').addEventListener('click', function() { cameraInput.click(); });
    document.getElementById('btn-file').addEventListener('click', function()   { fileInput.click(); });

    if (canSetFiles) {
        // --- Enhanced path: accumulate everything into fileInput; deletable preview ---
        var allFiles = [];

        function writeBack() {
            var dt = new DataTransfer();
            allFiles.forEach(function(f) { dt.items.add(f); });
            fileInput.files = dt.files;   // (does not fire 'change')
        }

        function render() {
            preview.innerHTML = '';
            allFiles.forEach(function(file, idx) {
                var wrap = document.createElement('div'); wrap.className = 'photo-thumb-wrap';
                var img = document.createElement('img'); img.className = 'photo-thumb';
                img.src = URL.createObjectURL(file);
                var del = document.createElement('button');
                del.type = 'button'; del.className = 'photo-thumb-del'; del.innerHTML = '&times;';
                del.addEventListener('click', function() { allFiles.splice(idx, 1); writeBack(); render(); });
                wrap.appendChild(img); wrap.appendChild(del); preview.appendChild(wrap);
            });
            setCount(allFiles.length);
        }

        function onPick() {
            // Append this selection (never reset), then clear the input so the
            // same file can be picked again and the camera input stays empty.
            forEach(this.files, function(f) { allFiles.push(f); });
            this.value = '';
            writeBack();
            render();
        }

        cameraInput.addEventListener('change', onPick);
        fileInput.addEventListener('change', onPick);
    } else {
        // --- Fallback: keep each pick in its own images[] input so all submit ---
        function renderAll() {
            preview.innerHTML = '';
            var n = 0;
            forEach(form.querySelectorAll('input.photo-input'), function(inp) {
                if (!inp.files) return;
                forEach(inp.files, function(file) {
                    n++;
                    var wrap = document.createElement('div'); wrap.className = 'photo-thumb-wrap';
                    var img = document.createElement('img'); img.className = 'photo-thumb';
                    try { img.src = URL.createObjectURL(file); } catch (e) {}
                    wrap.appendChild(img); preview.appendChild(wrap);
                });
            });
            setCount(n);
        }

        function lockIn() {
            if (!this.files || this.files.length === 0) return;
            var isCamera = this.getAttribute('capture') !== null;
            var fresh = this.cloneNode(false);   // empty clone, same attrs (incl. name/capture/class)
            this.parentNode.insertBefore(fresh, this.nextSibling);
            this.removeAttribute('id');          // avoid duplicate ids; keep id on the fresh one
            extra.appendChild(this);             // hidden, but still submits its files
            fresh.addEventListener('change', lockIn);
            if (isCamera) { cameraInput = fresh; } else { fileInput = fresh; }
            renderAll();
        }

        cameraInput.addEventListener('change', lockIn);
        fileInput.addEventListener('change', lockIn);
    }

    // Every browser: never submit an empty file input (a stray empty images[]
    // entry was the original 500 trigger), and show progress feedback.
    if (form) {
        form.addEventListener('submit', function() {
            forEach(form.querySelectorAll('input[type=file]'), function(inp) {
                if (!inp.files || inp.files.length === 0) { inp.removeAttribute('name'); }
            });
            var btn = document.getElementById('btn-submit');
            var status = document.getElementById('upload-status');
            if (btn) { btn.disabled = true; btn.textContent = 'Uploading…'; }
            if (status) { status.style.display = 'block'; }
        });
    }
})();
</script>
@endpush
