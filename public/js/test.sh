#!/bin/bash
for test in $@
do
    test2=`echo $test | awk -F "." '{print $1"-min.js"}'`
    cp $test $test2
done
