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
        
        // $command = escapeshellcmd("python3 /var/www/plantex/public/f.py $modelPath $labelsPath $imageFullPath");
        // $output = exec($command);
        // return response()->json([
        //     'label' => trim($output),
        // ]);


        // **Changes to suppress output and extract "Grape__ww":**

        $descriptorSpec = [
            0 => ['pipe', 'r'],  // Read standard output of the Python script
            1 => ['pipe', 'w'],  // Not used, can be set to null
            2 => ['pipe', 'w'],  // Not used, can be used to capture standard error
        ];

        $process = proc_open('python3 /var/www/plantex/public/f.py ' . escapeshellarg($modelPath) . ' ' . escapeshellarg($labelsPath) . ' ' . escapeshellarg($imageFullPath), $descriptorSpec, $pipes);

        if (is_resource($process)) {
            // Read output from the pipe
            $output = stream_get_contents($pipes[0]);
            fclose($pipes[0]);

            // Close the process
            proc_close($process);

            // Extract "Grape__ww" from the output (assuming it's the first line)
            $label = trim(explode("\n", $output)[0]);

            return response()->json([
                'label' => $label,
            ]);
        } else {
            // Handle process opening error
            return response()->json(['error' => 'Failed to execute Python script'], 500);
        }
    }
}
