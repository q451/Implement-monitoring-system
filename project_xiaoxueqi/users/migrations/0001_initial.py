# Generated by Django 3.2.5 on 2022-07-04 06:27

from django.db import migrations, models


class Migration(migrations.Migration):

    initial = True

    dependencies = [
    ]

    operations = [
        migrations.CreateModel(
            name='account',
            fields=[
                ('id', models.BigAutoField(auto_created=True, primary_key=True, serialize=False, verbose_name='ID')),
                ('userid', models.CharField(max_length=128)),
                ('password', models.CharField(max_length=128)),
                ('email', models.EmailField(max_length=254, unique=True)),
                ('time_created', models.DateTimeField(auto_now_add=True)),
            ],
            options={
                'verbose_name': '用户',
                'verbose_name_plural': '用户',
                'ordering': ['time_created'],
            },
        ),
    ]