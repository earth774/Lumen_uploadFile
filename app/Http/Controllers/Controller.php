<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Laravel\Lumen\Routing\Controller as BaseController;

class Controller extends BaseController
{
    public function uploadImage(Request $request)
    {
        $response = null;
        $user = (object) ['image' => ""];

        if ($request->hasFile('image')) {
            $original_filename = $request->file('image')->getClientOriginalName();
            $original_filename_arr = explode('.', $original_filename);
            $file_ext = end($original_filename_arr);
            $destination_path = './upload/user/';
            $image = 'U-' . time() . '.' . $file_ext;

            if ($request->file('image')->move($destination_path, $image)) {
                $user->image = '/upload/user/' . $image;
                return $this->responseRequestSuccess($user);
            } else {
                return $this->responseRequestError('Cannot upload file');
            }
        } else {
            return $this->responseRequestError('File not found');
        }
    }

    public function saveImage(Request $request)
    {
        if (!empty($request->base64_image)) {
            $img_uniqid = uniqid('', true);
            $image = $request->base64_image;
            $destinationPath = "../public/upload/" . $request->subPath;
            if (!file_exists($destinationPath)) {
                mkdir($destinationPath, 0777, true);
            }
            $imgData = substr($image, 1 + strrpos($image, ","));
            if (file_put_contents("./upload/" . $request->subPath . "/" . $request->subPath . "-" . $img_uniqid . ".png", base64_decode($imgData)) != null) {
                return $this->responseRequestSuccess("/upload/" . $request->subPath . "/" . $request->subPath . "-" . $img_uniqid . ".png");
            } else {
                return $this->responseRequestError('Cannot upload file');
            }
        } else {
            return $this->responseRequestError('File not found');
        }
    }

    public function saveFile(Request $request)
    {
        if (!empty($request->base64_fileSrc)) {
            $name_uniqid = uniqid('', true);
            $file = $request->base64_fileSrc;
            $destinationPath = "../public/upload/" . $request->subPath;
            if (!file_exists($destinationPath)) {
                mkdir($destinationPath, 0777, true);
            }
            $fileData = substr($file, 1 + strrpos($file, ","));
            if (file_put_contents("./upload/" . $request->subPath . "/" . $request->subPath . "-" . $name_uniqid . '.' . $request->fileExtension, base64_decode($fileData)) != null) {
                return $this->responseRequestSuccess("/upload/" . $request->subPath . "/" . $request->subPath . "-" . $name_uniqid . '.' . $request->fileExtension);
            } else {
                return $this->responseRequestError('Cannot upload file');
            }
        } else {
            return $this->responseRequestError('File not found');
        }
    }

    protected function responseRequestSuccess($ret)
    {
        return response()->json(['status' => 'success', 'data' => $ret], 200)
            ->header('Access-Control-Allow-Origin', '*')
            ->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
    }

    protected function responseRequestError($message = 'Bad request', $statusCode = 200)
    {
        return response()->json(['status' => 'error', 'error' => $message], $statusCode)
            ->header('Access-Control-Allow-Origin', '*')
            ->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
    }
}
