window.DNG = window.DNG || {}
window.DNG.Generic = window.DNG.Generic || {};

window.DNG.Generic.ElementAjaxCaller = function (element, headers, data, options) {
    var url = element.dataset.ajaxUrl || element.getAttribute("href");

    var method = element.dataset.ajaxMethod || "GET";
    var form = document.querySelector(element.dataset.ajaxForm);

    console.log(form);

    if (form) {
        var csrfTokenFieldName = "_token";
        var token = form.querySelector(`[name=${csrfTokenFieldName}]`).value;

        var headers = headers || {};
        headers['X-CSRF-TOKEN'] = headers['X-CSRF-TOKEN'] || token;

        var data = new FormData(form);
    }

    var updateContainer = document.querySelector(element.dataset.ajaxUpdate);

    return new Promise(function () {
        let xhr = new XMLHttpRequest();

        xhr.open(method, url);
        xhr.onload = function () {
            if (this.status >= 200 && this.status < 300) {
                this.onSuccess();
            } else {
                this.onerror();
            }
        };
        xhr.onSuccess = function () {
            updateContainer.innerHTML = xhr.responseText;
        }
        xhr.onerror = function () {
            console.error({
                status: this.status,
                statusText: xhr.statusText
            });
        };

        Object.assign(xhr, options);
        console.log(xhr);
        xhr.send(data);
    });
}
