(function () {
  if (typeof TINYMCE_VARS === 'undefined') return;

  const { language, urls, translations, selectors } = TINYMCE_VARS;

  // --- TinyMCE init ---
  tinymce.init({
    selector: selectors.editor,
  base_url: '/admin/assets/plugins/tinymce', // cartella "root" di TinyMCE
  suffix: '.min',                             // se usi i file .min.js
  license_key: 'gpl',                         // opzionale, non obbligatorio
    height: 400,
    menubar: true,
    plugins: [
      'advlist','autolink','lists','link','image','charmap','preview',
      'anchor','searchreplace','visualblocks','code','fullscreen',
      'insertdatetime','media','table','help','wordcount'
    ],
    toolbar: 'undo redo | blocks | bold italic forecolor | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | removeformat | image media link | code | help',
    content_style: 'body { font-family: Helvetica, Arial, sans-serif; font-size:14px }',
    language: language,
    relative_urls: false,
    remove_script_host: false,
    convert_urls: true,
    branding: false,
    image_uploadtab: true,
    automatic_uploads: true,
    file_picker_types: 'image',
    file_picker_callback: function (cb, value, meta) {
      if (meta.filetype === 'image') {
        openMediaLibrary(function (imageUrl) {
          cb(imageUrl, { alt: '' });
        });
      }
    }
  });

  // --- Media library helpers ---
  function qs(sel) { return document.querySelector(sel); }

  const selectBtn = qs(selectors.selectImageBtn);
  const removeBtn = qs(selectors.removeImageBtn);
  const featuredInput = qs(selectors.featuredImageInput);
  const previewWrap = qs(selectors.imagePreviewWrapper);
  const previewImg  = qs(selectors.imagePreviewImg);

  if (selectBtn) {
    selectBtn.addEventListener('click', function () {
      openMediaLibrary(function (imageUrl) {
        if (featuredInput) featuredInput.value = imageUrl;
        if (previewImg) previewImg.src = imageUrl;
        if (previewWrap) previewWrap.style.display = 'block';
        if (removeBtn) removeBtn.style.display = 'inline-block';
      });
    });
  }

  if (removeBtn) {
    removeBtn.addEventListener('click', function () {
      if (featuredInput) featuredInput.value = '';
      if (previewWrap) previewWrap.style.display = 'none';
      this.style.display = 'none';
    });
  }

  function openMediaLibrary(callback) {
    const modal = document.createElement('div');
    modal.className = 'modal fade';
    modal.id = 'mediaLibraryModal';
    modal.innerHTML = `
      <div class="modal-dialog modal-xl">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">${translations.selectMedia}</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body p-0">
            <iframe src="${urls.mediaSelector}" style="width:100%; height:500px; border:none;"></iframe>
          </div>
        </div>
      </div>
    `;
    document.body.appendChild(modal);
    const bsModal = new bootstrap.Modal(modal);
    bsModal.show();

    // Fallback legacy callback (se il selettore media lo usa)
    window.onMediaSelected = function (media) {
      try { callback(media.url); } finally { bsModal.hide(); modal.remove(); }
    };

    // postMessage dal selettore media
    const onMessage = function (e) {
      if (e?.data?.type === 'mediaSelected') {
        try { callback(e.data.media.url); } finally { bsModal.hide(); modal.remove(); }
      } else if (e?.data?.type === 'closeModal') {
        bsModal.hide(); modal.remove();
      }
    };
    window.addEventListener('message', onMessage, { once: true });
  }

  // --- Preview iniziale se già presente ---
  document.addEventListener('DOMContentLoaded', function () {
    if (!featuredInput) return;
    const featuredImage = featuredInput.value;
    if (featuredImage) {
      if (previewImg) previewImg.src = featuredImage;
      if (previewWrap) previewWrap.style.display = 'block';
      if (removeBtn) removeBtn.style.display = 'inline-block';
    }
  });
})();