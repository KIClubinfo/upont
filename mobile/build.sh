#!/bin/bash
# [AT'016] Release de l'appli mobile

cordova build --release
jarsigner -verbose -sigalg SHA1withRSA -digestalg SHA1 -keystore ../utils/upont.mobile.keystore platforms/android/build/outputs/apk/android-release-unsigned.apk youpontmobile
cp platforms/android/build/outputs/apk/android-release-unsigned.apk upont-mobile.apk
