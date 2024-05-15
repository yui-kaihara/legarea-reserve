window.addEventListener('DOMContentLoaded', function () {
    
    //要素を取得
    var selectElement = document.getElementsByClassName('is-submit');

    for (var i = 0; i < selectElement.length; i++) {

        //変更が入った場合
        selectElement[i].addEventListener('change', function () {

            //要素に値があればその値のURLに遷移する
            if (this.value !== '') {
                // window.location.href = this.value;
                var form = this.closest('form');
                form.submit();
            }
        });
    }
});