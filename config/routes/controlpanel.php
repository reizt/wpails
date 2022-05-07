<?php
$controlpael_routes = [
  ['slug'=>'myinfo', 'title'=>'基本情報'],
  ['slug'=>'dashboard', 'title'=>'ダッシュボード'],
  ['slug'=>'projects', 'title'=>'プロジェクト管理'],
  ['slug'=>'project', 'is_namespace'=>true,
    'ancestors'=>[
      ['slug'=>'new', 'title'=>'新規プロジェクト'],
      ['slug'=>'info', 'title'=>'プロジェクト詳細'],
      ['slug'=>'edit', 'title'=>'プロジェクト編集'],
      ['slug'=>'todos', 'title'=>'プロジェクト TODO'],
      ['slug'=>'servers', 'title'=>'プロジェクト サーバー情報'],
      ['slug'=>'maintenances', 'title'=>'プロジェクト 保守管理'],
    ]
  ],
  ['slug'=>'todos', 'title'=>'TODO'],
  ['slug'=>'employees', 'title'=>'従業員管理', 'filter'=>'only_admin'],
  ['slug'=>'employee', 'is_namespace'=>true, 'filter'=>'only_admin',
    'ancestors'=>[
      ['slug'=>'new', 'title'=>'従業員追加'],
      ['slug'=>'info', 'title'=>'従業員詳細'],
      ['slug'=>'edit', 'title'=>'従業員編集'],
      ['slug'=>'attendances', 'title'=>'勤怠詳細'],
    ]
  ],
  ['slug'=>'clients', 'title'=>'顧客管理', 'filter'=>'only_admin'],
  ['slug'=>'client', 'is_namespace'=>true, 'filter'=>'only_admin',
    'ancestors'=>[
      ['slug'=>'new', 'title'=>'顧客追加'],
      ['slug'=>'info', 'title'=>'顧客詳細'],
      ['slug'=>'edit', 'title'=>'顧客編集'],
    ]
  ],
  ['slug'=>'daily_reports', 'title'=>'日報'],
  ['slug'=>'daily_report', 'is_namespace'=>true,
    'ancestors'=>[
      ['slug'=>'new', 'title'=>'新規日報'],
      ['slug'=>'info', 'title'=>'日報詳細'],
      ['slug'=>'edit', 'title'=>'日報編集'],
    ]
  ],
  ['slug'=>'attendances', 'title'=>'勤怠'],
  ['slug'=>'schedules', 'title'=>'スケジュール管理'],
  ['slug'=>'schedule', 'is_namespace'=>true,
    'ancestors'=>[
      ['slug'=>'new', 'title'=>'スケジュール追加'],
      ['slug'=>'info', 'title'=>'スケジュール詳細'],
      ['slug'=>'edit', 'title'=>'スケジュール編集'],
    ]
  ],
  ['slug'=>'stocks', 'title'=>'在庫管理'],
  ['slug'=>'stock', 'is_namespace'=>true,
    'ancestors'=>[
      ['slug'=>'new', 'title'=>'在庫追加'],
      ['slug'=>'info', 'title'=>'在庫詳細'],
      ['slug'=>'edit', 'title'=>'在庫編集'],
    ]
  ],
];
