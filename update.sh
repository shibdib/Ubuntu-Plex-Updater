#!/bin/bash
url=$1
sudo rm plexmediaserver_*
sudo wget $url
sudo dpkg -i plexmediaserver*