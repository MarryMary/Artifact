<?php

include "src/Typer.php";

use MarryMary\Artifact\Typer;

$instance = new Typer();
var_dump($instance->exchange_array("[this,is,nested_array,and,[nest]]"));