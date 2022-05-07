<?php
final class Client extends User{
  protected const USER_TYPE = 'client';
  protected const ORIGINAL_COLUMNS = [
    ['name'=>'display_name', 'label'=>'会社名', 'validations'=>['required'=>true]],
    ['name'=>'permission', 'label'=>'アクセス権限', 'default'=>'client'],
  ];
}
