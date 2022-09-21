from django.conf.urls import url
from django.contrib import admin
from django.urls import path, include
from . import views

urlpatterns = [
    #静态页面URL
    # path('', views.video),
    path('chack/', views.facecheck),
    path('webchack/', views.Onlinefacecheck),
    path('quyu/', views.quyucheck),
    path('shouji/', views.shouji),
]
