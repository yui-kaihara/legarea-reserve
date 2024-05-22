window.addEventListener('DOMContentLoaded', function () {

    var copyButton = document.getElementsByClassName('is-copyText');

    //ボタンがクリックされたら
    for (var i = 0; i < copyButton.length; i++) {
        
        copyButton[i].addEventListener('click', function () {

            //コピー対象のテキストを設定する
            var protocol = window.location.protocol;
            var hostname = window.location.hostname;
            var hashId = this.dataset.hashId;
            var copyText = protocol + '//' + hostname + '/form?id=' + hashId;
            
            //テキストをクリップボードにコピーする
            navigator.clipboard.writeText(copyText);
        
            //開催回を設定
            var times = '第' + this.dataset.times + '回';
    
            //コピーをお知らせする
            alert(times + '申込フォームのURLをコピーしました。');
            
            return false;
        });
    }
});