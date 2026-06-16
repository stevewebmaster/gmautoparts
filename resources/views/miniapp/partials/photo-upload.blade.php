{{--
    Shared photo-upload control for the mini-app (parts + vehicles).
    Two named inputs (camera + file) both submit as images[]. We never rely on
    being able to assign input.files (Samsung/Safari silently ignore it):
      - When the browser DOES allow it, we consolidate into one input and show
        a deletable thumbnail preview.
      - When it does NOT, both inputs keep their own native FileList and we just
        strip the name from any empty input on submit, so only real files post.
--}}
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
    <input type="file" id="input-camera" name="images[]" accept="image/*" capture="environment" multiple style="display:none">
    <input type="file" id="input-file"   name="images[]" accept="image/*" multiple style="display:none">
    <div id="photo-preview" class="photo-preview-strip"></div>
    <p class="photo-hint" id="photo-count">No photos selected.</p>
</div>
