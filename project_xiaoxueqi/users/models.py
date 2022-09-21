from django.db import models

# Create your models here.
class account(models.Model):
    userid = models.CharField(max_length=128)
    password = models.CharField(max_length=128)
    email = models.EmailField(unique=True)

