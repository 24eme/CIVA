<?php
class Document {

    public static function getLastByFilename($directory, $filenameUndated) {
        return self::getByDatedFilename($directory, $filenameUndated, date('Ymd'));
    }

    public static function getByDatedFilename($directory, $filenameUndated, $date, $withUndated = true) {
        if (!($dt = DateTime::createFromFormat('Ymd', $date))) throw new Exception ('Date argument must be to Ymd format');
        $result = null;
        if ($files = self::getFilesInDirectory($directory, true)) {
            foreach($files as $filename => $target) {
                $filenameSlugified = KeyInflector::slugify(trim($filename));
                if (strpos($filenameSlugified, KeyInflector::slugify(trim($filenameUndated))) !== false) {
                    $extract = self::extractPrefixedDate($filenameSlugified);
                    if ($extract && $date >= $extract) return $target;
                    if (!$extract && $withUndated && !$result) $result = $target;
                }
            }
        }
        return $result;
    }

    private static function extractPrefixedDate($str) {
        $str = str_replace(['-', '_'], '',$str);
        $prefixedDate = substr($str, 0, 8);
        return (ctype_digit((string) $prefixedDate))? $prefixedDate : null;
    }

    public static function getFilesInDirectory($directoryName, $descending = false) {
        $files = [];
        if ($directoryName[strlen($directoryName) - 1] != '/') $directoryName .= '/';
        if ($directoryFiles = @scandir($directoryName, $descending)) {
            foreach($directoryFiles as $directoryFile) {
                if (!is_file($directoryName.$directoryFile)) continue;
                $files[$directoryFile] = $directoryName.$directoryFile;
            }
        }
        return $files;
    }
}
