from django.conf.urls import url
from django.contrib import admin
from django.urls import path, include
from . import views

urlpatterns = [
    #静态页面URL
    path('', views.index),
    # path('send/', views.sends),
    path('data/', views.data),
]
