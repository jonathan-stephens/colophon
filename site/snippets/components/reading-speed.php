<?php
  $wordCount = $article->text()->words();
  $minSpeed = 167; // words per minute
  $maxSpeed = 285; // words per minute

  $minSeconds = ceil($wordCount / ($minSpeed / 60));
  $maxSeconds = ceil($wordCount / ($maxSpeed / 60));

  if ($minSeconds < 60) {
    if ($minSeconds === $maxSeconds) {
      echo $minSeconds . ' sec read';
    } else {
      echo $maxSeconds . '&thinsp;–&thinsp;' . $minSeconds . '  sec read';
    }
  } else {
    $minMinutes = ceil($minSeconds / 60);
    $maxMinutes = ceil($maxSeconds / 60);
    if ($minMinutes === $maxMinutes) {
      echo $minMinutes . ' min read';
    } else {
      echo $maxMinutes . '&thinsp;–&thinsp;' . $minMinutes . ' min read';
    }
  }
?>
