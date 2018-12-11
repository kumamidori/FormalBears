# FormalBears

- このリポジトリは [@iteman (KUBO Atuhiro)](https://github.com/iteman) さんによる `FormalBears` をフォークして改変して作ったものです。
- FormalBears オリジナルプロジェクトは現在のところ限定公開ですが、著者による紹介のスライドが [こちら](https://www.slideshare.net/iteman/the-birth-of-formalbears) で公開されています。
- オリジナルプロジェクトの方にはさまざまな機能がありますが、このパッケージは、そのうちの `コンフィグレーション言語機能` だけを取り出したサブセット版です。
- 本家とはパッケージのライフサイクルが独立しています。

## Requires

PHP7.0.8以上

## Install

```
composer require fob/formalbears
```

## 機能

1. 設定ファイルによる設定（ `YAML` ）とグラマー定義を通したコンパイル機能（ `symfony/config` 統合）
2. 設定のマージ（デフォルト値の設定、development / production といった環境毎のオーバーライド）
3. 環境変数統合

## Application Directories

アプリケーションパッケージはルートレベルのディレクトリとして下記ディレクトリを必要とします。

| Subject | it MUST be named: |
| ----------------------------------------------- | -------------------------- |
| configuration files                             | `etc/config/`                  |


`config` ディレクトリは下記のサブディレクトリを必要とします。

```
[config]
   |
   +--- [contexts] コンテキストグローバルの設定 
   +--- [modules] 各モジュールの設定
```

たとえば下記のように使います。

```
[config]
   |
   +--- [contexts] コンテキストグローバル設定 
            |
            +--- app.yml
            +--- api.yml
            +--- cli.yml
            +--- prod.yml
            
   +--- [modules] 各モジュールの設定
            |
            [my_project_foo] MyProjectFooModule に対応する設定ルートディレクトリ
                |
                +--- [app]
                        |
                        +--- my_project_foo.yaml
                +--- [prod]
                        |
                        +--- my_project_foo.yaml
```

上記のように設定すれば、prod の環境では `app` の設定に `prod` のオーバーライド設定をマージさせることができます。

## TODO

- テスト
- CI
- CS

## Copyright

Copyright (c) 2018 Atsuhiro Kubo, Nana Yamane, All rights reserved.

## License

[The BSD 2-Clause License](http://opensource.org/licenses/BSD-2-Clause)
