#!/bin/bash

curenv="dev"

if [ -n "$1" ]; then
    curenv="$1"
fi

cp api/index.php.sample api/index.php

cp app/config/db.php.sample app/config/db.php
cp app/config/params.php.sample app/config/params.php
cp app/config/api_"$curenv".php.sample app/config/api_"$curenv".php


if [ ! -d "./app/runtime" ]; then
    mkdir app/runtime
    chmod a+w app/runtime
fi
