<?php
/*
Plugin Name: Upload blog post icon
Plugin URI:
Description: Uploading and showing blog post icon
Version: 1.0
Author: Plexonic
Author URI: http://www.plexonic.com
*/


define('PLEX_PLUGIN_URL', plugin_dir_url(__FILE__));
define('PLEX_PLUGIN_DIR', plugin_dir_path(__FILE__));
$uploadDir = wp_upload_dir();
define('PLEX_UPLOAD_DIR', $uploadDir['basedir']."/post-images");
define('PLEX_UPLOAD_URL', $uploadDir['baseurl']."/post-images");


add_action('post_edit_form_tag', 'func_edit_form');
function func_edit_form() {
    echo 'enctype="multipart/form-data"';

}


add_action('edit_form_advanced', 'func_add_icon_admin');
function func_add_icon_admin($post){
    $postImageName = get_post_meta($post->ID, 'post_image', true);
    ?>
    <link rel="stylesheet" type="text/css" href="<?php echo PLEX_PLUGIN_URL ?>assets/css/plugin.css" />

    <div id="post-image-sortables" class="meta-box-sortables ui-sortable">
        <div class="postbox">

            <div class="handlediv" title="Click to toggle"><br></div>
            <h3 class="hndle"><span>Post custom image</span></h3>
            <div id="post-image-inside" class="inside">
                <div id="imagePreviewContainer"></div>
                <?php if ( !empty($postImageName) ): ?>

                    <div id="post-image-preview">
                        <a id="delete-post-image" href="#delete-post-image"></a>

                        <img class="plug_img" src="<?php echo PLEX_UPLOAD_URL.'/'.$postImageName; ?>" width="158" />
                    </div>


                <?php endif; ?>

                <input id="delete-image-flag" type="hidden" name="delete_image" />
                <input id="delete_flag_new" type="hidden" name="delete_flag_new" value="" />
                <input id="file_upload" name="file_upload" type="file" value="<?php echo $postImageName ?>" />
                <div id="divImgName"> </div>
                <div id="queue"></div>
            </div>
        </div>
    </div>

    <script type="text/javascript" src="<?php echo PLEX_PLUGIN_URL ?>assets/js/plugin.js"></script>
    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js" type="text/javascript"></script>
    <script src="<?php echo PLEX_PLUGIN_URL ?>assets/js/jquery.uploadify.min.js" type="text/javascript"></script>
    <link rel="stylesheet" type="text/css" href="<?php echo PLEX_PLUGIN_URL ?>assets/css/uploadify.css">

    <script type="text/javascript">
        <?php $timestamp = time();?>
        $(function() {
            $('#file_upload').uploadify({
                swf : '<?php echo PLEX_PLUGIN_URL ?>uploadify.swf',
                uploader: '<?php echo PLEX_PLUGIN_URL ?>uploadify.php',
                onUploadSuccess: function(file, data, response) {
                    var result = JSON.parse( data );

                    $("#imagePreviewContainer").html( '<div id="post-image-preview-new"><a id="delete-post-image-new" href="#delete-post-image"></a><img id="plug_img_new" class="plug_img_new" src="<?php echo PLEX_PLUGIN_URL ?>uploads/' + result.fileName + '" width="158"></div>' );
                    $('#divImgName').html('<input name="inputImgName" type="hidden" value="' + result.fileName + '" />');
                    $('#post-image-preview').hide();

                    var deleteButton_new = document.querySelector("#delete-post-image-new");


                    deleteButton_new.addEventListener("click", function( e ) {
                        $("#post-image-preview-new").addClass("hidden");
                        $("#delete_flag_new").val("on");
                        e.preventDefault();

                    });
                },
                'onUploadStart': function(file){
                    $('#post-image-preview-new').append('<div id="loder_div" ><img style="position: absolute; top: 32px; left: 64px;" src="<?php echo PLEX_PLUGIN_URL; ?>assets/img/loading.gif" ></div>');
                },
                multi: false
            });
        });
    </script>

<?php
}


add_action('save_post', 'func_post_publish');
function func_post_publish($postId) {
    if ( !wp_is_post_revision($postId) ) {

        $resizeResult = false;
        $imgTempName = $_POST['inputImgName'];
        $delete_flag_new = $_POST['delete_flag_new'];
        $delete_image = $_POST['delete_image'];
        $path = PLEX_PLUGIN_DIR. 'uploads/';
        $newImageName = md5($postId).".jpg";

        if( ($imgTempName !== null && $delete_flag_new !== 'on' ) ){
            $resizeResult = rename($path.$imgTempName, PLEX_UPLOAD_DIR.'/'.$newImageName);
        }

        if ( $resizeResult ) {
        	$postImageName = $newImageName.'?r='.rand();
            if ( get_post_meta($postId, 'post_image', true) === null ) {
                add_post_meta($postId, 'post_image', $postImageName);
            }
            else {
                update_post_meta($postId, 'post_image', $postImageName);
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

    if ( !wp_is_post_revision($postId) && !empty($postImageName) ){
    	$postImageParts = explode('?', $postImageName);
        $postImageName = $postImageParts[0];
        unlink(PLEX_UPLOAD_DIR.'/'.$postImageName);
        delete_post_meta($postId, 'post_image');
    }
}
?>