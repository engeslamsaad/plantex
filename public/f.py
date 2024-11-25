import os
import logging
import sys
import tflite_runtime.interpreter as tflite
from PIL import Image
import numpy as np
from contextlib import redirect_stdout, redirect_stderr
from io import StringIO

# Suppress TensorFlow Lite logs via environment variables
os.environ['TF_CPP_MIN_LOG_LEVEL'] = '3'

# Suppress Python logging globally
logging.disable(logging.CRITICAL)

def load_labels(filename):
    with open(filename, 'r') as f:
        return [line.strip() for line in f.readlines()]

def run_inference(model_path, labels_path, image_path):
    # Capture and suppress unwanted logs during model initialization
    with StringIO() as buf, redirect_stdout(buf), redirect_stderr(buf):
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

    # Capture and suppress unwanted logs during inference
    with StringIO() as buf, redirect_stdout(buf), redirect_stderr(buf):
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

        # Capture all output
        with StringIO() as buf, redirect_stdout(buf), redirect_stderr(buf):
            result = run_inference(model_path, labels_path, image_path)
            output = buf.getvalue()  # Capture all logs

        # Filter out unwanted lines (e.g., INFO messages)
        filtered_output = [
            line for line in output.splitlines() if not line.startswith("INFO:")
        ]

        # Print only the relevant result
        # print(filtered_output)
        # print("///////////////////")
        # print(output)
        # print("///////////////////")
        # print(result)
    except Exception as e:
        print(f"Error: {e}", file=sys.stderr)
