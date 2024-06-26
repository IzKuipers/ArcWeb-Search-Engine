<?php

$show_results = false;

if (isset($_GET["q"])) {
  $query = urlencode(trim($_GET["q"]));
  $show_results = true;
}