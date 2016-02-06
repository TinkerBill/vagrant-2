#!/bin/sh
ssh -p 722 tinkerbi@hera.krystal.co.uk <<'ENDSSH'
#commands to run on remote host
cd public_html/gitstart
pwd
git pull
ENDSSH
