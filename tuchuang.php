<?php
/*
Plugin Name: 小昂裕的百宝库
Plugin URI: https://xiaoangyu.cc
Description: 自动将上传到WordPress媒体库的图片转存到指定的图床，并替换URL。
Author: 小昂裕
Author URI: https://xiaoangyu.cc
*/

if (!defined('ABSPATH')) {
    exit;
}

define('SCV_CDN_API_URL', 'https://换成你的图床网址/api/index.php');
define('SCV_CDN_API_TOKEN', '换成你的token');

add_action('admin_init', 'scdn_register_settings');
function scdn_register_settings() {
}

add_action('admin_menu', 'scdn_add_admin_menu');
function scdn_add_admin_menu() {
    add_options_page(
        '图床设置',
        '图床',
        'manage_options',
        'scdn-settings',
        'scdn_settings_page'
    );
}

function scdn_settings_page() {
    ?>
    <div class="wrap">
        <h1><?php echo esc_html(get_admin_page_title()); ?></h1>

        <div class="notice notice-warning is-dismissible" style="padding: 1rem;">
            <p><strong>重要提示：</strong></p>
            <p>请不要轻易禁用或卸载本插件。禁用或卸载将导致之前通过本插件上传的所有图片在您的网站上无法显示（链接失效）。</p>
            <p>但请放心，您的图片文件本身仍然安全地存储在图床服务器上，并不会丢失。</p>
        </div>

        <div class="notice notice-info is-dismissible" style="padding: 1rem;">
            <p><strong>图床信息：</strong></p>
            <p>当前使用 EasyImages2.0 图床，API地址：<?php echo esc_html(SCV_CDN_API_URL); ?></p>
            <p>图片格式转换由图床服务器自动处理，无需在此设置。</p>
        </div>
    </div>
    <?php
}

add_filter('wp_generate_attachment_metadata', 'scdn_handle_upload', 20, 2);

function scdn_handle_upload($metadata, $attachment_id)
{
    if (!wp_attachment_is_image($attachment_id)) {
        return $metadata;
    }

    $file_path = get_attached_file($attachment_id);
    if (!$file_path || !file_exists($file_path)) {
        error_log("SCV-CDN Uploader: File not found for attachment ID {$attachment_id}.");
        return $metadata;
    }

    $ch = curl_init();
    $cfile = new CURLFile($file_path, mime_content_type($file_path), basename($file_path));
    $post_data = [
        'image' => $cfile,
        'token' => SCV_CDN_API_TOKEN,
    ];

    curl_setopt($ch, CURLOPT_URL, SCV_CDN_API_URL);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);

    $response = curl_exec($ch);
    $curl_error = curl_error($ch);
    curl_close($ch);

    if ($curl_error) {
        error_log("SCV-CDN Uploader cURL Error: " . $curl_error . " - Response: " . $response);
        return $metadata;
    }

    $result = json_decode($response, true);

    if (isset($result['result']) && $result['result'] === 'success' && !empty($result['url'])) {
        $remote_url = $result['url'];

        update_post_meta($attachment_id, '_scdn_url', $remote_url);
        
        $upload_dir = wp_upload_dir();
        if (file_exists($file_path)) {
            unlink($file_path);
        }
        if (isset($metadata['sizes']) && is_array($metadata['sizes'])) {
            foreach ($metadata['sizes'] as $size => $size_data) {
                $thumb_file = $upload_dir['basedir'] . '/' . dirname($metadata['file']) . '/' . $size_data['file'];
                if (file_exists($thumb_file)) {
                    unlink($thumb_file);
                }
            }
        }

        $metadata['file'] = '';
        $metadata['sizes'] = [];

        error_log("SCV-CDN Uploader: Successfully uploaded attachment ID {$attachment_id} to {$remote_url}");

    } else {
        $error_message = isset($result['message']) ? $result['message'] : 'Unknown API error.';
        $error_code = isset($result['code']) ? $result['code'] : 'Unknown code';
        error_log("SCV-CDN Uploader API Error for attachment ID {$attachment_id}: Code {$error_code} - {$error_message}");
    }
    
    return $metadata;
}

function scdn_get_attachment_url($url, $attachment_id)
{
    $remote_url = get_post_meta($attachment_id, '_scdn_url', true);
    return $remote_url ? $remote_url : $url;
}
add_filter('wp_get_attachment_url', 'scdn_get_attachment_url', 10, 2);

function scdn_get_attachment_image_src($image, $attachment_id, $size, $icon)
{
    $remote_url = get_post_meta($attachment_id, '_scdn_url', true);
    if ($remote_url && is_array($image)) {
        $image[0] = $remote_url;
    }
    return $image;
}
add_filter('wp_get_attachment_image_src', 'scdn_get_attachment_image_src', 10, 4);

function scdn_prepare_attachment_for_js($response, $attachment, $meta)
{
    $remote_url = get_post_meta($attachment->ID, '_scdn_url', true);
    if ($remote_url) {
        $response['url'] = $remote_url;
        if (isset($response['sizes']) && is_array($response['sizes'])) {
            foreach ($response['sizes'] as $size => &$size_data) {
                $size_data['url'] = $remote_url;
            }
        }
    }
    return $response;
}
add_filter('wp_prepare_attachment_for_js', 'scdn_prepare_attachment_for_js', 10, 3);