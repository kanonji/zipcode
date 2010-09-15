====================
Zipcode Shell for CakePHP (Japanese)
====================

---------------
注意
---------------
| まだバギーなはずなので、動かない事もあるかもしれません。
| エラー処理やMacじゃない環境への配慮など、細かいところまで手が回ってない未完成品です。
| 全部出来上がってないですが、一応動く部分もあるので置いておきます。
| ちょうど自分が郵便番号を扱う必要があったので、作りながらついでにShellにしてみました。
| 未完成部分も今後作っていきたいので、もし気がついた点などあればご連絡頂けると助かります。

---------------
機能
---------------

1. http://www.post.japanpost.jp/zipcode/download.html からのCSVダウンロードとDBへの格納
2. 上記を格納するための、schemaを使ったテーブル生成
3. AjaxZip用のJSONの生成（予定）

---------------
使い方
---------------
1. cd APP/plugins
2. git clone git://github.com/kanonji/zipcode.git
3. cp zipcode/config/schema/zipcodes.php APP/config/schema/zipcodes.php
4. cake zipcode して [I]
5. cake bake model zipcodes
6. cake zipcode して [D] 

これでzipcodesテーブルに郵便番号とか住所データが格納されるはずです。

---------------
環境
---------------
- php 5.2.6
- CakePHP 1.3.4
- MAMP 1.7.2
- Mac OS X 10.5.8（Leopard）

System requirementというより、この環境で作りました的な意味です。