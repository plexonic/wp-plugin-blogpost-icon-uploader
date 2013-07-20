<?php
// Define a destination
$targetFolder = '/uploads'; // Relative to the root

if (!empty($_FILES)) {



	$tempFile = $_FILES['Filedata']['tmp_name'];
    $imgFileName = $_FILES['Filedata']['name'];
    $arrayFileName = explode(".", $imgFileName);
    $numb = count($arrayFileName) - 1;
    $endOfImgFileName = $arrayFileName[$numb];
    $origFileName = '1'.'.'.$endOfImgFileName;
	$targetPath = __DIR__ . $targetFolder;
	$targetFile = rtrim($targetPath,'/') . '/' . $origFileName;

    // Validate the file type
	$fileTypes = array('jpg','jpeg','gif','png'); // File extensions
	$fileParts = pathinfo($origFileName);

    $returnResult = array();

	if ( isset($fileParts['extension']) && in_array($fileParts['extension'], $fileTypes))
    {
		$uploadResult = move_uploaded_file($tempFile, $targetFile);

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