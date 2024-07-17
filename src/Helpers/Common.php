<?php
namespace Dvpz\Helpers;

use Dvpz\Options\CsgOptions;
use mikehaertl\shellcommand\Command;

class Common {
    /**
     * @return bool
     */
    private function isAcfActivated()
    {
        return function_exists('get_field');
    }

    /**
     * @return string
     */
    public function getRootFolderName()
    {
        return $this->getOptionField(CsgOptions::OPTKEY_ROOT_FOLDER) ?? '';
    }

    /**
     * @return string
     */
    public function getSubdirPrefix()
    {
        return $this->getOptionField(CsgOptions::OPTKEY_SUBDIR_PREFIX) ?? '';
    }

    /**
     * @return string
     */
    public function getUrlPlaceHolder()
    {
        return $this->getOptionField(CsgOptions::OPTKEY_URL_PLACEHOLDER) ?? '';
    }

    /**
     * @return bool|null
     */
    public function getOptionField($fieldKey)
    {
        if (!$this->isAcfActivated()) {
            return null;
        }

        return get_field($fieldKey, 'option') ?: null;
    }

    /**
     * @param $path
     *
     * @return bool
     */
    public function deleteDirectory($dirname) {
        if (!is_dir($dirname)) {
            return false;
        }

        $dirHandle = opendir($dirname);

        if ($dirHandle === false) {
            return false;
        }

        while($file = readdir($dirHandle)) {
          if ($file != "." && $file != "..") {
             if (!is_dir($dirname."/".$file))
                unlink($dirname."/".$file);
             else
                $this->deleteDirectory($dirname.'/'.$file);
          }
       }

       closedir($dirHandle);
       rmdir($dirname);

       return true;
    }
}

