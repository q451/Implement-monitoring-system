import time
import cv2
cap = cv2.VideoCapture(0)
while (1):  
    ret, img = cap.read()
    start = "吃核查"

    # 图片 添加的文字 位置 字体 字体大小 字体颜色 字体粗细
    cv2.putText(img, start, (5,50 ), cv2.FONT_HERSHEY_SIMPLEX, 0.75, (0, 0, 255), 2)
    cv2.imshow("image", img)
    if cv2.waitKey(1) & 0xFF == ord('q'):
        break
cap.release()  
cv2.destroyAllWindows()  