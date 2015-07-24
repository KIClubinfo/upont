Installation - Mobile app
=========================

**If you want to develop the mobile app**, you must follow theses additionnal steps:

Android
-------

  * Download the [Android SDK](http://developer.android.com/sdk/installing/index.html?pkg=tools)
  * Add the path ``~/android-sdk-linux/tools/android` (or the SDK directory if you changed it) in your PATH environment variable
  * Launch the SDK with `android`
  * Download the SDK Tools and API 19 packages
  * In Tools > Manage AVDs, create an AVD, choose a phone model, an architecture and a skin
  * In the /mobile directory, launch `cordova platform add android`
  * To build the app, execute `cordova emulate android`

iOS
---
TODO
