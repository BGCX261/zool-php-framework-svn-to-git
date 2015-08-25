<?php

!isset($_SERVER['HTTP_USER_AGENT']) || die('Can call from console.');

echo " ======== Zool autodeploy starts ========\n";
echo " ========================================\n\n";

$hash = '1';
while(true){

    $path = dirname(__FILE__);

    $output = shell_exec("php $path/deployer_helper.php $hash");

    $output = explode("\n", $output);

    $hash = $output[count($output)-2];

    $outputBuffer = '';

    for ($i=0; $i<count($output)-2; $i++)
        $outputBuffer .= $output[$i] ."\n";

    echo $outputBuffer;

    if(strpos($outputBuffer, 'INCOMPLETE DEPLOYMENT') !== false){

        $hash = '1';

        echo "\nPress ENTER if you solved the problem. Or press 'q' to quit from deployer.\n";

        fscanf(STDIN, "%s\n", $answer);

        if(strtolower(trim($answer)) == 'q'){
            die("\n");
        }

    }else{

        sleep(2);

    }

}