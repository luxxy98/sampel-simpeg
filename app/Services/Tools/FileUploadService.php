<?php

namespace App\Services\Tools;

use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use InvalidArgumentException;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;

final class FileUploadService
{
    private const int CHUNK_SIZE = 4096;

    private const int|float MAX_FILE_SIZE = 5 * 1024 * 1024;

    public function __construct(private readonly string $disk = 'public')
    {
    }

    /**
     * Public method untuk validasi file sebelum upload
     * Dipanggil dari controller untuk memastikan file aman
     */
    public function validateFileForUpload(UploadedFile $file): void
    {
        $this->validateFile($file);
    }

    /**
     * Validasi file upload dengan security checks
     */
    private function validateFile(UploadedFile $file): void
    {
        // Basic validation
        if (!$file->isValid()) {
            throw new InvalidArgumentException('File upload gagal atau corrupt');
        }

        if ($file->getSize() > self::MAX_FILE_SIZE) {
            throw new InvalidArgumentException('File terlalu besar. Maksimal ' . (self::MAX_FILE_SIZE / 1024 / 1024) . 'MB');
        }

        // Extension validation
        $extension = strtolower($file->getClientOriginalExtension());
        $allowedTypes = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'pdf'];

        if (!in_array($extension, $allowedTypes)) {
            throw new InvalidArgumentException("Tipe file tidak diizinkan: $extension");
        }

        // Enhanced security validation
        $this->validateFileContent($file);
        $this->validateMimeType($file, $extension);
        $this->scanForMaliciousContent($file);
    }

    /**
     * Validasi konten file berdasarkan magic bytes
     */
    private function validateFileContent(UploadedFile $file): void
    {
        $filePath = $file->getPathname();
        $fileHandle = fopen($filePath, 'rb');

        if (!$fileHandle) {
            throw new InvalidArgumentException('Tidak dapat membaca file');
        }

        // Baca magic bytes pertama
        $magicBytes = fread($fileHandle, 16);
        fclose($fileHandle);

        $extension = strtolower($file->getClientOriginalExtension());

        // Validasi magic bytes untuk setiap tipe file
        $this->validateMagicBytes($magicBytes, $extension);
    }

    /**
     * Validasi magic bytes file header
     */
    private function validateMagicBytes(string $magicBytes, string $extension): void
    {
        $validSignatures = [
            'pdf' => [
                "\x25\x50\x44\x46", // %PDF
            ],
            'jpg' => [
                "\xFF\xD8\xFF\xE0", // JFIF
                "\xFF\xD8\xFF\xE1", // EXIF
                "\xFF\xD8\xFF\xE8", // SPIFF
            ],
            'jpeg' => [
                "\xFF\xD8\xFF\xE0", // JFIF
                "\xFF\xD8\xFF\xE1", // EXIF
                "\xFF\xD8\xFF\xE8", // SPIFF
            ],
            'png' => [
                "\x89\x50\x4E\x47\x0D\x0A\x1A\x0A", // PNG
            ],
            'gif' => [
                "GIF87a", // GIF87a
                "GIF89a", // GIF89a
            ],
            'webp' => [
                "RIFF", // WEBP (first 4 bytes)
            ]
        ];

        if (!isset($validSignatures[$extension])) {
            throw new InvalidArgumentException("Format file tidak didukung: $extension");
        }

        $isValid = false;
        foreach ($validSignatures[$extension] as $signature) {
            if (str_starts_with($magicBytes, $signature)) {
                $isValid = true;
                break;
            }
        }

        if (!$isValid) {
            throw new InvalidArgumentException("File tidak sesuai dengan format $extension yang sebenarnya. Kemungkinan file telah diubah atau corrupted");
        }
    }

    /**
     * Validasi MIME type vs extension
     */
    private function validateMimeType(UploadedFile $file, string $extension): void
    {
        $serverMime = $file->getMimeType();
        $validMimeTypes = [
            'pdf' => ['application/pdf'],
            'jpg' => ['image/jpeg'],
            'jpeg' => ['image/jpeg'],
            'png' => ['image/png'],
            'gif' => ['image/gif'],
            'webp' => ['image/webp']
        ];

        if (!isset($validMimeTypes[$extension])) {
            throw new InvalidArgumentException("Extension tidak didukung: $extension");
        }

        // Cek MIME type dari server (lebih dapat dipercaya)
        if ($serverMime && !in_array($serverMime, $validMimeTypes[$extension])) {
            throw new InvalidArgumentException("MIME type file ($serverMime) tidak sesuai dengan extension .$extension");
        }
    }

    /**
     * Get MIME type berdasarkan extension
     */
    private function getMimeType(string $filename): string
    {
        $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

        return match ($extension) {
            'pdf' => 'application/pdf',
            'jpg', 'jpeg' => 'image/jpeg',
            'png' => 'image/png',
            'gif' => 'image/gif',
            'webp' => 'image/webp',
            default => 'application/octet-stream'
        };
    }

    /**
     * Scan konten file untuk mendeteksi potensi malicious content
     */
    private function scanForMaliciousContent(UploadedFile $file): void
    {
        $filePath = $file->getPathname();
        $content = file_get_contents($filePath);

        if ($content === false) {
            throw new InvalidArgumentException('Tidak dapat membaca konten file');
        }

        // Deteksi script/executable content dalam file
        $maliciousPatterns = [
            // JavaScript patterns
            '/<script[^>]*>/i',
            '/javascript:/i',
            '/eval\s*\(/i',
            '/document\.write/i',
            '/window\.location/i',

            // PHP patterns
            '/<\?php/i',
            '/<\?=/i',
            '/eval\s*\(/i',
            '/system\s*\(/i',
            '/exec\s*\(/i',
            '/shell_exec\s*\(/i',
            '/passthru\s*\(/i',
            '/file_get_contents\s*\(/i',
            '/file_put_contents\s*\(/i',

            // Binary executable signatures
            '/MZ[\x00-\xFF]{58}PE/s', // PE executable
            '/\x7fELF/s', // ELF executable
            '/\xca\xfe\xba\xbe/s', // Mach-O

            // Suspicious PDF content
            '/\/JavaScript\s*\(/i',
            '/\/JS\s*\(/i',
            '/\/Launch\s*\(/i',
            '/\/EmbeddedFile\s*\(/i',

            // Shell commands
            '/\b(wget|curl|nc|netcat|bash|sh|cmd|powershell)\s+/i',

            // Suspicious strings
            '/\b(backdoor|malware|trojan|virus|exploit)\b/i',
        ];

        foreach ($maliciousPatterns as $pattern) {
            if (preg_match($pattern, $content)) {
                throw new InvalidArgumentException('File mengandung konten yang mencurigakan dan tidak dapat diterima untuk alasan keamanan');
            }
        }

        // Validasi khusus untuk PDF
        if (strtolower($file->getClientOriginalExtension()) === 'pdf') {
            $this->validatePdfSecurity($content);
        }
    }

    /**
     * Validasi keamanan khusus untuk PDF
     */
    private function validatePdfSecurity(string $content): void
    {
        // Deteksi PDF dengan JavaScript
        if (preg_match('/\/JavaScript\s*\(|\/JS\s*\(/i', $content)) {
            throw new InvalidArgumentException('PDF mengandung JavaScript dan tidak diizinkan');
        }

        // Deteksi PDF dengan launch actions
        if (preg_match('/\/Launch\s*\(|\/GoToR\s*\(/i', $content)) {
            throw new InvalidArgumentException('PDF mengandung launch action yang tidak diizinkan');
        }

        // Deteksi embedded files dalam PDF
        if (preg_match('/\/EmbeddedFile\s*\(|\/EmbeddedFiles\s*/i', $content)) {
            throw new InvalidArgumentException('PDF mengandung embedded file dan tidak diizinkan');
        }

        // Deteksi form actions mencurigakan
        if (preg_match('/\/SubmitForm\s*\(|\/ImportData\s*\(/i', $content)) {
            throw new InvalidArgumentException('PDF mengandung form action yang mencurigakan');
        }
    }

    /**
     * Hapus file berdasarkan type
     */
    public function deleteFileByType(string $fileName, string $type): bool
    {
        $config = $this->getConfigForType($type);

        return $this->deleteFile($fileName, $config['directory']);
    }

    /**
     * Konfigurasi untuk berbagai tipe file
     */
    private function getConfigForType(string $type): array
    {
        return match ($type) {
            'person_foto' => ['directory' => 'person'],
            'pendidikan' => ['directory' => 'pendidikan'],
            'golongan' => ['directory' => 'golongan'],
            'struktural' => ['directory' => 'struktural'],
            'homebase' => ['directory' => 'homebase'],
            'jakad' => ['directory' => 'jakad'],
            'sertifikasi' => ['directory' => 'sertifikasi'],
            default => ['directory' => 'dokumen'],
        };
    }

    /**
     * Hapus file
     */
    public function deleteFile(string $fileName, string $directory): bool
    {
        $filePath = $directory . '/' . $fileName;

        return Storage::disk($this->disk)->delete($filePath);
    }

    /**
     * View file dengan optimasi streaming
     */
    public function viewFile(Request $request, string $dir, string $filename): BinaryFileResponse|StreamedResponse
    {
        $filePath = $dir . '/' . $filename;
        $disk = Storage::disk($this->disk);

        if (!$disk->exists($filePath)) {
            abort(404, 'File tidak ditemukan');
        }

        $absolutePath = $disk->path($filePath);
        $size = $disk->size($filePath);
        $mimeType = $this->getMimeType($filename);

        $range = $request->header('Range');
        if ($range || $size > self::MAX_FILE_SIZE) {
            return $this->servePartialContent($absolutePath, $size, $mimeType, $range, $filename);
        }

        return response()->file($absolutePath, ['Content-Type' => $mimeType, 'Content-Length' => $size, 'Content-Disposition' => 'inline; filename="' . $filename . '"', 'Cache-Control' => 'private, max-age=3600', 'Expires' => gmdate('D, d M Y H:i:s', time() + 3600) . ' GMT']);
    }

    /**
     * Serve partial content dengan optimasi streaming
     */
    private function servePartialContent(string $filePath, int $fileSize, string $mimeType, ?string $range, string $filename): StreamedResponse
    {
        $start = 0;
        $end = $fileSize - 1;

        if ($range && preg_match('/bytes=(\d+)-(\d*)/i', $range, $matches)) {
            $start = (int)$matches[1];
            $end = $matches[2] ? (int)$matches[2] : $fileSize - 1;

            if ($start >= $fileSize || $end >= $fileSize || $start > $end) {
                abort(416, 'Range Not Satisfiable');
            }
        }

        $length = $end - $start + 1;

        return new StreamedResponse(function () use ($filePath, $start, $length) {
            $handle = fopen($filePath, 'rb');
            if (!$handle) {
                return;
            }

            fseek($handle, $start);
            $bytesToRead = $length;

            while ($bytesToRead > 0 && !feof($handle)) {
                $chunkSize = min(self::CHUNK_SIZE, $bytesToRead);
                $chunk = fread($handle, $chunkSize);

                if ($chunk === false) {
                    break;
                }

                echo $chunk;
                $bytesToRead -= strlen($chunk);

                if (ob_get_level()) {
                    ob_flush();
                }
                flush();
            }

            fclose($handle);
        }, $range ? 206 : 200, ['Content-Type' => $mimeType, 'Content-Length' => $length, 'Content-Range' => $range ? "bytes $start-$end/$fileSize" : null, 'Content-Disposition' => 'inline; filename="' . $filename . '"', 'Accept-Ranges' => 'bytes', 'Cache-Control' => 'private, max-age=3600', 'Expires' => gmdate('D, d M Y H:i:s', time() + 3600) . ' GMT']);
    }

    /**
     * Upload dengan template filename
     */
    public function uploadWithTemplate(UploadedFile $file, string $type, string $template, array $data): array
    {
        $customFileName = $this->generateCustomFileName($template, $data, $file);

        return $this->uploadByType($file, $type, $customFileName);
    }

    /**
     * Generate custom filename dengan template
     */
    public function generateCustomFileName(string $template, array $data, UploadedFile $file): string
    {
        $extension = strtolower($file->getClientOriginalExtension());

        // Replace placeholders dalam template
        $fileName = $template;
        foreach ($data as $key => $value) {
            $fileName = str_replace('{' . $key . '}', $this->sanitizeForFilename($value), $fileName);
        }

        // Tambahkan timestamp jika diperlukan
        $fileName = str_replace('{timestamp}', time(), $fileName);
        $fileName = str_replace('{date}', date('Ymd'), $fileName);
        $fileName = str_replace('{datetime}', date('YmdHis'), $fileName);

        // Pastikan ada extension
        if (!str_ends_with(strtolower($fileName), '.' . $extension)) {
            $fileName .= '.' . $extension;
        }

        return $fileName;
    }

    /**
     * Sanitize string untuk filename
     */
    private function sanitizeForFilename(string $string): string
    {
        // Remove atau replace karakter yang tidak diinginkan
        $string = preg_replace('/[^a-zA-Z0-9\-_.]/', '_', $string);
        $string = preg_replace('/_+/', '_', $string); // Multiple underscore jadi satu

        return trim($string, '_');
    }

    /**
     * Upload file dengan konfigurasi yang sudah ditentukan
     */
    public function uploadByType(UploadedFile $file, string $type, ?string $customFileName = null): array
    {
        $config = $this->getConfigForType($type);

        return $this->upload($file, $config['directory'], $customFileName);
    }

    /**
     * Upload file dengan optimasi low resource
     */
    private function upload(UploadedFile $file, string $directory, ?string $customFileName = null): array
    {
        $this->validateFile($file);

        $fileName = $customFileName ?: $this->generateDefaultFileName($file);
        $path = Storage::disk($this->disk)->putFileAs($directory, $file, $fileName);

        return ['file_name' => $fileName, 'original_name' => $file->getClientOriginalName(), 'path' => $path, 'size' => $file->getSize(), 'mime_type' => $file->getClientMimeType() ?: $file->getMimeType(), 'extension' => strtolower($file->getClientOriginalExtension()), 'url' => asset('storage/' . $path)];
    }

    /**
     * Generate nama file default
     */
    private function generateDefaultFileName(UploadedFile $file): string
    {
        $extension = strtolower($file->getClientOriginalExtension());

        return str_replace('-', '', (string)Str::uuid()) . '.' . $extension;
    }

    /**
     * Update dengan template filename
     */
    public function updateWithTemplate(UploadedFile $newFile, ?string $oldFileName, string $type, string $template, array $data): array
    {
        $customFileName = $this->generateCustomFileName($template, $data, $newFile);

        return $this->updateFileByType($newFile, $oldFileName, $type, $customFileName);
    }

    /**
     * Update file berdasarkan type
     */
    public function updateFileByType(UploadedFile $newFile, ?string $oldFileName, string $type, ?string $customFileName = null): array
    {
        $config = $this->getConfigForType($type);

        return $this->updateFile($newFile, $config['directory'], $oldFileName, $customFileName);
    }

    /**
     * Update file - hapus yang lama dan upload yang baru
     */
    private function updateFile(UploadedFile $newFile, string $directory, ?string $oldFileName = null, ?string $customFileName = null): array
    {
        $uploadResult = $this->upload($newFile, $directory, $customFileName);

        if ($oldFileName) {
            $this->deleteFile($oldFileName, $directory);
        }

        return $uploadResult;
    }
}
