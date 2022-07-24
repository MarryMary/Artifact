# Artifact/Typer Library
PHP向け文字列操作ライブラリ  

# 概要
PHPを使った文字列操作をより簡単に行えるようにしたライブラリです。  
本ライブラリには現状以下のようなメソッドが備わっています。  
* Artifactクラス・StartAnalyzeメソッド（ネスト追跡機能付き囲み文字内文字列抽出機能）  
* trim_X メソッド(trim_r, trim_l, trim_all)  
* printメソッド（引数に渡した文字列すべてを連結して出力する機能が標準装備）  
* exchange_arrayメソッド（配列のような記述になっている文字列をPHPで配列として使用できるように変換）  
* exchange_stringメソッド（配列をまるごと文字列に変換する）  
* writer_stringメソッド（配列をまるごとファイルにエクスポートするメソッド）
* get_typeメソッド（文字列の型推論）  
* exchange_autotypeメソッド（get_typeメソッドの結果を使用して自動で型変換）  