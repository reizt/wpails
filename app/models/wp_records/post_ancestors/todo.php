<?php
final class Todo extends Post{
  protected const POST_TYPE = 'todo';
  protected const ORIGINAL_COLUMNS = [
    ['name'=>'post_title', 'label'=>'タイトル', 'required'=>true],
    ['name'=>'deadline', 'label'=>'期日'],
    ['name'=>'post_content', 'label'=>'内容'],
    ['name'=>'pic_user_id', 'label'=>'担当者', 'type'=>'int', 'required'=>true],
    ['name'=>'project_id', 'label'=>'プロジェクト', 'type'=>'int', 'required'=>true],
  ];
}
