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
        $imageFullPath = '/var/ww/plantex/public/1.png';

        // Run the Python script
        $command = escapeshellcmd("python3 f.py $modelPath $labelsPath $imageFullPath");
        $output = shell_exec($command);

        // Return the result
        return response()->json([
            'label' => trim($output),
        ]);
    }
}
