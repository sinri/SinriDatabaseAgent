#!/bin/bash
phpcbf --report=full --standard=PSR2 --ignore=vendor . 
phpcs --report=full --standard=PSR2 --ignore=vendor . 
if [ $? -eq 0 ]; then
	echo 'PSR2 verified'
else
	echo 'PSR2 warned'
fi