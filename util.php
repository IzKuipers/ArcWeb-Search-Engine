<?php

function simplifyIncomingHtml($html)
{
  $html = str_replace('strong>', 'b>', $html); //change <strong> to <b>
  $html = str_replace('em>', 'i>', $html); //change <em> to <i>

  return $html;
}