<?php
/*
Plugin Name: Upload blog post icon
Plugin URI:
Description: Uploading and showing blog post icon
Version: 1.0
Author: Plexonic
Author URI: http://www.plexonic.com
*/


define('PLEX_PLUGIN_DIR', plugin_dir_url(__FILE__));
$uploadDir = wp_upload_dir();
define('PLEX_UPLOAD_DIR', $uploadDir['basedir']."/post-images");
define('PLEX_UPLOAD_URL', $uploadDir['baseurl']."/post-images");
define('PLEX_UPLOAD_TEMP_DIR', $uploadDir['basedir']."/post-images_temp");



function resizePostImage($file, $destinationFolder, $fileName, $width, $height, $rgb = 0xFFFFFF, $quality = 100)
{
    $src = $file["tmp_name"];

    if (!file_exists($src))
        return false;

    $size = getimagesize($src);

    if ($size === false)
        return false;

    $format = strtolower(substr($size['mime'], strpos($size['mime'], '/') + 1));
    $icfunc = "imagecreatefrom" . $format;

    if (!function_exists($icfunc))
        return false;

    $x_ratio = $width / $size[0];
    $y_ratio = $height / $size[1];

    $ratio = min($x_ratio, $y_ratio);
    $use_x_ratio = ($x_ratio == $ratio);

    $new_width = $use_x_ratio ? $width : floor($size[0] * $ratio);
    $new_height = !$use_x_ratio ? $height : floor($size[1] * $ratio);
    $new_left = $use_x_ratio ? 0 : floor(($width - $new_width) / 2);
    $new_top = !$use_x_ratio ? 0 : floor(($height - $new_height) / 2);

    $isrc = $icfunc($src);
    $idest = imagecreatetruecolor($width, $height);

    imagefill($idest, 0, 0, $rgb);

    if (($format == 'gif') or ($format == 'png')) {
        imagealphablending($idest, false);
        imagesavealpha($idest, true);
    }

    if ($format == 'gif') {
        $transparent = imagecolorallocatealpha($idest, 255, 255, 255, 127);
        imagefilledrectangle($idest, 0, 0, $width, $height, $transparent);
        imagecolortransparent($idest, $transparent);
    }

    imagecopyresampled($idest, $isrc, $new_left, $new_top, 0, 0, $new_width, $new_height, $size[0], $size[1]);

    if ( !is_dir($destinationFolder) ) {
        mkdir($destinationFolder, 0775);
    }

    getResultImage($idest, $destinationFolder."/".$fileName, $size['mime']);

    imagedestroy($isrc);
    imagedestroy($idest);

    return true;
}

function getResultImage($dst_r, $dest_path, $type)
{
    $imgExt = getImageExtension( $type );

    switch ($imgExt) {
        case 'jpg':
            return imagejpeg($dst_r, $dest_path, 90);
            break;
        case 'png';
            return imagepng($dst_r, $dest_path, 2);
            break;
        case 'gif';
            return imagegif($dst_r, $dest_path);
            break;
        default:
            return null;
    }
}

function getImageExtension( $type )
{
    switch ($type) {
        case 'image/jpg':
        case 'image/jpeg':
        case 'image/pjpeg':
            return 'jpg';
            break;
        case 'image/png';
            return 'png';
            break;
        case 'image/gif';
            return 'gif';
            break;
        default:
            return '';
    }
}



add_action('post_edit_form_tag', 'func_edit_form');
function func_edit_form() {
    echo 'enctype="multipart/form-data"';
    if( !is_dir(PLEX_UPLOAD_TEMP_DIR) ){
        mkdir(PLEX_UPLOAD_TEMP_DIR, 0775);
    }


}


add_action('edit_form_advanced', 'func_add_icon_admin');
function func_add_icon_admin($post){
    $postImageName = get_post_meta($post->ID, 'post_image', true);
?>
    <link rel="stylesheet" type="text/css" href="<?php echo PLEX_PLUGIN_DIR ?>assets/css/plugin.css" />
    <div id="post-image-sortables" class="meta-box-sortables ui-sortable">
        <div class="postbox">
            <div class="handlediv" title="Click to toggle"><br></div>
            <h3 class="hndle"><span>Post custom image</span></h3>

            <div id="post-image-inside" class="inside">
                <?php if ( !empty($postImageName) ): ?>
                <div id="post-image-preview">
                    <a id="delete-post-image" href="#delete-post-image"></a>
                    <img src="<?php echo PLEX_UPLOAD_URL.'/'.$postImageName; ?>" width="158" />
                </div>
                <div id="post-image-undo" class="hidden">
                    <a id="post-image-undo-link" href="#">Undo delete</a>
                </div>
                <?php endif; ?>

                <input id="delete-image-flag" type="hidden" name="delete_image" />
                <input name="upload_image" type="file" value="<?php echo $postImageName ?>" />
            </div>
        </div>
    </div>
    <script type="text/javascript" src="<?php echo PLEX_PLUGIN_DIR ?>assets/js/plugin.js"></script>
<?php
}


add_action('save_post', 'func_post_publish');
function func_post_publish($postId) {

    if ( !wp_is_post_revision($postId) ) {

        $delete_image = $_POST['delete_image'];
        $imageFile = $_FILES['upload_image'];
        $newImageName = md5($postId).".".getImageExtension($imageFile['type']);

        $resizeResult = resizePostImage($imageFile, PLEX_UPLOAD_DIR, $newImageName, 330, 200); // 158x96



        if ( $resizeResult ) {
            if ( get_post_meta($postId, 'post_image', true) === null ) {
                add_post_meta($postId, 'post_image', $newImageName);
            }
            else {
                update_post_meta($postId, 'post_image', $newImageName);
            }
        }

        if ($delete_image === 'on'){
            func_delete($postId);
        }
    }

}

add_action('before_delete_post', 'func_delete');
function func_delete($postId){
    $postImageName = get_post_meta($postId, 'post_image', true);

    if ( !wp_is_post_revision($postId) && !empty($postImageName) )
    {
        unlink(PLEX_UPLOAD_DIR.'/'.$postImageName);
        delete_post_meta($postId, 'post_image');
    }
}
?>