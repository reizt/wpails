<?php
final class Todo extends Post{
  protected const POST_TYPE = 'todo';
  protected const ORIGINAL_COLUMNS = [
    ['name'=>'post_title', 'label'=>'タイトル', 'required'=>true],
    ['name'=>'deadline', 'label'=>'期日'],
    ['name'=>'post_content', 'label'=>'内容'],
  ];
}
