<?php

/* * **************************************
  Example of how to use this uploader class...
  You can uncomment the following lines (minus the require) to use these as your defaults.

  // list of valid extensions, ex. array("jpeg", "xml", "bmp")
  $allowedExtensions = array();
  // max file size in bytes
  $sizeLimit = 10 * 1024 * 1024;

  require('valums-file-uploader/server/php.php');
  $uploader = new FileUploader($allowedExtensions, $sizeLimit);

  // Call handleUpload() with the name of the folder, relative to PHP's getcwd()
  $result = $uploader->handleUpload('uploads/');

  // to pass data through iframe you will need to encode all html tags
  echo htmlspecialchars(json_encode($result), ENT_NOQUOTES);

 * **************************************** */

/**
 * Handle file uploads via XMLHttpRequest
 */
class UploadedFileXhr {

    function getFileContent() {
        return file_get_contents("php://input");
    }

    function getName() {
        return $_GET['file'];
    }

    function getSize() {
        if (isset($_SERVER["CONTENT_LENGTH"])) {
            return (int) $_SERVER["CONTENT_LENGTH"];
        } else {
            throw new Exception('Getting content length is not supported.');
        }
    }

}

/**
 * Handle file uploads via regular form post (uses the $_FILES array)
 */
class UploadedFileForm {

    function getFileContent() {
        return file_get_contents($_FILES['file']['tmp_name']/* "php://input" */);
    }

    function getName() {
        return $_FILES['file']['name'];
    }

    function getSize() {
        return $_FILES['file']['size'];
    }

}

class FileUploader {

    private $allowedExtensions = array();
    private $sizeLimit = 10485760;
    private $file;

    function __construct(array $allowedExtensions = array(), $sizeLimit = 10485760) {
        $allowedExtensions = array_map("strtolower", $allowedExtensions);

        $this->allowedExtensions = $allowedExtensions;
        $this->sizeLimit = $sizeLimit;

        //$this->checkServerSettings(); // I commented this because it wasn't working (...don't ask me why)

        if (isset($_GET['file'])) {
            $this->file = new UploadedFileXhr();
        } elseif (isset($_FILES['file'])) {
            $this->file = new UploadedFileForm();
        } else {
            $this->file = false;
        }
    }

    public function getName() {
        if ($this->file)
            return $this->file->getName();
    }

    /* private function checkServerSettings(){        
      $postSize = $this->toBytes(ini_get('post_max_size'));
      $uploadSize = $this->toBytes(ini_get('upload_max_filesize'));

      if ($postSize < $this->sizeLimit || $uploadSize < $this->sizeLimit){
      $size = max(1, $this->sizeLimit / 1024 / 1024) . 'M';
      die("{'error':'increase post_max_size and upload_max_filesize to $size'}");
      }
      } */

    private function toBytes($str) {
        $val = trim($str);
        $last = strtolower($str[strlen($str) - 1]);
        switch ($last) {
            case 'g': $val *= 1024;
            case 'm': $val *= 1024;
            case 'k': $val *= 1024;
        }
        return $val;
    }

    /**
     * Returns array('success'=>true) or array('error'=>'error message')
     */
    function handleUpload() {
        if (!$this->file) {
            //return array('error' => 'No files were uploaded.');
            throw new InternalException('No files were uploaded.');
        }

        $size = $this->file->getSize();

        if ($size == 0) {
            //return array('error' => 'File is empty');
            throw new ForbiddenException('Could not upload this file. Maybe you are exceeding the maximium file size of ' . ($this->sizeLimit / 1024 / 1024) . 'MB?');
        }

        if ($size > $this->sizeLimit) {
            /* return array('error' => 'File is too large'); */
            throw new ForbiddenException('This file exceeds the maximium file size of ' . ($this->sizeLimit / 1024 / 1024) . 'MB');
        }

        $pathinfo = pathinfo($this->file->getName());
        $filename = $pathinfo['filename'];
        //$filename = md5(uniqid());
        $ext = @$pathinfo['extension'];  // hide notices if extension is empty

        if ($this->allowedExtensions && !in_array(strtolower($ext), $this->allowedExtensions)) {
            $these = implode(', ', $this->allowedExtensions);
            //return array('error' => 'File has an invalid extension, it should be one of '. $these . '.');
            throw new ForbiddenException('This file has an invalid extension. It should be one of these types: ' . $these . '.');
        }

        $file = $this->createFile($ext);
        return array("success" => true, "file" => $file);

        /* if ($this->file->save($uploadDirectory . $filename . $ext)){
          return array('success'=>true);
          } else {
          return array('error'=> 'Could not save uploaded file.' .
          'The upload was cancelled, or server error encountered');
          } */
    }

    private function createFile($extension) {
        $file_content = $this->file->getFileContent();
        if($extension == 'csv' || $extension == 'xls') $file_content = str_replace(',', ' ', $file_content);

        $lines = preg_split("/(\r\n|\n|\r)/", $file_content);
        $head = $lines[0];
        
        //$words = array(); // empty
        //if($extension == 'txt')
            $words = preg_split('/\s+/', $head, -1, PREG_SPLIT_NO_EMPTY);
        //else if($extension == 'csv')
            //$words = explode(',', $head);

        //NEW TEST (CSV FILES)
        /*for ($k = 0; $k < count($words); $k++) {
            $lastCharacter = substr($words[$k], -1);
            if ($lastCharacter == ",")
                $words[$k] = substr($words[$k], 0, -1); // Remove comma at the end
        }*/

        $type = $words[0];
        if ($type == "3P4W" || $type == "1P3W" || $type == "3P3W")
            $default = "I";
        else if ($type == "HARMO")
            $default = "H(1-31)";
        foreach ($words as $w) {
            $parts = explode("=", $w);
            if ($parts[0] == "INPUT") {
                if ($type == "HARMO")
                    $scope = $parts[1];
                else if ($type == "3P4W" || $type == "1P3W" || $type == "3P3W")
                    $scope = "ALL";
            }
        }
        $label = "$type($scope)";

        $file = array(
            'name' => $this->getName(),
            'type' => $type,
            'scope' => $scope,
            'label' => $label,
            'default' => $default,
            'byte_size' => $this->file->getSize(),
            'content' => $file_content);

        return $file;
    }

}

?>
