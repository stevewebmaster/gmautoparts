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
    var cameraInput = document.getElementById('input-camera');
    var fileInput   = document.getElementById('input-file');
    var preview     = document.getElementById('photo-preview');
    var countLabel  = document.getElementById('photo-count');
    var form        = cameraInput.closest('form');

    // Can this browser actually have files assigned to an input? (Samsung/Safari can't.)
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

    // --- Enhanced path: consolidate everything into fileInput, deletable preview ---
    var allFiles = [];

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
            del.addEventListener('click', function() {
                allFiles.splice(idx, 1);
                writeBack();
                renderPreview();
            });
            wrap.appendChild(img);
            wrap.appendChild(del);
            preview.appendChild(wrap);
        });
        countLabel.textContent = allFiles.length ? allFiles.length + ' photo(s) selected.' : 'No photos selected.';
    }

    function writeBack() {
        var dt = new DataTransfer();
        allFiles.forEach(function(f) { dt.items.add(f); });
        fileInput.files = dt.files;
    }

    function addToAll(fileList) {
        for (var i = 0; i < fileList.length; i++) { allFiles.push(fileList[i]); }
    }

    if (canSetFiles) {
        cameraInput.addEventListener('change', function() {
            addToAll(this.files);
            this.value = '';        // clear camera input so it doesn't double-submit
            writeBack();            // everything now lives in fileInput
            renderPreview();
        });
        fileInput.addEventListener('change', function() {
            // fileInput may already hold consolidated files; rebuild allFiles from it
            allFiles = [];
            addToAll(this.files);
            renderPreview();
        });
    } else {
        // --- Fallback path: no preview/delete; just report counts. Both native
        //     inputs keep their own files and post directly. ---
        function updateCount() {
            var n = (cameraInput.files ? cameraInput.files.length : 0) +
                    (fileInput.files ? fileInput.files.length : 0);
            countLabel.textContent = n ? n + ' photo(s) selected.' : 'No photos selected.';
        }
        cameraInput.addEventListener('change', updateCount);
        fileInput.addEventListener('change', updateCount);
    }

    // Belt-and-braces for every browser: never submit an empty file input
    // (a stray empty images[] entry is what caused the original 500).
    if (form) {
        form.addEventListener('submit', function() {
            [cameraInput, fileInput].forEach(function(inp) {
                if (!inp.files || inp.files.length === 0) {
                    inp.removeAttribute('name');
                }
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
