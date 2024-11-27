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

def run_inference(model_path, labels_path, image_path, conf_threshold=0.5, nms_threshold=0.4):
    with SuppressOutput():
        interpreter = tflite.Interpreter(model_path=model_path)
        interpreter.allocate_tensors()

    labels = load_labels(labels_path)
    input_details = interpreter.get_input_details()
    output_details = interpreter.get_output_details()

    # Load and preprocess image
    img = Image.open(image_path).convert('RGB')
    target_size = input_details[0]['shape'][1:3]
    img_resized = img.resize((target_size[1], target_size[0]))
    input_data = np.expand_dims(np.array(img_resized, dtype=np.float32) / 255.0, axis=0)

    with SuppressOutput():
        interpreter.set_tensor(input_details[0]['index'], input_data)
        interpreter.invoke()

    # Get output tensor
    output_data = interpreter.get_tensor(output_details[0]['index'])[0]

    # Decode YOLOv5 output
    boxes = []
    confidences = []
    class_ids = []

    for i in range(output_data.shape[0]):
        # Parse each prediction
        x_center, y_center, width, height, object_confidence, *class_probs = output_data[i]
        
        # Filter by object confidence threshold
        if object_confidence < conf_threshold:
            continue

        # Find class with the highest probability
        class_id = np.argmax(class_probs)
        class_prob = class_probs[class_id]
        
        if class_prob < conf_threshold:
            continue

        # Store the prediction
        boxes.append([x_center, y_center, width, height])
        confidences.append(object_confidence * class_prob)
        class_ids.append(class_id)

    # Postprocessing: Apply Non-Maximum Suppression (NMS) if needed
    # (Typically, YOLOv5 would perform NMS internally, but you may need to handle it here for custom models)
    indices = non_max_suppression(np.array(boxes), np.array(confidences), threshold=nms_threshold)
    
    # Print the results
    for idx in indices:
        box = boxes[idx]
        label = labels[class_ids[idx]]
        confidence = confidences[idx]
        print(f"Detected {label} with confidence {confidence:.2f} at {box}")

def non_max_suppression(boxes, confidences, threshold):
    # Simple NMS implementation
    if len(boxes) == 0:
        return []
    
    boxes = np.array(boxes)
    confidences = np.array(confidences)
    
    # Compute the area of each box
    areas = (boxes[:, 2] * boxes[:, 3])
    
    # Sort by confidence scores (highest first)
    order = confidences.argsort()[::-1]
    
    keep = []
    
    while order.size > 0:
        i = order[0]
        keep.append(i)
        
        # Compute IoU (Intersection over Union) between the highest-confidence box and the rest
        xx1 = np.maximum(boxes[i, 0] - boxes[i, 2] / 2, boxes[order[1:], 0] - boxes[order[1:], 2] / 2)
        yy1 = np.maximum(boxes[i, 1] - boxes[i, 3] / 2, boxes[order[1:], 1] - boxes[order[1:], 3] / 2)
        xx2 = np.minimum(boxes[i, 0] + boxes[i, 2] / 2, boxes[order[1:], 0] + boxes[order[1:], 2] / 2)
        yy2 = np.minimum(boxes[i, 1] + boxes[i, 3] / 2, boxes[order[1:], 1] + boxes[order[1:], 3] / 2)
        
        # Compute intersection area
        w = np.maximum(0, xx2 - xx1)
        h = np.maximum(0, yy2 - yy1)
        intersection = w * h
        
        # Compute IoU
        iou = intersection / (areas[i] + areas[order[1:]] - intersection)
        
        # Keep boxes with IoU < threshold
        order = order[np.where(iou < threshold)[0] + 1]
    
    return keep

if __name__ == '__main__':
    try:
        model_path = sys.argv[1]
        labels_path = sys.argv[2]
        image_path = sys.argv[3]
        result = run_inference(model_path, labels_path, image_path)
        print(result)

    except Exception as e:
        print(f"Error: {e}", file=sys.stderr)
