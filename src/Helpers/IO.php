<?php
namespace App\Helpers;

class IO {

    /**
     * Create folder
     * @param  string $folder [description]
     * @return [type]         [description]
     */
    public static function createFolder($path = '/') {
        try {
            // Check if directory already exists
            if (is_dir($path) || empty($path)) {
                return true;
            } else {

                $dir = pathinfo($path, PATHINFO_DIRNAME);
                // s($path, $dir);
                if (self::createFolder($dir)) {

                    $chmod = octdec('0777');
                    // sd($path);
                    mkdir($path, $chmod);

                    if (strtoupper(substr(PHP_OS, 0, 3)) != 'WIN') {
                        $umask = umask(0);
                        chmod($dir, $chmod);
                        umask($umask);
                    }
                    return true;
                }
            }

        } catch(\Exception $e) {
            // s($e);
        }
        return false;
    }

    public static function createUniqueFilename($dest_folder) {
        $dest_filename = sprintf('%d_%d', time(), uniqid(rand()));

        if (!is_dir($dest_folder)) {
            return $dest_filename;
        }

        while ((glob(sprintf('%s/%s%s', $dest_folder, $dest_filename, '.*')))) {
            $dest_filename = sprintf('%d_%d', time(), uniqid(rand()));
            // s($dest_filename);
        }
        // sd($dest_filename);

        return $dest_filename;
    }

    # Get Files Recursive
    public static function getFileInFolder($base_dir = '/', $recursive = false, $inc_filesize = false, $sub_dir = '') {
        $files = [];
        $curr_dir = (!empty($sub_dir) ? $base_dir . '/' . $sub_dir : $base_dir);

        // Open a known directory, and proceed to read its contents
        if (is_dir($curr_dir)) {
            if ($handle = opendir($curr_dir)) {
                while (false !== ($filename = readdir($handle))) {
                    if ('.' != $filename && '..' != $filename) {
                        if (is_file($curr_dir . '/' . $filename)) {
                            if (!empty($sub_dir)) {
                                $filename = $sub_dir . '/' . $filename;
                            }

                            $files[] = ((bool) $inc_filesize ? [$filename, filesize($curr_dir . '/' . $filename)] : $filename);
                        } else if ((bool) $recursive && is_dir($base_dir . '/' . $filename)) {
                            $subdirs = self::getFileInFolder($base_dir, $recursive, $inc_filesize, $filename);
                            $files = array_merge($files, $subdirs);
                        }
                    }
                }
                closedir($handle);
            }
        }
        sort($files);

        return $files;
    }

    # Get Files Recursive
    public static function getFileExtInFolder($base_dir = '/', $extensions = [], $recursive = false, $inc_filesize = false, $sub_dir = '') {
        $files = [];
        $curr_dir = (!empty($sub_dir) ? $base_dir . '/' . $sub_dir : $base_dir);

        if (!is_array($extensions)) {
            $extensions = [$extensions];
        }

        // Open a known directory, and proceed to read its contents
        if (is_dir($curr_dir) && !empty($extensions)) {
            if ($handle = opendir($curr_dir)) {
                while (false !== ($filename = readdir($handle))) {
                    if ('.' != $filename && '..' != $filename) {
                        if (is_file($curr_dir . '/' . $filename)) {
                            $info = pathinfo($filename);

                            if (in_array($info['extension'], $extensions)) {
                                if (!empty($sub_dir)) {
                                    $filename = $sub_dir . '/' . $filename;
                                }

                                $files[] = $filename;
                            }
                        } else if ((bool) $recursive && is_dir($base_dir . '/' . $filename)) {
                            $subdirs = self::getFileInFolder($base_dir, $extension, $recursive, $inc_filesize, $filename);
                            $files = array_merge($files, $subdirs);
                        }
                    }
                }
                closedir($handle);
            }
        }
        sort($files);

        return $files;
    }

    public static function getImages($base_dir = '/', $extensions = ['jpg', 'png', 'bmp']) {
        try {

            $files = [];
            $curr_dir = (!empty($sub_dir) ? $base_dir . '/' . $sub_dir : $base_dir);

            if (!is_array($extensions)) {
                $extensions = [$extensions];
            }
            if (empty($extensions)) $extensions = [''];

            // Open a known directory, and proceed to read its contents
            if (is_dir($curr_dir)) {
                if ($handle = opendir($curr_dir)) {
                    while (false !== ($filename = readdir($handle))) {
                        if ('.' === $filename || '..' === $filename
                            || is_dir($filename)) {
                            continue;
                        }

                        // s(is_file($filename), $filename);
                        array_push($files, $filename);
                    }
                    closedir($handle);
                }
            }
            if (!empty($files)) {
                sort($files);
                $images = [];

                foreach($files as $filename) {
                    try {
                        $info = pathinfo($filename);
                        if (!in_array($info['extension'], $extensions)) {
                            continue;
                        }
                        list($width, $height, $type, $attr) = getimagesize( $base_dir . '/' . $filename );
                        // s($info, $width, $height, $type, $attr);

                        $image = [
                                'name' => $filename,
                                'filename' => $info['filename'],
                                'extension' => $info['extension'],
                                'width' => $width,
                                'height' => $height,
                                'size' => filesize( $base_dir . '/' . $filename ),
                                'ratio' => AspectRatio::create($width, $height),
                            ];
                        array_push($images, $image);
                    } catch(\Exception $e) {
                        // sd($e);
                    }
                }

                $files = $images;
            }

        } catch(\Exception $e) {
            // sd($e);
        }
        // sd($files);
        return $files;
    }

    public static function ratio( $x, $y ) {
        $gcd = gmp_strval(gmp_gcd($x, $y));
        return ($x/$gcd).':'.($y/$gcd);
    }

    # Delete Folder Recursive
    public static function deleteFolder($dir = '/', $deleteFile = true) {

        if (!is_writable($dir)) {
            if (!@chmod($dir, 0777)) {
                return false;
            }
        }

        if (is_dir($dir)) {
            $d = dir($dir);
            while (false !== ($entry = $d->read())) {
                if ('.' == $entry || '..' == $entry) {
                    continue;
                }

                $entry = $dir . '/' . $entry;
                if (is_dir($entry)) {
                    if (!(bool) $deleteFile) {
                        self::deleteFileInFolder($entry);
                    }

                    if (!self::deleteFolder($entry, $deleteFile)) {
                        return false;
                    }
                    continue;
                }
                if (!@unlink($entry)) {
                    $d->close();
                    return false;
                }
            }

            $d->close();

            //Remove Folder
            rmdir($dir);
        }
        return true;
    }

    // # Check physical file
    public static function isFileExists($base_dir = '', $filename = '') {
        if (!empty($filename) && !in_array($base_dir, ['.', '..']) && file_exists($base_dir . '/' . $filename) && is_file($base_dir . '/' . $filename)) {
            return true;
        } else if (empty($filename) && is_file($base_dir) && file_exists($base_dir)) {
            return true;
        } else {
            return false;
        }
    }

    // # Delete physical file
    public static function deleteFile($base_dir = '', $filename = '') {
        if (self::isFileExists($base_dir, $filename)) {
            $file_location = $base_dir . (!empty($filename) ? '/' . $filename : '');
            if (is_file($file_location)) {
                unlink($file_location);
            }
        }
    }

    // # Delete physical file
    public static function deleteFileInFolder($base_dir = '') {
        if (is_dir($base_dir)) {
            if ($handle = opendir($base_dir)) {
                while (false !== ($filename = readdir($handle))) {
                    if ('.' != $filename && '..' != $filename) {
                        if (is_file($base_dir . '/' . $filename)) {
                            unlink($base_dir . '/' . $filename);
                        }
                    }
                }
                closedir($handle);
            }
        }
    }

    // # Move file
    public static function moveFile($src_filename = '', $dest_folder = '', $overwrite = false) {
        if (file_exists($src_filename) && is_file($src_filename)) {
            $info = pathinfo($src_filename);
            $dest_filename = ((bool) $overwrite ? $info['basename'] : self::makeUniqueFilename($dest_folder, $info['basename']));
            $dest_file = $dest_folder . '/' . $dest_filename;

            self::createFolder($dest_folder);
            if (!rename($src_filename, $dest_file)) {
                if (copy($src_filename, $dest_file)) {
                    unlink($src_filename);
                    return $dest_filename;
                } else {
                    return false;
                }
            } else {
                return $dest_filename;
            }
        }
    }

    // # Copy file
    public static function copyFile($src_filename = '', $dest_folder = '', $overwrite = false) {
        if (file_exists($src_filename) && is_file($src_filename)) {
            $info = pathinfo($src_filename);
            $dest_filename = ((bool) $overwrite ? $info['basename'] : self::makeUniqueFilename($dest_folder, $info['basename']));
            $dest_file = $dest_folder . '/' . $dest_filename;

            self::createFolder($dest_folder);
            if (copy($src_filename, $dest_file)) {
                return $dest_filename;
            } else {
                return false;
            }
        }
    }

    // # Copy file
    public static function copyDirectory($src_folder, $dst_folder)
    {
     if (is_dir($src_folder)) {
         // Clear folder
         if (is_dir($dst_folder)) {
             self::deleteFolder($dst_folder, true);
         }

         // Create destination folder
         self::createFolder($dst_folder);

         if ($handle = opendir($src_folder)) {
             while (false !== ($filename = readdir($handle))) {
                 if ('.' != $filename && '..' != $filename) {
                     if (is_dir($src_folder . '/' . $filename)) {
                         self::copyDirectory($src_folder . '/' . $filename, $dst_folder . '/' . $filename);
                     } else {
                         copy($src_folder . '/' . $filename, $dst_folder . '/' . $filename);
                     }
                 }
             }
             closedir($handle);
         }
     }
    }

    public static function makeUniqueFilename($dest_folder = '', $filename = '') {
        $dest_folder = $dest_folder . '/';
        $new_filename = $filename;
        $i = 1;
        while (file_exists($dest_folder . $new_filename)) {
            $new_filename = ereg_replace('(.*)(\.[a-zA-Z0-9]+)$', '\1(' . $i . ')\2', $filename);
            $i++;
        }
        return $new_filename;
    }

    # Get File extension
    public static function getFileExtension($filename) {
        $info = pathinfo($filename);

        return $info['extension'];
    }

    # Get Filesize
    public static function getFilesize($filename) {
        /* calculate file size */
        $size = filesize($filename);
        if ($size < 1000) {
            $size = (int) $size . ' Byte';
        } else if ($size < 1000000) {
            $size = floor((int) $size / 1000) . ' KB';
        } else {
            $size = floor((int) $size / 1000000) . ' MB';
        }
        return $size;
    }

    public static function returnFilesize($val, $sep = ' ') {
        /* calculate file size */
        if (!is_numeric($val)) {
            $size = filesize($val); // Get from file
        } else {
            $size = $val;
        }
        $unit = null;
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];

        foreach ($units as $item) {
            if ($size >= 1024) {
                $size = $size / 1024;
            } else {
                $unit = $item;
                break;
            }
        }

        return round($size, 2) . $sep . $unit;
    }

    // Convert unit to bytes
    public static function returnBytes($val) {
        $val = trim($val);
        $last = strtolower($val{strlen($val) - 1});
        switch ($last) {
            // The 'G' modifier is available since PHP 5.1.0
            case 'g':
                $val = (int) $val * 1024;
            case 'm':
                $val = (int) $val * 1024;
            case 'k':
                $val = (int) $val * 1024;
        }

        return (int) $val;
    }

    /**
     * Get filename
     * @param  string  $filename [description]
     * @param  boolean $ext      [description]
     * @return [type]            [description]
     */
    public static function getFilename($filename = '', $ext = true) {
        $info = pathinfo($filename);
        if (!(bool) $ext) {
            return $info['filename'];
        } else {
            return $info['basename'];
        }
    }

    /**
     * Get mine type of file
     * @param  [type]  $filename [description]
     * @param  boolean $debug    [description]
     * @return [type]            [description]
     */
    public static function getFileMimeType($filename, $debug = false) {
        # https://chrisjean.com/generating-mime-type-in-php-is-not-magic/

        if (function_exists('finfo_open') && function_exists('finfo_file') && function_exists('finfo_close')) {
            $fileinfo = finfo_open(FILEINFO_MIME_TYPE);
            $mime_type = finfo_file($fileinfo, $filename);
            finfo_close($fileinfo);

            if (!empty($mime_type)) {
                if (true === $debug) {
                    return ['mime_type' => $mime_type, 'method' => 'fileinfo'];
                }

                return $mime_type;
            }
        }

        if (function_exists('mime_content_type')) {
            $mime_type = mime_content_type($filename);

            if (!empty($mime_type)) {
                if (true === $debug) {
                    return ['mime_type' => $mime_type, 'method' => 'mime_content_type'];
                }

                return $mime_type;
            }
        }

        $mime_types = [
            'ai' => 'application/postscript',
            'aif' => 'audio/x-aiff',
            'aifc' => 'audio/x-aiff',
            'aiff' => 'audio/x-aiff',
            'asc' => 'text/plain',
            'asf' => 'video/x-ms-asf',
            'asx' => 'video/x-ms-asf',
            'au' => 'audio/basic',
            'avi' => 'video/x-msvideo',
            'bcpio' => 'application/x-bcpio',
            'bin' => 'application/octet-stream',
            'bmp' => 'image/bmp',
            'bz2' => 'application/x-bzip2',
            'cdf' => 'application/x-netcdf',
            'chrt' => 'application/x-kchart',
            'class' => 'application/octet-stream',
            'cpio' => 'application/x-cpio',
            'cpt' => 'application/mac-compactpro',
            'csh' => 'application/x-csh',
            'css' => 'text/css',
            'dcr' => 'application/x-director',
            'dir' => 'application/x-director',
            'djv' => 'image/vnd.djvu',
            'djvu' => 'image/vnd.djvu',
            'dll' => 'application/octet-stream',
            'dms' => 'application/octet-stream',
            'doc' => 'application/msword',
            'dvi' => 'application/x-dvi',
            'dxr' => 'application/x-director',
            'eps' => 'application/postscript',
            'etx' => 'text/x-setext',
            'exe' => 'application/octet-stream',
            'ez' => 'application/andrew-inset',
            'flv' => 'video/x-flv',
            'gif' => 'image/gif',
            'gtar' => 'application/x-gtar',
            'gz' => 'application/x-gzip',
            'hdf' => 'application/x-hdf',
            'hqx' => 'application/mac-binhex40',
            'htm' => 'text/html',
            'html' => 'text/html',
            'ice' => 'x-conference/x-cooltalk',
            'ief' => 'image/ief',
            'iges' => 'model/iges',
            'igs' => 'model/iges',
            'img' => 'application/octet-stream',
            'iso' => 'application/octet-stream',
            'jad' => 'text/vnd.sun.j2me.app-descriptor',
            'jar' => 'application/x-java-archive',
            'jnlp' => 'application/x-java-jnlp-file',
            'jpe' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'jpg' => 'image/jpeg',
            'js' => 'application/x-javascript',
            'kar' => 'audio/midi',
            'kil' => 'application/x-killustrator',
            'kpr' => 'application/x-kpresenter',
            'kpt' => 'application/x-kpresenter',
            'ksp' => 'application/x-kspread',
            'kwd' => 'application/x-kword',
            'kwt' => 'application/x-kword',
            'latex' => 'application/x-latex',
            'lha' => 'application/octet-stream',
            'lzh' => 'application/octet-stream',
            'm3u' => 'audio/x-mpegurl',
            'man' => 'application/x-troff-man',
            'me' => 'application/x-troff-me',
            'mesh' => 'model/mesh',
            'mid' => 'audio/midi',
            'midi' => 'audio/midi',
            'mif' => 'application/vnd.mif',
            'mov' => 'video/quicktime',
            'movie' => 'video/x-sgi-movie',
            'mp2' => 'audio/mpeg',
            'mp3' => 'audio/mpeg',
            'mpe' => 'video/mpeg',
            'mpeg' => 'video/mpeg',
            'mpg' => 'video/mpeg',
            'mpga' => 'audio/mpeg',
            'ms' => 'application/x-troff-ms',
            'msh' => 'model/mesh',
            'mxu' => 'video/vnd.mpegurl',
            'nc' => 'application/x-netcdf',
            'odb' => 'application/vnd.oasis.opendocument.database',
            'odc' => 'application/vnd.oasis.opendocument.chart',
            'odf' => 'application/vnd.oasis.opendocument.formula',
            'odg' => 'application/vnd.oasis.opendocument.graphics',
            'odi' => 'application/vnd.oasis.opendocument.image',
            'odm' => 'application/vnd.oasis.opendocument.text-master',
            'odp' => 'application/vnd.oasis.opendocument.presentation',
            'ods' => 'application/vnd.oasis.opendocument.spreadsheet',
            'odt' => 'application/vnd.oasis.opendocument.text',
            'ogg' => 'application/ogg',
            'otg' => 'application/vnd.oasis.opendocument.graphics-template',
            'oth' => 'application/vnd.oasis.opendocument.text-web',
            'otp' => 'application/vnd.oasis.opendocument.presentation-template',
            'ots' => 'application/vnd.oasis.opendocument.spreadsheet-template',
            'ott' => 'application/vnd.oasis.opendocument.text-template',
            'pbm' => 'image/x-portable-bitmap',
            'pdb' => 'chemical/x-pdb',
            'pdf' => 'application/pdf',
            'pgm' => 'image/x-portable-graymap',
            'pgn' => 'application/x-chess-pgn',
            'png' => 'image/png',
            'pnm' => 'image/x-portable-anymap',
            'ppm' => 'image/x-portable-pixmap',
            'ppt' => 'application/vnd.ms-powerpoint',
            'ps' => 'application/postscript',
            'qt' => 'video/quicktime',
            'ra' => 'audio/x-realaudio',
            'ram' => 'audio/x-pn-realaudio',
            'ras' => 'image/x-cmu-raster',
            'rgb' => 'image/x-rgb',
            'rm' => 'audio/x-pn-realaudio',
            'roff' => 'application/x-troff',
            'rpm' => 'application/x-rpm',
            'rtf' => 'text/rtf',
            'rtx' => 'text/richtext',
            'sgm' => 'text/sgml',
            'sgml' => 'text/sgml',
            'sh' => 'application/x-sh',
            'shar' => 'application/x-shar',
            'silo' => 'model/mesh',
            'sis' => 'application/vnd.symbian.install',
            'sit' => 'application/x-stuffit',
            'skd' => 'application/x-koan',
            'skm' => 'application/x-koan',
            'skp' => 'application/x-koan',
            'skt' => 'application/x-koan',
            'smi' => 'application/smil',
            'smil' => 'application/smil',
            'snd' => 'audio/basic',
            'so' => 'application/octet-stream',
            'spl' => 'application/x-futuresplash',
            'src' => 'application/x-wais-source',
            'stc' => 'application/vnd.sun.xml.calc.template',
            'std' => 'application/vnd.sun.xml.draw.template',
            'sti' => 'application/vnd.sun.xml.impress.template',
            'stw' => 'application/vnd.sun.xml.writer.template',
            'sv4cpio' => 'application/x-sv4cpio',
            'sv4crc' => 'application/x-sv4crc',
            'swf' => 'application/x-shockwave-flash',
            'sxc' => 'application/vnd.sun.xml.calc',
            'sxd' => 'application/vnd.sun.xml.draw',
            'sxg' => 'application/vnd.sun.xml.writer.global',
            'sxi' => 'application/vnd.sun.xml.impress',
            'sxm' => 'application/vnd.sun.xml.math',
            'sxw' => 'application/vnd.sun.xml.writer',
            't' => 'application/x-troff',
            'tar' => 'application/x-tar',
            'tcl' => 'application/x-tcl',
            'tex' => 'application/x-tex',
            'texi' => 'application/x-texinfo',
            'texinfo' => 'application/x-texinfo',
            'tgz' => 'application/x-gzip',
            'tif' => 'image/tiff',
            'tiff' => 'image/tiff',
            'torrent' => 'application/x-bittorrent',
            'tr' => 'application/x-troff',
            'tsv' => 'text/tab-separated-values',
            'txt' => 'text/plain',
            'ustar' => 'application/x-ustar',
            'vcd' => 'application/x-cdlink',
            'vrml' => 'model/vrml',
            'wav' => 'audio/x-wav',
            'wax' => 'audio/x-ms-wax',
            'wbmp' => 'image/vnd.wap.wbmp',
            'wbxml' => 'application/vnd.wap.wbxml',
            'wm' => 'video/x-ms-wm',
            'wma' => 'audio/x-ms-wma',
            'wml' => 'text/vnd.wap.wml',
            'wmlc' => 'application/vnd.wap.wmlc',
            'wmls' => 'text/vnd.wap.wmlscript',
            'wmlsc' => 'application/vnd.wap.wmlscriptc',
            'wmv' => 'video/x-ms-wmv',
            'wmx' => 'video/x-ms-wmx',
            'wrl' => 'model/vrml',
            'wvx' => 'video/x-ms-wvx',
            'xbm' => 'image/x-xbitmap',
            'xht' => 'application/xhtml+xml',
            'xhtml' => 'application/xhtml+xml',
            'xls' => 'application/vnd.ms-excel',
            'xml' => 'text/xml',
            'xpm' => 'image/x-xpixmap',
            'xsl' => 'text/xml',
            'xwd' => 'image/x-xwindowdump',
            'xyz' => 'chemical/x-xyz',
            'zip' => 'application/zip',
        ];

        $ext = strtolower(array_pop(explode('.', $filename)));

        if (!empty($mime_types[$ext])) {
            if (true === $debug) {
                return ['mime_type' => $mime_types[$ext], 'method' => 'from_array'];
            }

            return $mime_types[$ext];
        }

        if (true === $debug) {
            return ['mime_type' => 'application/octet-stream', 'method' => 'last_resort'];
        }

        return 'application/octet-stream';
    }
}
