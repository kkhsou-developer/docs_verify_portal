<?php
// Start session to access or set CAPTCHA code
session_start();

// Ensure session is active
if (session_status() !== PHP_SESSION_ACTIVE) {
    die('Session not started.');
}

// --- Improved CAPTCHA Generation ---
// Generate a new 5-character alphanumeric CAPTCHA on every request for better security.
$char_pool = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
$captcha_code = substr(str_shuffle($char_pool), 0, 5);
$_SESSION['captcha_code'] = $captcha_code;

// --- Image Dimensions ---
$width = 150;
$height = 40; // Set image height to 40px

// Create CAPTCHA image
$image = imagecreatetruecolor($width, $height);
if (!$image) {
    die('Failed to create CAPTCHA image.');
}

// Allocate colors
$bg_color = imagecolorallocate($image, 255, 255, 255); // White background
$text_color = imagecolorallocate($image, 20, 20, 20);     // Dark gray text
$line_color = imagecolorallocate($image, 200, 200, 200); // Light gray lines
$noise_color = imagecolorallocate($image, 220, 220, 220); // Lighter gray noise

// Fill background
imagefilledrectangle($image, 0, 0, $width, $height, $bg_color);

// Add noise pixels
for ($i = 0; $i < 200; $i++) {
    imagesetpixel($image, random_int(0, $width), random_int(0, $height), $noise_color);
}

// Add noise lines
for ($i = 0; $i < 5; $i++) {
    imageline($image, 0, random_int(0, $height), $width, random_int(0, $height), $line_color);
}

// --- Add CAPTCHA text ---
// Use built-in font and dynamically center the text
$font = 5; // Built-in font for simplicity

$font_width = imagefontwidth($font) * strlen($captcha_code);
$font_height = imagefontheight($font);
$x_pos = ($width - $font_width) / 2;
$y_pos = ($height - $font_height) / 2;

imagestring($image, $font, $x_pos, $y_pos, $captcha_code, $text_color);

// Output image as PNG
header('Content-Type: image/png');
imagepng($image);
imagedestroy($image);
?>