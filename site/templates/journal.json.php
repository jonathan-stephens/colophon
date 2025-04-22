<?php

$data = $pages->find('blog')->children()->published()->paginate(10);
$json = [];

$json['data']  = [];
$json['pages'] = $data->pagination()->pages();
$json['page']  = $data->pagination()->page();

foreach($data as $article) {

  $json['data'][] = array(
    'url'   => (string)$article->url(),
    'title' => (string)$article->title(),
    'text'  => (string)$article->text(),
    'date'  => (string)$article->date()
  );

}

echo json_encode($json);
