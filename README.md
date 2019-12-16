# DEPRECATED

[Notice] this package is abandoned and the recommended alternative is [formal-bears/formal-bears](https://github.com/kumamidori/FormalBears.FormalBears).

-----

# FormalBears

 
Meta framwork for [BEAR.Sunday](https://github.com/bearsunday/BEAR.Sunday) applications

- [@iteman (KUBO Atuhiro)](https://github.com/iteman) さんによるメタフレームワーク `FormalBears` をコピー、改変して作りました（著者の承諾を得て公開しています）。
- オリジナル版（本家のリポジトリ）は限定公開です。著者による紹介のスライドが [こちら](https://www.slideshare.net/iteman/the-birth-of-formalbears) で公開されています。

## Requires

PHP7.1以上

## Install

```
composer require fob/formalbears
```

## 機能

- 設定ファイルによる設定（ `YAML` ）とグラマー定義を通したコンパイル機能（ `symfony/config` 統合）
- 設定のマージ（デフォルト値の設定、development / production といった環境毎のオーバーライド）
- 環境変数統合
- マルチバインディング (ref. [Multibindings · google/guice](https://github.com/google/guice/wiki/Multibindings) )

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

## Links

- [kumamidori/FormalBearsDemo](https://github.com/kumamidori/FormalBearsDemo)
- [\[FormalBears\] Configurable BEAR\.Sunday \- Qiita](https://qiita.com/kumamidori/items/53f3a271e3de70c5abf4)

## TODO

- 環境変数統合のサンプルアプリケーション追加
- テスト
- CI
- CS

## Copyright

Copyright (c) 2019 Atsuhiro Kubo, Nana Yamane, All rights reserved.

## License

[The BSD 2-Clause License](http://opensource.org/licenses/BSD-2-Clause)
