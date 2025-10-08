<?php
echo "<pre>";
echo "FILE: ".__FILE__."\n";
echo "CWD : ".getcwd()."\n\n";
echo "_SERVER['DOCUMENT_ROOT']: ".$_SERVER['DOCUMENT_ROOT']."\n";
echo "_SERVER['SCRIPT_FILENAME']: ".$_SERVER['SCRIPT_FILENAME']."\n";
echo "_SERVER['SCRIPT_NAME']: ".$_SERVER['SCRIPT_NAME']."\n";
echo "_SERVER['REQUEST_URI']: ".$_SERVER['REQUEST_URI']."\n";
phpinfo(INFO_GENERAL);
