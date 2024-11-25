import sys
import tflite_runtime.interpreter as tflite
from PIL import Image
import numpy as np


def load_labels(filename):
    """Load labels from a text file."""
    with open(filename, 'r') as f:
        return [line.strip() for line in f.readlines()]


def run_inference(model_path, labels_path, image_path):
    """Run inference on an image using the TFLite model."""
    # Load the TFLite model
    interpreter = tflite.Interpreter(model_path=model_path)
    interpreter.allocate_tensors()

    # Load labels
    labels = load_labels(labels_path)

    # Get input and output tensor details
    input_details = interpreter.get_input_details()
    output_details = interpreter.get_output_details()

    # Preprocess the image
    img = Image.open(image_path).convert('RGB')
    input_shape = input_details[0]['shape'][1:3]  # Get the expected input shape
    img = img.resize(input_shape)  # Resize image
    input_data = np.expand_dims(np.array(img, dtype=np.float32) / 255.0, axis=0)

    # Ensure the input data matches the input tensor's dtype
    input_data = input_data.astype(input_details[0]['dtype'])

    # Run inference
    interpreter.set_tensor(input_details[0]['index'], input_data)
    interpreter.invoke()

    # Get and process the output
    output_data = interpreter.get_tensor(output_details[0]['index'])
    predicted_index = np.argmax(output_data)
    confidence = output_data[0][predicted_index]  # Get confidence score

    return labels[predicted_index], confidence


if __name__ == '__main__':
    if len(sys.argv) != 4:
        print("Usage: python script.py <model_path> <labels_path> <image_path>")
        sys.exit(1)

    model_path = sys.argv[1]
    labels_path = sys.argv[2]
    image_path = sys.argv[3]

    try:
        result, confidence = run_inference(model_path, labels_path, image_path)
        print(f"Predicted Label: {result}")
        print(f"Confidence: {confidence:.2f}")
    except Exception as e:
        print(f"Error: {e}")
