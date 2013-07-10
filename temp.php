<!DOCTYPE html 5>
<html>
<header>
    <title> Plexonic plugin </title>
    <link href="<?php echo PLEX_PLUGIN_DIR; ?>assets/css/style.css" rel="stylesheet" type="text/css" />
</header>
<body>


<?php
    define('plex_dir', plugin_dir_url(__FILE__));

    function plex_add_text_in_footer(){
        echo '<div class="footer_div"> <h1>By Plexonic</h1> </div>';
    }

    function plex_post_icon(){
        echo '<div class="div_post">
        <img src="'.plex_dir.'1.png" />
        </div>';
    }

    add_action ('wp_footer', 'plex_add_text_in_footer');
    add_action ('the_content', 'plex_post_icon');
?>



$plex_drafts = $wpdb->get_results(
"
SELECT ID, post_title
FROM $wpdb->posts
WHERE post_type = 'post' AND post_status = 'publish'
"
);

<select name="post_id">
    <?php
    foreach ($plex_drafts as $plex_draft){
        echo '<option value="'.$plex_draft->ID.'"> '.$plex_draft->post_title.' -- '.$plex_draft->ID.'</option>';
    }
    ?>
    <select>

        <input type="submit" value="Add" name="add_file_name"  />




        add_action('wp_trash_post', 162);

        function delete_func(){
        global $wpdb;

        $wpdbwp->delete(
        "$wpdb->wp_postmeta",
        array("meta_key" => "post_image"),
        array("%s")
        );


        $wpdb->insert(
        'wp_postmeta',
        array('post_id' => 555, 'meta_key' => '5555555', 'meta_value' => '55555555555'),
        array('%d', '%s', '%s')
        );
        }







        $postid = 174;


        add_action( 'before_delete_post', 'my_func' );
        function my_func( $postid ){


        global $post_type;
        if ( $meta_key = 'post_image' ) return;


        }





































//plex plugin's admin panel START

        <?php
        

        define('PLEX_PLUGIN_DIR', plugin_dir_url(__FILE__));
        $uploadDir = wp_upload_dir();
        define('PLEX_UPLOAD_DIR', $uploadDir['basedir']."/post-images");
        define('PLEX_UPLOAD_URL', $uploadDir['baseurl']."/post-images");

        add_action( 'post_edit_form_tag' , 'post_edit_form_tag' );
        function post_edit_form_tag( ) {
            echo 'enctype="multipart/form-data"';
        }

        add_action('edit_form_advanced', 'plex_plugin_admin');
        function plex_plugin_admin( $post )
        {
            global $wpdb;

            $postId = $post->ID;

            $query = sprintf("SELECT * FROM wp_postmeta WHERE post_id = %d AND meta_key = '%s'", $postId, 'post_image');
            $result = $wpdb->get_row($query);

            if ( $result->meta_id )
            {
                ?>
                <img src="<?php echo PLEX_UPLOAD_URL."/".$result->meta_value ?>">
            <?php
            }
            ?>
            <div class="main_div">
                Add icon: <input name="upload_file" type="file" />
            </div>
        <?php
        }

        add_action('save_post', 'plex_post_publish');
        function plex_post_publish( $postId )
        {
            global $wpdb;

            if ( empty($_REQUEST['wp-preview']) )
            {
                $parentId = wp_is_post_revision( $postId );

                $postId = $parentId !== false ? $parentId : $postId;

                $file = $_FILES['upload_file'];
                $fileName = md5($postId).".".getImageExtension($_FILES['upload_file']['type']);

                $result = img_resize($file, PLEX_UPLOAD_DIR, $fileName, 158, 96);

                if(  $result ) {
                    $query = sprintf("SELECT * FROM wp_postmeta WHERE post_id = %d AND meta_key = '%s'", $postId, 'post_image');
                    $result = $wpdb->get_row($query);

                    if ( $result->meta_id )
                    {
                        $wpdb->update(
                            'wp_postmeta',
                            array('meta_value' => $fileName),
                            array('meta_id' => $result->meta_id),
                            array('%s'),
                            array('%d')
                        );
                    }
                    else
                    {
                        $wpdb->insert(
                            'wp_postmeta',
                            array('post_id' => $postId, 'meta_key' => 'post_image', 'meta_value' => $fileName),
                            array('%d', '%s', '%s')
                        );
                    }
                }
            }
        }

        function img_resize($file, $destinationFolder, $fileName, $width, $height, $rgb = 0xFFFFFF, $quality = 100)
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





        add_action('before_delete_post', 'func_del');

        function func_del( $postId )
        {
            global $wpdb;

            $query = sprintf("SELECT meta_value FROM wp_postmeta WHERE post_id = %d AND meta_key = '%s'", $postId, 'post_image');
            $result = $wpdb->get_row($query);
            $imageName = $result->meta_value;

            if ( $imageName )
            {
                $wpdb->delete('wp_postmeta', array('post_id' => $postId, 'meta_key' => 'post_image'), array('%d', '%s'));
                unlink(PLEX_UPLOAD_DIR."/".$imageName);
            }
    }
        ?>

//plex plugin's admin panel END












    //sql join select

    SELECT wp_posts.ID, wp_postmeta.post_id AS id
    FROM wp_posts
    INNER JOIN wp_postmeta ON wp_posts.ID = wp_postmeta.post_id





    SELECT wp_postmeta.post_id, wp_postmeta.meta_key, wp_postmeta.meta_value, wp_posts.post_type
    FROM wp_postmeta
    INNER JOIN wp_posts  ON wp_postmeta.post_id = wp_posts.ID WHERE meta_key = 'post_image' AND post_type = 'post'

    //sql join select end