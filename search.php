<?php

require_once ("util.php");

$headers = getallheaders();

if ($headers["Sec-Fetch-Dest"] !== "iframe") {
  http_response_code(401);
  die;
}

if (!isset($_GET["q"])) {
  header("location:/");
}

$query = urlencode($_GET["q"]);
$url = "https://html.duckduckgo.com/html?q=$query";

$results_html = file_get_contents($url);

if (!$results_html) {
  header("location:/error.php");

  die;
}

$simple = simplifyIncomingHtml($results_html);
$result_blocks = explode('<h2 class="result__title">', $simple);
$html = "";

// var_dump($blocks, $simple);

for ($x = 1; $x <= count($result_blocks) - 1; $x++) {
  // $block = $blocks[$x];
  if (strpos($result_blocks[$x], '<a class="badge--ad">') !== false) {
    continue;
  }
  // var_dump(explode('class="result__a" href="', $result_blocks[$x]));
  ;
  $result_link = explode('class="result__a" href="', $result_blocks[$x])[1];
  $result_topline = explode('">', $result_link);
  $result_link = urldecode(str_replace('//duckduckgo.com/l/?uddg=', '', explode("&amp;rut=", $result_topline[0])[0]));
  // $result_link = substr($result_link, 0, -1);
  // var_dump(explode("rut=", $result_topline[0]), $result_topline);
  // $result_link = explode('&rut=', $result_link)[0];
  // result title
  $result_title = str_replace("</a>", "", explode("\n", $result_topline[1]));
  // result display url
  $result_display_url = explode('class="result__url"', $result_blocks[$x])[1];
  $result_display_url = trim(explode("\n", $result_display_url)[1]);
  // result snippet
  $result_snippet = explode('class="result__snippet"', $result_blocks[$x])[1];
  $result_snippet = explode('">', $result_snippet)[1];
  $result_snippet = explode('</a>', $result_snippet)[0];

  $html .= <<<HTML
    <div class="result">
      <a href="$result_link" class="title">$result_title[0]</a>
      <p class="link">$result_link</p>
      <p class="snippet">$result_snippet</p>
    </div>
  HTML;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="css/main.css">
  <title><?php $query ?> - ArcWeb Find</title>
</head>

<body>
  <header>
    <a href="/"><img src="/img/logo.svg" alt="ArcOS"></a>
    <form action="">
      <input type="text" placeholder="Browse the web..." name="q" value="<?= htmlspecialchars($query) ?>">
      <button class="material-icons-round" type="submit">search</button>
    </form>
  </header>
  <main>
    <?= $html ?>
  </main>
</body>

</html>