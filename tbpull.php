<?php
// 
echo "tbpull";
//`tbpull`;
//`git pull`;
/*
echo shell_exec("ssh -p 722 tinkerbi@hera.krystal.co.uk <<'ENDSSH'
#commands to run on remote host
cd public_html/gitstart
pwd
git pull
ENDSSH"
);

*/
echo (shell_exec('./tbpull.sh'));