#!/bin/bash
rm -f acptemplates.tar
7z a -ttar -mx=9 acptemplates.tar ./acptemplates/*
rm -f files.tar
7z a -ttar -mx=9 files.tar ./files/*
# rm -f templates.tar
# 7z a -ttar -mx=9 templates.tar ./templates/*
rm -f eu.hanashi.wsc.teamspeak-api.tar
7z a -ttar -mx=9 eu.hanashi.wsc.teamspeak-api.tar ./* -x!acptemplates -x!files -x!templates -x!eu.hanashi.wsc.teamspeak-api.tar -x!.git -x!.gitignore -x!make.bat -x!make.sh