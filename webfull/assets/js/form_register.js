// Đối tượng Validator
function Validator(options) {
    var selectorRules = {};

    // Hàm thực hiện validate
    function validate(inputElement, rule) {
        var errorElement = inputElement.parentElement.querySelector(options.errorSelector);
        var errorMessage;

        // Lấy ra các rule của selector
        var rules = selectorRules[rule.selector];

        for (var i = 0; i < rules.length; i++) {
            errorMessage = rules[i](inputElement.value);
            if (errorMessage) break;
        }

        if (errorMessage) {
            errorElement.innerText = errorMessage;
            inputElement.parentElement.classList.add("invalid");
        } else {
            errorElement.innerText = "";
            inputElement.parentElement.classList.remove("invalid");
        }

        return !errorMessage;
    }

    // Lấy element của form
    var formElement = document.querySelector(options.form);

    if (formElement) {
        formElement.onsubmit = function (e) {

            var isFormValid = true;

            options.rules.forEach(function (rule) {
                var inputElement = formElement.querySelector(rule.selector);
                var isValid = validate(inputElement, rule);

                if (!isValid) {
                    isFormValid = false;
                }
            });

            // ❗ NGĂN SUBMIT nếu có lỗi
            if (!isFormValid) {
                e.preventDefault();
                return false;
            }
        };

        options.rules.forEach(function (rule) {

            if (Array.isArray(selectorRules[rule.selector])) {
                selectorRules[rule.selector].push(rule.test);
            } else {
                selectorRules[rule.selector] = [rule.test];
            }

            var inputElement = formElement.querySelector(rule.selector);

            if (inputElement) {
                inputElement.onblur = function () {
                    validate(inputElement, rule);
                };

                inputElement.oninput = function () {
                    var errorElement = inputElement.parentElement.querySelector(".form-message");
                    errorElement.innerText = "";
                    inputElement.parentElement.classList.remove("invalid");
                };
            }
        });
    }
}


// RULES -------------------------
Validator.isRequired = function (selector, message) {
    return {
        selector: selector,
        test: function (value) {
            return value.trim() ? undefined : message;
        }
    }
}

Validator.isEmail = function (selector, message) {
    return {
        selector: selector,
        test: function (value) {
            var regex = /^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/;
            return regex.test(value) ? undefined : message;
        }
    }
}

Validator.minLength = function (selector, minLength) {
    return {
        selector: selector,
        test: function (value) {
            return value.length >= minLength ? undefined : `Vui lòng nhập tối thiểu ${minLength} ký tự`;
        }
    }
}

Validator.isConfirmed = function (selector, getConfirmValue, message) {
    return {
        selector: selector,
        test: function (value) {
            return value === getConfirmValue() ? undefined : message;
        }
    }
}
