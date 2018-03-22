#!/bin/sh

find .. -type f -name *.php > phps

xgettext -L PHP --keyword=l:1 -F --copyright-holder=PoliEdro -j -f phps -d messages -p .

mv messages.po messages.pot

rm phps
