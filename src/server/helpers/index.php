<?php

use MatthiasMullie\Minify;
use Intervention\Image\ImageManager;

class Cache
{
    private static $cache = array();

    public static function set($key, $value, $expiration = 3600)
    {
        $expirationTime = time() + $expiration;
        self::$cache[$key] = array('value' => $value, 'expiration' => $expirationTime);
    }

    public static function get($key)
    {
        if (isset(self::$cache[$key]) && self::$cache[$key]['expiration'] > time()) {
            return self::$cache[$key]['value'];
        }

        // If the key is not in the cache or has expired
        unset(self::$cache[$key]); // Clear the expired cache entry
        return null;
    }

    public static function clear($key)
    {
        unset(self::$cache[$key]);
    }

    public static function clearAll()
    {
        self::$cache = array();
    }

    // Automatic expiration mechanism
    public static function cleanup()
    {
        foreach (self::$cache as $key => $cacheEntry) {
            if ($cacheEntry['expiration'] <= time()) {
                unset(self::$cache[$key]);
            }
        }
    }
}


function setCSPHeader() {
    $nonce = hash('sha256', 'inline-scripts');
    header("Content-Security-Policy: default-src 'self'; script-src 'self' 'nonce-$nonce' 'unsafe-inline'; style-src 'self' 'unsafe-inline'; object-src 'none'; base-uri 'self'; frame-ancestors 'none'; form-action 'self'; frame-src 'none'; img-src 'self' data:; connect-src 'self'; font-src 'self'; media-src 'self'; manifest-src 'self'; worker-src 'self'; report-uri /report-csp-violation;");
}

function setSecurityHeaders() {
    // Set X-Content-Type-Options header to prevent MIME sniffing
    header("X-Content-Type-Options: nosniff");
    // Set X-Frame-Options header to prevent Clickjacking
    header("X-Frame-Options: DENY");
    // Set X-XSS-Protection header to enable XSS filtering
    header("X-XSS-Protection: 1; mode=block");
    // Set Strict-Transport-Security header to enforce HTTPS
    header("Strict-Transport-Security: max-age=31536000; includeSubDomains; preload");
    // Set Referrer-Policy header to control the information sent to other websites when a user clicks on a link
    header("Referrer-Policy: same-origin");
}

function minifyHTML($contentType, $buffer)
{
    $response = "";

    if (strpos($contentType, 'text/html') !== false) {
        if(strpos($buffer,'<pre>') !== false) {
            $replace = array(
                '/<!--[^\[](.*?)[^\]]-->/s' => '',
                "/<\?php/"                  => '<?php ',
                "/\r/"                      => '',
                "/>\n</"                    => '><',
                "/>\s+\n</"                 => '><',
                "/>\n\s+</"                 => '><',
            );
        } else {
            $replace = array(
                '/<!--[^\[](.*?)[^\]]-->/s' => '',
                "/<\?php/"                  => '<?php ',
                "/\n([\S])/"                => '$1',
                "/\r/"                      => '',
                "/\n/"                      => '',
                "/\t/"                      => '',
                "/>\s+/"                      => '>',
                "/\s+</"                      => '<',
                "/\s+\/>/"                      => '/>',
                "/ +/"                      => ' ',
            );
        }
        $buffer = preg_replace(array_keys($replace), array_values($replace), $buffer);
        $response = $buffer;
    }

    return $response;

}


function servePublicAsset($publicDirectory, $requestUrlWithoutQuery, $cache_duration) {
    $publicFilePath = $publicDirectory . ltrim($requestUrlWithoutQuery, '/');

    if (file_exists($publicFilePath)) {
        // Serve the public asset file directly
        $fileType = pathinfo($publicFilePath, PATHINFO_EXTENSION);
        $allowedTypes = ['css', 'js', 'png', 'jpg', 'jpeg', 'gif', 'ico', 'html', 'woff2', 'woff', 'ttf', 'txt', 'svg'];

        if (in_array($fileType, $allowedTypes)) {
            // Start output buffering
            ob_start();

            // Output the file content
            readfile($publicFilePath);

            // Get the content from the buffer and clean the buffer
            $content = ob_get_clean();

            $contentType = get_content_type($publicFilePath);

            header('Cache-Control: public, max-age=' . $cache_duration);

            if (in_array($fileType, ['svg'])){
                header('Content-Type: image/svg+xml');
                echo $content;
                exit;
            } elseif (in_array($fileType, ['css', 'js', 'html'])) {
                if ($fileType == 'html') {
                    $content = minifyHTML($contentType, $content);
                } else {
                    $minifier = ($fileType === 'css') ? new Minify\CSS($content) : (new Minify\JS($content));
                    $content = $minifier->minify();
                }
                header("Content-type: $contentType");

                echo $content;
                exit;
            } elseif (in_array($fileType, ['woff2', 'woff', 'ttf'])) {
                // Fonts: Set appropriate headers
                header("Content-type: $contentType");
                header("Access-Control-Allow-Origin: *"); // Adjust as needed

                echo $content;
                exit;
            } elseif (in_array($fileType, ['txt'])) {
                echo $content;
                exit;
            }

            // Check if width and height parameters are provided for images
            $width = isset($_GET['width']) ? (int)$_GET['width'] : null;
            $height = isset($_GET['height']) ? (int)$_GET['height'] : null;

            // Validate the desired file type to prevent security risks
            $allowedImageTypes = ['webp', 'avif', 'png', 'jpg', 'jpeg', 'gif'];

            // Get the desired file type from the query parameters (default to png if not provided)
            $desiredFileType = isset($_GET['type']) ? strtolower($_GET['type']) : 'png';

            if (!in_array($desiredFileType, $allowedImageTypes)) {
                // Set the appropriate content type based on the file type
                header("Content-type: $contentType");
            } else {
                header("Content-type: image/" . $desiredFileType);
            }

            if (!in_array($desiredFileType, $allowedImageTypes)) {
                // Invalid file type requested, respond with an error or use a default
                echo 'Invalid file type requested';
                exit;
            }

            // create image manager with desired driver
            $manager = new ImageManager(new \Intervention\Image\Drivers\Gd\Driver());

            // Load the original image using Intervention
            $image = $manager->read($publicFilePath);

            // Resize the image while maintaining aspect ratio
            $image->resizeDown($width, $height);

            // Convert the image to the desired file type
            $imageData = $image->encodeByExtension($desiredFileType);

            // Output the resized and converted image
            echo $imageData;
            exit;
        }
    }
}

function constructFilePath($baseDirectory, $requestUrlWithoutQuery) {
    $filePath = $baseDirectory . ltrim($requestUrlWithoutQuery, '/');

    if (!is_dir($filePath)) {
        $filePath .= '.php';
    } else {
        $filePath .= '/index.php';
    }
    return $filePath;
}

function handleMissingFile($filePath) {
    if (!file_exists($filePath) || pathinfo($filePath, PATHINFO_EXTENSION) !== 'php') {
        // Handle the case when the requested PHP file does not exist
        echo '404 Not Found';
        exit;
    }
}

function streamContent($content) {
    // Set an initial chunk size suitable for very low response times
    $initialChunkSize = 500;

    // Calculate the total length of the content
    $contentLength = strlen($content);

    // Calculate the number of chunks based on the total length
    $numChunks = ceil($contentLength / $initialChunkSize);

    // Adjust chunk size dynamically for faster delivery with a lower limit
    $chunkSize = max(128, ceil($contentLength / $numChunks));

    // Output the HTML in chunks
    for ($i = 0; $i < $numChunks; $i++) {
        $startPos = $i * $chunkSize;
        $chunk = substr($content, $startPos, $chunkSize);

        // Output the chunk
        echo $chunk;
        ob_flush();
        flush();
    }

    ob_flush();
    flush();
}


function includeAndHandleLayout($filePath, $layoutFilePath, $cache_duration, $db, ...$args) {
    global $nonce, $currentUrl, $appUrl;

    if (isset($args[0])){
        foreach ($args as $key => $value) {
            foreach ($value as $var_name => $var_val) {
                $$var_name = $var_val;
            }
        }
    }

    // Start output buffering
    ob_start();

    // Include the PHP file
    include $filePath;

    // Get the content from the buffer
    $content = ob_get_clean();

    // Minify the HTML content while preserving JavaScript and CSS
    $minifiedContent = minifyHTML('text/html', $content);

    // Check if $cache_duration is set and greater than 0 for caching
    if (isset($cache_duration) && $cache_duration > 0) {
        header('Cache-Control: public, max-age=' . $cache_duration);
    }

    // Apply the layout if found
    if ($layoutFilePath) {
        // Start output buffering
        ob_start();

        // Include the layout PHP file
        include $layoutFilePath;

        // Get the content from the buffer
        $layoutContent = minifyHTML('text/html', ob_get_clean());
        $layoutContent = str_replace('<page />', $minifiedContent, $layoutContent);
        $layoutContent = str_replace('<page/>', $minifiedContent, $layoutContent);

        streamContent($layoutContent);
    } else {
        // No layout found, just output the minified content
        streamContent($minifiedContent);
    }
}


/**
 * Find the layout file for the given page.
 *
 * @param string $pageUrl The URL of the page.
 * @return string|false The path to the layout file if found, false otherwise.
 */
function find_layout_file($pageUrl)
{
    $layoutDirectory = __DIR__ . '/../../client/pages/';

    // Explode the URL into segments
    $urlSegments = explode('/', trim($pageUrl, '/'));

    // Iterate through each segment and check for a layout file
    foreach ($urlSegments as $index => $segment) {
        $potentialLayoutPath = $layoutDirectory . implode('/', array_slice($urlSegments, 0, $index + 1)) . '/layout.php';

        if (file_exists($potentialLayoutPath)) {
            return $potentialLayoutPath;
        }
    }

    // Check for the root layout file
    $rootLayoutPath = $layoutDirectory . 'layout.php';
    if (file_exists($rootLayoutPath)) {
        return $rootLayoutPath;
    }

    return false;
}

/**
 * Get the content type based on the file extension.
 *
 * @param string $filePath The path to the file.
 * @return string|false The content type if found, false otherwise.
 */
function get_content_type($filePath)
{
    $content_types = [
        'css' => 'text/css',
        'js' => 'application/javascript',
        'html' => 'text/html',
        'png' => 'image/png',
        'jpg' => 'image/jpg',
        'svg' => 'image/svg+xml',
        'jpeg' => 'image/jpeg',
        'gif' => 'image/gif',
        'ico' => 'image/ico',
        'woff2' => 'font/woff2',
        'woff' => 'font/woff',
        'ttf' => 'font/ttf',
        'txt' => 'text/plain',
    ];

    $ext = pathinfo($filePath, PATHINFO_EXTENSION);

    $returnVal = isset($ext) ? $content_types[$ext] : "";

    return $returnVal;
}


/**
 * Requires all PHP files in the specified folder path.
 *
 * @param string $folderPath The path of the folder containing PHP files.
 * @return void
 */
function requireAllFilesInFolder($folderPath)
{
    foreach (glob($folderPath . '/*.php') as $filename) {
        require_once $filename;
    }
    foreach (glob($folderPath . '/**/*.php') as $filename) {
        require_once $filename;
    }
    foreach (glob($folderPath . '/**/**/*.php') as $filename) {
        require_once $filename;
    }
}