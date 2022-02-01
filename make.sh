#!/bin/bash
PACKAGE_NAME=de.xxschrarndxx.wsc.minecraft-api
PACKAGE_TYPES=(acptemplates files)

for i in "${PACKAGE_TYPES[@]}"
do
    rm -rf ${i}.tar
    7z a -ttar -mx=9 ${i}.tar ./${i}/*
done

rm -rf ${PACKAGE_NAME}.tar ${PACKAGE_NAME}.tar.gz
7z a -ttar -mx=9 ${PACKAGE_NAME}.tar ./* -x!acptemplates -x!files -x!templates -x!${PACKAGE_NAME}.tar -x!${PACKAGE_NAME}.tar.gz -x!.git -x!.gitignore -x!make.sh -x!make.bat -x!.github -x!php_cs.dist -x!phpcs.xml -x!Readme.md -x!pictures
7z a ${PACKAGE_NAME}.tar.gz ${PACKAGE_NAME}.tar

for i in "${PACKAGE_TYPES[@]}"
do
    rm -rf ${i}.tar
done

VERSION=`cat ./package.xml | grep -o '<version>*.*.*<\/version>'`
VERSION=${VERSION//<version>/}
VERSION=${VERSION//<\/version>/}


phpDocumentor -d . -t docs/$VERSION