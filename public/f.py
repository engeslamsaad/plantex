import os
import logging
import sys
import tflite_runtime.interpreter as tflite
from PIL import Image
import numpy as np

# Advanced log suppression
# Suppress TensorFlow Lite logs via environment variables
os.environ['TF_CPP_MIN_LOG_LEVEL'] = '3'

# Suppress unwanted TensorFlow or library logs by redirecting stderr
class SuppressStderr:
    def __enter__(self):
        self.stderr = sys.stderr
        sys.stderr = open(os.devnull, 'w')

    def __exit__(self, exc_type, exc_value, traceback):
        sys.stderr.close()
        sys.stderr = self.stderr

def load_labels(filename):
    with open(filename, 'r') as f:
        return [line.strip() for line in f.readlines()]

def run_inference(model_path, labels_path, image_path):
    # Use SuppressStderr to suppress logs during model loading
    with SuppressStderr():
        interpreter = tflite.Interpreter(model_path=model_path)
        interpreter.allocate_tensors()

    # Load the labels
    labels = load_labels(labels_path)

    # Load and preprocess the image
    input_details = interpreter.get_input_details()
    output_details = interpreter.get_output_details()
    img = Image.open(image_path).convert('RGB')
    target_size = input_details[0]['shape'][1:3]
    img = img.resize((target_size[1], target_size[0]))
    input_data = np.expand_dims(np.array(img, dtype=np.float32) / 255.0, axis=0)

    # Run inference
    with SuppressStderr():  # Suppress logs during inference
        interpreter.set_tensor(input_details[0]['index'], input_data)
        interpreter.invoke()

    # Get results
    output_data = interpreter.get_tensor(output_details[0]['index'])
    result = np.argmax(output_data)
    return labels[result]

if __name__ == '__main__':
    try:
        model_path = sys.argv[1]
        labels_path = sys.argv[2]
        image_path = sys.argv[3]
        result = run_inference(model_path, labels_path, image_path)

        # Explicitly print only the result
        print(result)
    except Exception as e:
        print(f"Error: {e}", file=sys.stderr)
