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
        $command = escapeshellcmd("python3 f.py $modelPath $labelsPath $imageFullPath");
        $output = shell_exec($command);
        echo "Output: <pre>$output</pre>";


        
        // if ($output === null) {
        //     echo "Error: Failed to execute Python script.";
        // } else {
        //     echo "Command executed: $command<br>";
        //     echo "Output: <pre>$output</pre>";
        // }

        // Return the result
        return response()->json([
            'label' => trim($output),
        ]);
    }
}
