import mediapipe as mp
import numpy as np
from mediapipe.tasks import python
from mediapipe.tasks.python import vision
import inspect
import os

class GestureDetector:
    def __init__(self, min_detection_confidence=0.3, min_tracking_confidence=0.3):
        model_path = self._get_model_path()
        options = vision.HandLandmarkerOptions(
            base_options=python.BaseOptions(model_asset_path=model_path),
            running_mode=vision.RunningMode.IMAGE,
            num_hands=1,
            min_hand_detection_confidence=min_detection_confidence,
            min_tracking_confidence=min_tracking_confidence,
        )
        self.landmarker = vision.HandLandmarker.create_from_options(options)

    def _get_model_path(self):
        module_path = inspect.getfile(mp)
        return os.path.join(
            os.path.dirname(module_path),
            "modules", "hand_landmarker", "hand_landmarker.task"
        )

    def is_peace_sign(self, landmarks):
        index_tip = landmarks[8]
        middle_tip = landmarks[12]
        ring_tip = landmarks[16]
        pinky_tip = landmarks[20]
        index_pip = landmarks[6]
        middle_pip = landmarks[10]
        ring_pip = landmarks[14]
        pinky_pip = landmarks[18]

        index_up = index_tip.y < index_pip.y
        middle_up = middle_tip.y < middle_pip.y
        ring_down = ring_tip.y > ring_pip.y
        pinky_down = pinky_tip.y > pinky_pip.y

        return index_up and middle_up and ring_down and pinky_down

    def detect(self, frame_rgb):
        mp_image = mp.Image(image_format=mp.ImageFormat.SRGB, data=np.ascontiguousarray(frame_rgb))
        result = self.landmarker.detect(mp_image)
        if result.hand_landmarks:
            return True, self.is_peace_sign(result.hand_landmarks[0])
        return False, False

    def close(self):
        self.landmarker.close()
