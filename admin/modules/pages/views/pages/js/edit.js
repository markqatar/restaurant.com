tinymce.init({
    selector: '.tinymce-editor',
    base_url: '/admin/assets/plugins/tinymce', // cartella "root" di TinyMCE
    suffix: '.min',                             // se usi i file .min.js
    license_key: 'gpl',                         // opzionale, non obbligatorio
    height: 400,
    menubar: true,
    plugins: [
        'advlist', 'autolink', 'lists', 'link', 'image', 'charmap', 'preview',
        'anchor', 'searchreplace', 'visualblocks', 'code', 'fullscreen',
        'insertdatetime', 'media', 'table', 'help', 'wordcount'
    ],
    toolbar: 'undo redo | blocks | ' +
        'bold italic forecolor | alignleft aligncenter ' +
        'alignright alignjustify | bullist numlist outdent indent | ' +
        'removeformat | image media link | code | help',
    content_style: 'body { font-family:Helvetica,Arial,sans-serif; font-size:14px }',
    language: '<?php echo get_current_language(); ?>',
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

// Image selection functionality
document.getElementById('selectImageBtn').addEventListener('click', function () {
    openMediaLibrary(function (imageUrl) {
        document.getElementById('featured_image').value = imageUrl;
        document.getElementById('previewImg').src = imageUrl;
        document.getElementById('imagePreview').style.display = 'block';
        document.getElementById('removeImageBtn').style.display = 'inline-block';
    });
});

document.getElementById('removeImageBtn').addEventListener('click', function () {
    document.getElementById('featured_image').value = '';
    document.getElementById('previewImg').src = '';
    document.getElementById('imagePreview').style.display = 'none';
    this.style.display = 'none';
});

// Simple media library opener (to be enhanced)
function openMediaLibrary(callback) {
    // For now, just prompt for URL - will be replaced with proper media library
    const imageUrl = prompt('Inserisci URL dell\'immagine:');
    if (imageUrl) {
        callback(imageUrl);
    }
}

// Initialize image preview if image already selected
document.addEventListener('DOMContentLoaded', function () {
    const featuredImage = document.getElementById('featured_image').value;
    if (featuredImage) {
        document.getElementById('previewImg').src = featuredImage;
        document.getElementById('imagePreview').style.display = 'block';
        document.getElementById('removeImageBtn').style.display = 'inline-block';
    } else {
        document.getElementById('imagePreview').style.display = 'none';
        document.getElementById('removeImageBtn').style.display = 'none';
    }
});
