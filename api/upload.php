<?php
/**
 * upload.php — POST /api/upload (admin protected, rate-limited)
 *
 * Validates the uploaded file's real bytes (not just the client-supplied
 * mimetype/extension, which can be spoofed) and saves it to /uploads/.
 */

const ALLOWED_MIME_EXTENSIONS = [
    'image/png' => '.png',
    'image/jpeg' => '.jpg',
    'image/webp' => '.webp',
    'image/gif' => '.gif'
];

function sniffImageSignature(string $bytes): bool {
    if (strlen($bytes) < 12) {
        return false;
    }
    // PNG
    if (substr($bytes, 0, 8) === "\x89PNG\x0d\x0a\x1a\x0a") {
        return true;
    }
    // JPEG
    if (substr($bytes, 0, 3) === "\xff\xd8\xff") {
        return true;
    }
    // GIF87a / GIF89a
    $sig6 = substr($bytes, 0, 6);
    if ($sig6 === 'GIF87a' || $sig6 === 'GIF89a') {
        return true;
    }
    // WEBP (RIFF....WEBP)
    if (substr($bytes, 0, 4) === 'RIFF' && substr($bytes, 8, 4) === 'WEBP') {
        return true;
    }
    return false;
}

function handleUpload(): void {
    requireAdmin();

    if (!rateLimit('upload', 15 * 60, 60)) {
        jsonResponse(['error' => 'Too many upload requests. Please try again later.'], 429);
    }

    if (!isset($_FILES['image'])) {
        jsonResponse(['error' => 'No image file provided'], 400);
    }

    $file = $_FILES['image'];

    if ($file['error'] !== UPLOAD_ERR_OK) {
        $message = $file['error'] === UPLOAD_ERR_INI_SIZE || $file['error'] === UPLOAD_ERR_FORM_SIZE
            ? 'Image is too large. Please choose a file under 5MB.'
            : 'Upload failed';
        jsonResponse(['error' => $message], 400);
    }

    // 5MB limit
    if ($file['size'] > 5 * 1024 * 1024) {
        jsonResponse(['error' => 'Image is too large. Please choose a file under 5MB.'], 400);
    }

    $header = @file_get_contents($file['tmp_name'], false, null, 0, 12);
    if ($header === false || !sniffImageSignature($header)) {
        jsonResponse(['error' => 'The uploaded file is not a valid image.'], 400);
    }

    // Determine the extension from the sniffed signature rather than trusting
    // the client-supplied mimetype.
    $ext = '.jpg';
    if (substr($header, 0, 8) === "\x89PNG\x0d\x0a\x1a\x0a") {
        $ext = '.png';
    } elseif (substr($header, 0, 3) === "\xff\xd8\xff") {
        $ext = '.jpg';
    } elseif (substr($header, 0, 6) === 'GIF87a' || substr($header, 0, 6) === 'GIF89a') {
        $ext = '.gif';
    } elseif (substr($header, 0, 4) === 'RIFF' && substr($header, 8, 4) === 'WEBP') {
        $ext = '.webp';
    }

    if (!is_dir(UPLOADS_DIR)) {
        @mkdir(UPLOADS_DIR, 0755, true);
    }

    $filename = bin2hex(random_bytes(16)) . $ext;
    $destination = UPLOADS_DIR . '/' . $filename;

    if (!@move_uploaded_file($file['tmp_name'], $destination)) {
        jsonResponse(['error' => 'Could not save the uploaded file.'], 500);
    }

    jsonResponse(['success' => true, 'url' => rtrim(BASE_PATH, '/') . '/uploads/' . $filename]);
}
