<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Validator;

class InferenceController extends Controller
{
    public function classify(Request $request)
    {
        $request->validate([
        ]);

        $validation = Validator::make($request->all(), [
            'image' => 'required|image',
         ]);
         if ($validation->fails()) {
            return response()->json($validation->errors(), 400, [], JSON_UNESCAPED_UNICODE);
         }
        // Save the uploaded image
        $imagePath = $request->file('image')->store('uploads', 'public');

        // Define paths for model, labels, and image
        $modelPath = public_path('mobilenetV1.tflite');
        $labelsPath = public_path('labels.txt');
        $imageFullPath = public_path("storage/$imagePath");
        
        $command = escapeshellcmd("python3 /var/www/plantex/public/f.py $modelPath $labelsPath $imageFullPath") . " 2>&1";
        $output = shell_exec($command);

        return response()->json([
            'output' => $output, // Check the raw output
            'label' => trim($output), // Return the processed output
        ]);
    }
    public function classify_yolov(Request $request)
    {
        $request->validate([
        ]);

        $validation = Validator::make($request->all(), [
            'image' => 'required|image',
         ]);
         if ($validation->fails()) {
            return response()->json($validation->errors(), 400, [], JSON_UNESCAPED_UNICODE);
         }
        // Save the uploaded image
        $imagePath = $request->file('image')->store('uploads', 'public');

        // Define paths for model, labels, and image
        $modelPath = public_path('yolov5_tomatoes.tflite');
        $labelsPath = public_path('labels_yolov5_tom.txt');
        $imageFullPath = public_path("storage/$imagePath");
        
        $command = escapeshellcmd("python3 /var/www/plantex/public/f.py $modelPath $labelsPath $imageFullPath") . " 2>&1";
        $output = shell_exec($command);

        return response()->json([
            'output' => $output, // Check the raw output
            'label' => trim($output), // Return the processed output
        ]);
    }
}
