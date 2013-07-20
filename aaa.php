<?php

define('PLEX_PLUGIN_DIR', plugin_dir_url(__FILE__));
$uploadDir = wp_upload_dir();
define('PLEX_UPLOAD_DIR', $uploadDir['basedir']."/post-images");
define('PLEX_UPLOAD_URL', $uploadDir['baseurl']."/post-images");
define('PLEX_UPLOAD_TEMP_DIR', $uploadDir['basedir']."/post-images_temp");


add_action('edit_form_advanced', 'func_add_icon_admin');
function func_add_icon_admin(){
?>
    <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js" type="text/javascript"></script>
    <script src="<?php echo PLEX_PLUGIN_DIR ?>jquery.uploadify.min.js" type="text/javascript"></script>
    <link rel="stylesheet" type="text/css" href="<?php echo PLEX_PLUGIN_DIR ?>uploadify.css">

    <div id="queue"></div>
    <input id="file_upload" name="file_upload" type="file" multiple="true">


    <div id="imagePreviewContainer"></div>



    <script type="text/javascript">
        <?php $timestamp = time();?>
        $(function() {
            $('#file_upload').uploadify({
                swf : '<?php echo PLEX_PLUGIN_DIR ?>uploadify.swf',
                uploader: '<?php echo PLEX_PLUGIN_DIR ?>uploadify.php',
                onUploadSuccess: function(file, data, response) {
                    var result = JSON.parse( data );

                    $("#imagePreviewContainer").html( '<img src="<?php echo PLEX_PLUGIN_DIR ?>uploads/' + result.fileName + '" width="158">' );

                }
            });
        });
    </script>



<?php
var_dump(PLEX_UPLOAD_TEMP_DIR);
}
?>

















