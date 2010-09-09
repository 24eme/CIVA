<?php

function sprintFloat($float) 
{
  return sprintf("%01.02f", $float);

}

function echoFloat($float) 
{
  echo sprintFloat($float);
}