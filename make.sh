#!/bin/bash
PACKAGE_NAME=de.xxschrandxx.wsc.minecraft-api
PACKAGE_TYPES=(acptemplates files)

for i in "${PACKAGE_TYPES[@]}"
do
    rm -rf ${i}.tar
    tar -c ${i}.tar ./${i}/*
done

rm -rf ${PACKAGE_NAME}.tar
tar -c ${PACKAGE_NAME}.tar ./* -x!acptemplates -x!files -x!templates -x!${PACKAGE_NAME}.tar -x!${PACKAGE_NAME}.tar.gz -x!.git -x!.gitignore -x!make.sh -x!make.bat -x!.github -x!php_cs.dist -x!phpcs.xml -x!Readme.md -x!pictures

for i in "${PACKAGE_TYPES[@]}"
do
    rm -rf ${i}.tar
done
