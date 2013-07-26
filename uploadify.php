<?php
// Define a destination
$targetFolder = '/uploads'; // Relative to the root

function resizePostImage($file, $destinationFolder, $fileName, $width, $height, $rgb = 0xFFFFFF, $quality = 100){

    $src = $file;
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

if (!empty($_FILES)) {
	$tempFile = $_FILES['Filedata']['tmp_name'];
    $imgFileName = $_FILES['Filedata']['name'];
    $arrayFileName = explode(".", $imgFileName);
    $numb = count($arrayFileName) - 1;
    $endOfImgFileName = $arrayFileName[$numb];
    $origFileName = '1.jpg';
	$targetPath = __DIR__ . $targetFolder;
	$targetFile = rtrim($targetPath,'/') . '/' . $origFileName;

    // Validate the file type
	$fileTypes = array('jpg','jpeg','gif','png'); // File extensions
	$fileParts = pathinfo($origFileName);

    $returnResult = array();

	if ( isset($fileParts['extension']) && in_array($fileParts['extension'], $fileTypes))
    {

        $uploadResult = resizePostImage($tempFile, $targetPath, $origFileName, 330, 200); // 158x96

        if ( $uploadResult )
        {
            $returnResult['success'] = true;
            $returnResult['fileName'] = $origFileName;
        }
        else
        {
            $returnResult['success'] = false;
            $returnResult['message'] = 'Uploaded file cannot be moved to uploads folder.';
        }
	}
    else
    {
        $returnResult['success'] = false;
        $returnResult['message'] = 'Uploaded file type is invalid (should be an image).';
	}

    echo json_encode($returnResult);
}