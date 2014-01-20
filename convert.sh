#!/bin/bash
# Конвертирует модули с utf-8 в cp1251
 
for i in `find ./ -type f -name '*.php'`; do
        iconv -f utf-8 -t cp1251 $i >> $i.utf
	mv $i.utf $i
        echo "Convert " $i
done 