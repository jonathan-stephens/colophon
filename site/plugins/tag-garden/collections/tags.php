<?php

return [
  'tags' => function () {
    return site()->index()->pluck('tags', ',', true);
  },
];
