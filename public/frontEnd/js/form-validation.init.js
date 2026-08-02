$(document).ready(function () {
    if ($.fn.parsley) {
        $(".parsley-examples").parsley();
        var $demoForm = $("#demo-form");
        if ($demoForm.length) {
            var demoParsley = $demoForm.parsley();
            if (demoParsley && typeof demoParsley.on === 'function') {
                demoParsley.on("field:validated", function () {
                    var e = 0 === $(".parsley-error").length;
                    $(".alert-info").toggleClass("d-none", !e);
                    $(".alert-warning").toggleClass("d-none", e);
                }).on("form:submit", function () {
                    return false;
                });
            }
        }
    }
});