<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Validator;

class InferenceController extends Controller
{
    public function classify(Request $request)
    {
        
        try {


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
            $output = stream_get_contents($pipes[0]);
            fclose($pipes[0]);

            $return_value = proc_close($process);

            if ($return_value !== 0) {
                // Handle Python script error
                return response()->json(['error' => 'Python script failed with exit code: ' . $return_value], 500);
            }

            // Extract label
            $label = trim(explode("\n", $output)[0]);

            return response()->json(['label' => $label]);
        } else {
            // Handle process opening error
            return response()->json(['error' => 'Failed to execute Python script'], 500);
        }
                    //code...
    } catch (\Throwable $th) {
        dd($th);
    }
    }
}
