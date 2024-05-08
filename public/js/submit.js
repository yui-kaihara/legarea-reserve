
window.addEventListener('DOMContentLoaded', function () {
    
    //要素を取得
    var selectElement = document.getElementById('is-change');
    
    //変更が入った場合
    selectElement.addEventListener('change', function () {

        //要素に値があればその値のURLに遷移する
        if (selectElement.value !== '') {
            window.location.href = selectElement.value;
        }
    });
});