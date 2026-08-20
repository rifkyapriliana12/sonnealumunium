import cv2
import numpy as np
import math
import random
import sys
from .config import (
    CAMERA_SOURCE, BLUR_KERNEL_SIZE,
    FRAME_WIDTH, FRAME_HEIGHT,
    MIN_DETECTION_CONFIDENCE, MIN_TRACKING_CONFIDENCE,
)
from .gesture_detector import GestureDetector
from .audio_player import AudioPlayer

def heart_shape(scale=1.0):
    pts = []
    for t in np.linspace(0, 2 * math.pi, 60):
        x = 16 * math.sin(t) ** 3
        y = 13 * math.cos(t) - 5 * math.cos(2 * t) - 2 * math.cos(3 * t) - math.cos(4 * t)
        pts.append((int(x * scale), int(-y * scale)))
    return np.array(pts, dtype=np.int32)

class HeartParticle:
    def __init__(self, w, h):
        self.reset(w, h)

    def reset(self, w, h):
        self.x = random.randint(50, w - 50)
        self.y = h + random.randint(20, 100)
        self.size = random.uniform(0.4, 1.2)
        self.speed = random.uniform(1.0, 3.0)
        self.wobble = random.uniform(0, 2 * math.pi)
        self.alpha = random.uniform(0.4, 0.8)
        self.color = random.choice([
            (0, 0, 255), (0, 100, 255), (200, 0, 200),
            (0, 150, 255), (255, 100, 200)
        ])

    def update(self, w, h):
        self.y -= self.speed
        self.wobble += 0.05
        self.x += math.sin(self.wobble) * 0.5
        if self.y < -60:
            self.reset(w, h)

class CameraApp:
    def __init__(self, music_file):
        self.cap = cv2.VideoCapture(CAMERA_SOURCE, cv2.CAP_DSHOW)
        self.cap.set(cv2.CAP_PROP_FRAME_WIDTH, FRAME_WIDTH)
        self.cap.set(cv2.CAP_PROP_FRAME_HEIGHT, FRAME_HEIGHT)

        if not self.cap.isOpened():
            print("Error: Cannot open camera.")
            sys.exit(1)

        for _ in range(30):
            self.cap.read()

        self.detector = GestureDetector(
            min_detection_confidence=MIN_DETECTION_CONFIDENCE,
            min_tracking_confidence=MIN_TRACKING_CONFIDENCE,
        )
        self.audio = AudioPlayer(music_file)
        self.is_blurred = False
        self.is_fullscreen = False
        self.window_name = "Foto Kita Blur - TikTok Trend"
        self.hearts = [HeartParticle(FRAME_WIDTH, FRAME_HEIGHT) for _ in range(20)]

    def draw_hearts(self, frame):
        for h in self.hearts:
            h.update(FRAME_WIDTH, FRAME_HEIGHT)
            scale = h.size * 3
            shape = heart_shape(scale)
            offset = shape + np.array([int(h.x), int(h.y)], dtype=np.int32)
            overlay = frame.copy()
            cv2.fillPoly(overlay, [offset], h.color)
            cv2.addWeighted(overlay, h.alpha, frame, 1 - h.alpha, 0, frame)

    def apply_blur(self, frame):
        k = BLUR_KERNEL_SIZE
        if k % 2 == 0:
            k += 1
        return cv2.GaussianBlur(frame, (k, k), 0)

    def run(self):
        self.audio.start()
        cv2.namedWindow(self.window_name, cv2.WINDOW_NORMAL)

        while True:
            ret, frame = self.cap.read()
            if not ret:
                break

            frame = cv2.flip(frame, 1)
            frame_rgb = cv2.cvtColor(frame, cv2.COLOR_BGR2RGB)
            hand_detected, peace_detected = self.detector.detect(frame_rgb)

            if peace_detected:
                self.is_blurred = True
            else:
                self.is_blurred = False

            display = self.apply_blur(frame) if self.is_blurred else frame
            self.draw_hearts(display)

            overlay = display.copy()
            if self.is_blurred:
                status = "BLUR ON "
                color = (0, 0, 255)
            elif hand_detected:
                status = "HAND DETECTED - make peace sign"
                color = (0, 255, 255)
            else:
                status = "BLUR OFF - show your hand"
                color = (0, 255, 0)

            cv2.putText(overlay, status, (10, 40),
                        cv2.FONT_HERSHEY_SIMPLEX, 0.7, color, 2)
            cv2.putText(overlay, "Peace sign (V) = BLUR | q = quit", (10, FRAME_HEIGHT - 20),
                        cv2.FONT_HERSHEY_SIMPLEX, 0.5, (200, 200, 200), 1)
            cv2.addWeighted(overlay, 0.3, display, 0.7, 0, display)

            cv2.imshow(self.window_name, display)

            key = cv2.waitKey(1) & 0xFF
            if key == ord('q'):
                break
            elif key == ord('f'):
                self.is_fullscreen = not self.is_fullscreen
                cv2.setWindowProperty(self.window_name, cv2.WND_PROP_FULLSCREEN,
                                      cv2.WINDOW_FULLSCREEN if self.is_fullscreen else cv2.WINDOW_NORMAL)
            elif key in (27, 9):
                break

        self.cleanup()

    def cleanup(self):
        self.detector.close()
        self.audio.stop()
        self.cap.release()
        cv2.destroyAllWindows()
