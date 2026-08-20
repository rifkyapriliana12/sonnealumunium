import pygame
import os

class AudioPlayer:
    def __init__(self, music_file):
        self.music_file = music_file
        self.initialized = False

    def start(self):
        if not os.path.exists(self.music_file):
            return
        try:
            pygame.mixer.init()
            pygame.mixer.music.load(self.music_file)
            pygame.mixer.music.play(-1)
            self.initialized = True
        except Exception:
            self.initialized = False

    def stop(self):
        if self.initialized:
            pygame.mixer.music.stop()
            pygame.mixer.quit()
