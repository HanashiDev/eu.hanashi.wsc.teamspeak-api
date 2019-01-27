#!/bin/bash
rm -f acptemplates.tar
7z a -ttar -mx=9 acptemplates.tar ./acptemplates/*
rm -f files.tar
7z a -ttar -mx=9 files.tar ./files/*
rm -f eu.hanashi.wsc.teamspeak-api.tar
rm -f eu.hanashi.wsc.teamspeak-api.tar.gz
7z a -ttar -mx=9 eu.hanashi.wsc.teamspeak-api.tar ./* -x!acptemplates -x!files -x!templates -x!eu.hanashi.wsc.teamspeak-api.tar -x!.git -x!.gitignore -x!make.bat -x!make.sh
7z a eu.hanashi.wsc.teamspeak-api.tar.gz eu.hanashi.wsc.teamspeak-api.tar
