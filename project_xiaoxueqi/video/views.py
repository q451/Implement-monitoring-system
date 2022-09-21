import threading
from django.shortcuts import render

# Create your views here.
from django.contrib.auth.hashers import make_password, check_password
from django.shortcuts import render
from django.http import HttpResponse, StreamingHttpResponse
from django.template import loader
import json
from django.http import HttpResponse
from django.views.decorators.csrf import csrf_exempt
import cv2

class VideoCamera(object):
    def __init__(self):
        self.video = cv2.VideoCapture(0)
        (self.grabbed, self.frame) = self.video.read()
        threading.Thread(target=self.update, args=()).start()

    def __del__(self):
        self.video.release()

    def get_frame(self):
        image = self.frame
        _, jpeg = cv2.imencode('.jpg', image)
        return jpeg.tobytes()

    def update(self):
        while True:
            (self.grabbed, self.frame) = self.video.read()


def gen(camera):
    while True:
        frame = camera.get_frame()
        yield(b'--frame\r\n'
              b'Content-Type: image/jpeg\r\n\r\n' + frame + b'\r\n\r\n')


@csrf_exempt
def index(request):
    try:
        cam = VideoCamera()
        return StreamingHttpResponse(gen(cam), content_type="multipart/x-mixed-replace;boundary=frame")
    except:  # This is bad! replace it with proper handling
        pass

def data(request):
    list1 = ['physics', 'chemistry', 1997, 2000]
    return HttpResponse(111) 

