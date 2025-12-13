// Đối tượng `Validator`
function Validator(options) {
  var selectorRules = {};

  // Hàm thực hiện validate 1 input
  function validate(inputElement, rule) {
    var errorMessage;
    var errorElement = inputElement.parentElement.querySelector(
      options.errorSelector
    );

    // Lấy ra các rule của selector
    var rules = selectorRules[rule.selector];

    // Lặp qua từng rule và kiểm tra
    for (var i = 0; i < rules.length; i++) {
      errorMessage = rules[i](inputElement.value);
      if (errorMessage) {
        break;
      }
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

  // Lấy element của form cần validate
  var formElement = document.querySelector(options.form);

  if (formElement) {
    // Khi submit form
    formElement.onsubmit = function (e) {
      // *** QUAN TRỌNG: chặn submit mặc định ***
      e.preventDefault();

      var isFormValid = true;

      // Lặp qua từng rule và validate
      options.rules.forEach(function (rule) {
        var inputElement = formElement.querySelector(rule.selector);
        var isValid = validate(inputElement, rule);

        if (!isValid) {
          isFormValid = false;
        }
      });

      if (isFormValid) {
        // Trường hợp submit với javascript
        if (typeof options.onSubmit === "function") {
          var enableInputs = formElement.querySelectorAll(
            "[name]:not([disabled])"
          );
          var formValues = Array.from(enableInputs).reduce(function (
            values,
            input
          ) {
            values[input.name] = input.value;
            return values;
          },
          {});
          options.onSubmit(formValues);
        } else {
          // Submit theo hành vi mặc định
          formElement.submit();
        }
      }
      // nếu không hợp lệ -> KHÔNG submit gì hết
    };

    // Lặp qua mỗi rule và xử lý (blur / input)
    options.rules.forEach(function (rule) {
      // Lưu lại các rules cho mỗi thẻ input
      if (Array.isArray(selectorRules[rule.selector])) {
        selectorRules[rule.selector].push(rule.test);
      } else {
        selectorRules[rule.selector] = [rule.test];
      }

      var inputElement = formElement.querySelector(rule.selector);

      if (inputElement) {
        // Blur khỏi input thì validate
        inputElement.onblur = function () {
          validate(inputElement, rule);
        };

        // Khi nhập thì clear lỗi
        inputElement.oninput = function () {
          var errorElement =
            inputElement.parentElement.querySelector(".form-message");
          errorElement.innerText = "";
          inputElement.parentElement.classList.remove("invalid");
        };
      }
    });
  }
}

// Định nghĩa rules
// 1. Bắt buộc
Validator.isRequired = function (selector, message) {
  return {
    selector: selector,
    test: function (value) {
      return value.trim()
        ? undefined
        : message || "Vui lòng nhập vào trường này";
    },
  };
};

// 2. Email
Validator.isEmail = function (selector, message) {
  return {
    selector: selector,
    test: function (value) {
      var regex = /^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/;
      return regex.test(value)
        ? undefined
        : message || "Định dạng email không hợp lệ";
    },
  };
};

// 3. Độ dài tối thiểu
Validator.minLength = function (selector, minLength) {
  return {
    selector: selector,
    test: function (value) {
      return value.length >= minLength
        ? undefined
        : "Vui lòng nhập tối thiểu " + minLength + " ký tự";
    },
  };
};

// 4. Confirm (nhập lại mật khẩu)
Validator.isConfirmed = function (selector, getConfirmValue, message) {
  return {
    selector: selector,
    test: function (value) {
      return value === getConfirmValue()
        ? undefined
        : message || "Mật khẩu nhập lại không khớp";
    },
  };
};
