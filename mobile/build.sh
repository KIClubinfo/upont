#!/bin/bash
# [AT'016] Release de l'appli mobile

cordova build --release
cp platforms/android/ant-build/CordovaApp-release.apk upont-mobile.apk
