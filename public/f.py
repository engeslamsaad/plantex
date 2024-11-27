import os
import sys
import tflite_runtime.interpreter as tflite
from PIL import Image
import numpy as np

# Function to suppress all output (stdout and stderr) at the OS level
class SuppressOutput:
    def __enter__(self):
        self._stdout = os.dup(1)
        self._stderr = os.dup(2)
        devnull = os.open(os.devnull, os.O_WRONLY)
        os.dup2(devnull, 1)
        os.dup2(devnull, 2)
        os.close(devnull)

    def __exit__(self, exc_type, exc_value, traceback):
        os.dup2(self._stdout, 1)
        os.dup2(self._stderr, 2)
        os.close(self._stdout)
        os.close(self._stderr)

def load_labels(filename):
    with open(filename, 'r') as f:
        return [line.strip() for line in f.readlines()]

def run_inference(model_path, labels_path, image_path):
    with SuppressOutput():
        interpreter = tflite.Interpreter(model_path=model_path)
        interpreter.allocate_tensors()

    labels = load_labels(labels_path)
    input_details = interpreter.get_input_details()
    output_details = interpreter.get_output_details()

    img = Image.open(image_path).convert('RGB')
    target_size = input_details[0]['shape'][1:3]
    img = img.resize((target_size[1], target_size[0]))
    input_data = np.expand_dims(np.array(img, dtype=np.float32) / 255.0, axis=0)

    with SuppressOutput():
        interpreter.set_tensor(input_details[0]['index'], input_data)
        interpreter.invoke()

    output_data = interpreter.get_tensor(output_details[0]['index'])
    result = np.argmax(output_data)
    print (result)
    return labels[result]

if __name__ == '__main__':
    try:
        model_path = sys.argv[1]
        labels_path = sys.argv[2]
        image_path = sys.argv[3]
        result = run_inference(model_path, labels_path, image_path)
        print(result)
    except Exception as e:
        print(f"Error: {e}", file=sys.stderr)
