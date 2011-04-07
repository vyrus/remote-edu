<?php
    class Storage {
        private $directory;

        public function __construct ($directory) {
            $this->directory = $directory;
        }

        public function storeFile () {
            $args = func_get_args();
            $path = array_shift($args);
            if (count($args)) {
                $filename = array_shift($args);
            }
            else {
                do {
                    $filename = md5($path . rand (0, 0xFFFFFF));
                } while (file_exists($this->directory . '/' . $filename));
            }
            
            if (@move_uploaded_file($path, $this->directory . '/' . $filename) === FALSE) {
                //var_dump($this->directory . '/' . $filename);
                throw new Exception ('Невозможно сохранить файл на сервере');
            }

            return $filename;
        }

        public function removeFile ($filename) {
            if (@unlink ($this->directory . '/' . $filename) === FALSE) {
                throw new Exception ('Невозможно удалить файл с сервера');
            }
        }

        public function getFileContent ($filename) {
            return file_get_contents ($this->directory . '/' . $filename);
        }
    }
?>