from src.foto_kita_blur.config import MUSIC_FILE
from src.foto_kita_blur.camera_app import CameraApp


def main():
    app = CameraApp(music_file=MUSIC_FILE)
    app.run()


if __name__ == "__main__":
    main()
