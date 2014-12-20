#!/bin/bash

cd ..
git pull

git submodule update
git submodule foreach git pull

cd nodejs

if hash forever 2>/dev/null; then
	forever restart server.js
elif hash nodemon 2>/dev/null; then
	nodemon start server.js
else
	node server.js
fi