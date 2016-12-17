import ScreenCloud
from PythonQt.QtCore import QFile, QSettings, QUrl
from PythonQt.QtGui import QWidget, QDialog, QDesktopServices, QMessageBox
from PythonQt.QtUiTools import QUiLoader
import requests, time, os

###############################
## This is a temporary fix, should be removed when a newer python version is used ##
import logging
logging.captureWarnings(True)
###############################

class ImageserveUploader():
  def __init__(self):
    self.uil = QUiLoader()
    self.loadSettings()
    
  def showSettingsUI(self, parentWidget):
    self.parentWidget = parentWidget
    self.settingsDialog = self.uil.load(QFile(workingDir + "/settings.ui"), parentWidget)
    # self.settingsDialog.group_name.input_name.connect("textChanged(QString)", self.nameFormatEdited)
    self.settingsDialog.connect("accepted()", self.saveSettings)
    
    self.loadSettings()
    self.updateUi()
    self.settingsDialog.open()

  def updateUi(self):
    #self.loadSettings()
    self.settingsDialog.group_main.input_address.text = self.uploadURL;
    self.settingsDialog.group_main.input_passkey.text = self.passkey;
    self.settingsDialog.group_main.input_user.text = self.user;
    
    self.settingsDialog.adjustSize()

  def loadSettings(self):
    settings = QSettings()
    settings.beginGroup("uploaders")
    settings.beginGroup("imageserve")
    self.passkey = settings.value("passkey", "")
    self.uploadURL = settings.value("upload", "")
    self.user = settings.value("user", "")
    settings.endGroup()
    settings.endGroup()

  def saveSettings(self):
    settings = QSettings()
    settings.beginGroup("uploaders")
    settings.beginGroup("imageserve")
    settings.setValue("passkey", self.settingsDialog.group_main.input_passkey.text);
    settings.setValue("upload", self.settingsDialog.group_main.input_address.text);
    settings.setValue("user", self.settingsDialog.group_main.input_user.text);
    settings.endGroup()
    settings.endGroup()
  
  def isConfigured(self):
    self.loadSettings()
    return self.uploadURL != "" and self.passkey != ""
    
  def getFilename(self):
    return "Screenshot"
    # self.loadSettings()
    # return ScreenCloud.formatFilename(self.nameFormat)
        
  def upload(self, screenshot, name):
    self.loadSettings()
    
    if not self.isConfigured():
      return False;
    
    #Save to a temporary file
    timestamp = time.time()
    try:
      tmpFilename = QDesktopServices.storageLocation(QDesktopServices.TempLocation) + "/" + ScreenCloud.formatFilename(str(timestamp))
    except AttributeError:
      from PythonQt.QtCore import QStandardPaths #fix for Qt5
      tmpFilename = QStandardPaths.writableLocation(QStandardPaths.TempLocation) + "/" + ScreenCloud.formatFilename(str(timestamp))
    screenshot.save(QFile(tmpFilename), ScreenCloud.getScreenshotFormat())
    
    imgFile = open(tmpFilename, 'rb')
    
    # Send request
    if (self.user != ""):
      params = { 'password': self.passkey, 'user':self.user };
    else:
      params = { 'password': self.passkey };
    format = ("image/jpeg" if ScreenCloud.getScreenshotFormat() == "jpg" else "image/png")
    
    req = requests.request("POST", self.uploadURL+"/upload.php", data=params, files={ 'image': (str(timestamp), imgFile, format)  } )
    
    data = req.text.split(",", 2)
    if (data[0] == "error"):
      ScreenCloud.setError("Failed to upload image with error: " + data[1])
      return False;
    
    ScreenCloud.setUrl(self.uploadURL + data[1]);
    return True;
    