import gzip
from time import sleep
import time

from django.shortcuts import render
# Create your views here.
import cv2
import numpy as np
from imutils.object_detection import non_max_suppression
from pip import main
import pygame
import smtplib
from email.mime.text import MIMEText
from email.mime.multipart import MIMEMultipart
from django.http import StreamingHttpResponse
from django.views.decorators.csrf import csrf_exempt
from django.http import HttpResponse
from imutils.object_detection import non_max_suppression
from imutils import paths
import numpy as np
import imutils
import cv2
import pymysql

class VideoCamera:
    # 发送邮件提醒
    def send_email(self,msg_from, passwd, msg_to, text_content):
        msg = MIMEMultipart()
        subject = "危险报警"  # 主题
        text = MIMEText(text_content)
        msg.attach(text)
        message = ""
        msg['Subject'] = subject
        msg['From'] = msg_from
        msg['To'] = msg_to
        try:
            s = smtplib.SMTP_SSL("smtp.qq.com", 465)
            s.login(msg_from, passwd)
            s.sendmail(msg_from, msg_to, msg.as_string())
            message = ("发送成功")
        except smtplib.SMTPException as e:
            message = ("发送失败")
        finally:
            s.quit()
            return message

def gen_display(camera):
    # 视频流相机对象
    hog = cv2.HOGDescriptor()   #初始化方向梯度直方图描述子
    hog.setSVMDetector(cv2.HOGDescriptor_getDefaultPeopleDetector())  #设置支持向量机(Support Vector Machine)使得它成为一个预先训练好了的行人检测器
    faceCascade = cv2.CascadeClassifier(r'D:\Python\Python38\Lib\site-packages\cv2\data\haarcascade_frontalface_alt.xml')
    # 识别眼睛的分类器
    eyeCascade = cv2.CascadeClassifier(r'D:\Python\Python38\Lib\site-packages\cv2\data\haarcascade_eye.xml')    
    while True:
        # 读取图片
        ret, frame = camera.read()
        frame = imutils.resize(frame, width=min(400, frame.shape[1]))
        gray = cv2.cvtColor(frame, cv2.COLOR_BGR2GRAY)
        # gray = cv2.GaussianBlur(gray,(21,21),0)  # 对灰阶图像进行高斯模糊
        # 人脸检测
        faces = faceCascade.detectMultiScale(gray, scaleFactor=1.1, minNeighbors=5, minSize=(30, 30))
        # 在检测人脸的基础上检测眼睛
        # for (x, y, w, h) in faces:
            # fac_gray = gray[y: (y+h), x: (x+w)]
            # result = []
            # eyes = eyeCascade.detectMultiScale(fac_gray, 1.3, 2)

            # 眼睛坐标的换算，将相对位置换成绝对位置
            # for (ex, ey, ew, eh) in eyes:
            #     result.append((x+ex, y+ey, ew, eh))
        # 画矩形
        for (x, y, w, h) in faces:
            cv2.rectangle(frame, (x, y), (x+w, y+h), (255, 0, 0), 2)
        # for (ex, ey, ew, eh) in result:
        #     cv2.rectangle(frame, (ex, ey), (ex+ew, ey+eh), (0, 255, 0), 2)
        if ret:
            # 将图片进行解码
            ret, frame = cv2.imencode('.jpeg', frame)
            if ret:
                # 转换为byte类型的，存储在迭代器中
                yield (b'--frame\r\n'
                       b'Content-Type: image/jpeg\r\n\r\n' + frame.tobytes() + b'\r\n')


def facecheck(request):
    camera = cv2.VideoCapture(0)
    # 使用流传输传输视频流
    return StreamingHttpResponse(gen_display(camera), content_type='multipart/x-mixed-replace; boundary=frame')

def Onlinefacecheck(request):
    camera = cv2.VideoCapture('rtmp://192.168.31.154:1935/live/123')
    # 使用流传输传输视频流
    return StreamingHttpResponse(gen_display(camera), content_type='multipart/x-mixed-replace; boundary=frame')

def gen_quyu(camera):
    cam = VideoCamera()
    msg_from = '1393371859@qq.com'  # 发送方邮箱
    passwd = 'vgfgezofbpcbibca'  # 填入发送方邮箱的授权码（就是刚刚你拿到的那个授权码）
    msg_to = '19301105@bjtu.edu.cn'  # 收件人邮箱
    text_content = "禁区有人进入，危险!"  # 发送的邮件内容
    hog = cv2.HOGDescriptor()
    #设置支持向量机使得它成为一个预先训练好了的行人检测器
    hog.setSVMDetector(cv2.HOGDescriptor_getDefaultPeopleDetector())
    #读取摄像头视频
    try:
        db = pymysql.connect(host='106.14.13.251', user='root', password='yxp123456', db='Xiaoxueqi')
        print("连接成功")
    except pymysql.Error as e:
        print("创建失败" + str(e))
        
    counter = 100
    while True:
        ret, frame = camera.read()
        # 行人检测
        (rects, weights) = hog.detectMultiScale(frame, winStride=(4, 4), padding=(8, 8), scale=1.05)
        # 设置来抑制重叠的框
        rects = np.array([[x, y, x + w, y + h] for (x, y, w, h) in rects])
        pick = non_max_suppression(rects, probs=None, overlapThresh=0.65)
        # 绘制红色人体矩形框
        for (x, y, w, h) in pick:
            cv2.rectangle(frame, (x, y), (x + w, y + h), (0, 0, 255), 2)
        # 绘制蓝色危险区域框
        cv2.rectangle(frame, (350, 50), (600, 450), (255, 0, 0), 2)  # 原图 左上角坐标 有效角坐标  颜色 画线宽度
        # 打印检测到的目标个数
        # k = len(pick)
        counter = counter-1
        if counter ==0:
            counter = 100
            if len(pick) > 0:
                current_time = time.strftime('%Y-%m-%d %H:%M:%S',time.localtime(time.time()))
                try:
                    cur = db.cursor()
                    SQL = "INSERT INTO event_info(event_type, event_date, event_location , event_desc) VALUE (%s,%s,%s,%s)"
                    value = (4, (current_time), "院子", '[EVENT] %s, 院子, 有人闯入禁止区域!!!' % (current_time))
                    cur.execute(SQL, value)
                    db.commit()
                    print("！！！区域检测事件插入成功！！！")
                except pymysql.Error as e:
                    print("插入失败" + str(e))
                cam.send_email(msg_from, passwd, msg_to, text_content)
            
        if ret:
                # 将图片进行解码
                ret, frame = cv2.imencode('.jpeg', frame)
                if ret:
                    # 转换为byte类型的，存储在迭代器中
                    yield (b'--frame\r\n'
                        b'Content-Type: image/jpeg\r\n\r\n' + frame.tobytes() + b'\r\n')


def quyucheck(request):
    camera = cv2.VideoCapture(0)
    # 使用流传输传输视频流
    return StreamingHttpResponse(gen_quyu(camera), content_type='multipart/x-mixed-replace; boundary=frame')

def face(camera):
    action_list = [ 'open_mouth', 'smile', 'rise_head', 'look_left', 'look_right']
    action_map = {
        'open_mouth': 'please open your mouth',
        'smile': 'please smile ', 
        'rise_head': 'please raise you head',
        'look_left': 'please look left',
        'look_right': 'please look right'
    }
    counter = 100
    while True:
        counter = counter-1
        ret, frame = camera.read()
        print(counter)
        if counter == 0:
            for action in action_list:
                
                start = action_map[action]
                cv2.putText(frame, str(start), (5,50 ), cv2.FONT_HERSHEY_SIMPLEX, 0.75, (0, 0, 255), 2)
                for i in range(10): 
                    cv2.imwrite("static/" + action + str(i) + '.jpg', frame)
        if ret:
            # 将图片进行解码
            ret, frame = cv2.imencode('.jpeg', frame)
            if ret:
                # 转换为byte类型的，存储在迭代器中
                yield (b'--frame\r\n'
                    b'Content-Type: image/jpeg\r\n\r\n' + frame.tobytes() + b'\r\n')

def shouji(request):
    camera = cv2.VideoCapture(0)
    # 使用流传输传输视频流
    return StreamingHttpResponse(face(camera), content_type='multipart/x-mixed-replace; boundary=frame')