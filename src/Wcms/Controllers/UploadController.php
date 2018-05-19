<?php

namespace App\Wcms\Controllers;

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use Slim\Http\UploadedFile;

class UploadController {

    protected $public_path = __DIR__.'/../../../public';
    protected $directory = 'uploads';

    public function store(Request $request, Response $response, $args) {
        $uploaded_files = $request->getUploadedFiles();

        if(!isset($uploaded_files['file']))
            return $response->withStatus(400);

        $uploaded_file = $uploaded_files['file'];

        switch ($uploaded_file->getError()) {
            case UPLOAD_ERR_OK:
                $filename = $this->moveUploadedFile($uploaded_file);
                $url = sprintf('%s/%s/%s', getenv('BASE_PATH'), $this->directory, $filename);
                return $response->withJson(['url' => $url]);
                break;

            case UPLOAD_ERR_INI_SIZE:
            case UPLOAD_ERR_FORM_SIZE:
                return $response->withJson([
                    'message' => 'File is too big.'
                ], 400);
                break;

            case UPLOAD_ERR_PARTIAL:
                return $response->withJson([
                    'message' => 'The uploaded file was only partially uploaded.'
                ], 400);
                break;

            case UPLOAD_ERR_NO_FILE:
                return $response->withJson([
                    'message' => 'No file was uploaded.'
                ], 400);
                break;

            case UPLOAD_ERR_NO_TMP_DIR:
                return $response->withJson([
                    'message' => 'Missing a temporary folder.'
                ], 400);
                break;

            case UPLOAD_ERR_CANT_WRITE:
                return $response->withJson([
                    'message' => 'Failed to write file to disk.'
                ], 400);
                break;

            default:
                return $response->withJson([
                    'message' => 'Unable to upload.'
                ], 400);
                break;
        }
    }

    protected function moveUploadedFile(UploadedFile $file) {
        $extension = pathinfo($file->getClientFilename(), PATHINFO_EXTENSION);
        $basename = bin2hex(random_bytes(8));
        $filename = sprintf('%s.%0.8s', $basename, $extension);

        $file->moveTo(sprintf('%s/%s/%s', $this->public_path, $this->directory, $filename));

        return $filename;
    }

}
