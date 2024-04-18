#!/usr/bin/env sh

rig \
	--delete-route \
	--help foo \
	--list-routes \
	--new-module \
	--new-route \
	--start-servers \
	--update-route \
	--version \
	--view-action-log \
	--view-readme \
	--authority localhost:8080 \
	--defined-for-authorities localhost:8080 \
	--defined-for-files homepage.html \
	--defined-for-modules HelloWorld \
	--defined-for-named-positions roady-ui-main-content \
	--defined-for-positions 2 \
	--defined-for-requests Homepage \
	--for-authority localhost:8080 \
	--module-name HelloWorld \
	--named-positions roady-ui-main-content \
	--no-boilerplate \
	--open-in-browser \
	--path-to-roady-project ./ \
	--ports 3494 \
	--relative-path homepage.html \
	--responds-to Home \
	--route-hash 2340984
