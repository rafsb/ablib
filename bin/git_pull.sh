#!/bin/bash -e
cd ../..
git add -A
git commit -m "$1"
git pull
git push