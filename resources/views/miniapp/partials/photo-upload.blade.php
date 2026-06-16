{{--
    Shared photo-upload control for the mini-app (parts + vehicles).
    Two inputs (camera + file picker) both submit as images[]. Photos accumulate
    across taps from BOTH sources. We never rely on being able to assign
    input.files (Samsung/Safari may ignore it):
      - When the browser allows it, all photos are consolidated into one input
        with a deletable thumbnail preview.
      - When it does not, each pick is kept in its own images[] input so they
        all still submit; empty inputs have their name stripped on submit.
--}}
<div class="form-group">
    <label>Photos *</label>
    <div class="photo-upload-options">
        <button type="button" class="btn-upload-option" id="btn-camera">
            <span class="upload-icon">📷</span> Take Photo
        </button>
        <button type="button" class="btn-upload-option" id="btn-file">
            <span class="upload-icon">🖼️</span> Choose Files
        </button>
    </div>
    <input type="file" id="input-camera" class="photo-input" name="images[]" accept="image/*" capture="environment" multiple style="display:none">
    <input type="file" id="input-file"   class="photo-input" name="images[]" accept="image/*" multiple style="display:none">
    {{-- Holds "locked in" inputs on browsers that can't merge files (fallback). --}}
    <div id="photo-extra-inputs" style="display:none"></div>
    <div id="photo-preview" class="photo-preview-strip"></div>
    <p class="photo-hint" id="photo-count">No photos selected.</p>
    <p class="photo-hint">Add as many as you like — tap a button again to add more.</p>
</div>
