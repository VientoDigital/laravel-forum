<script  type="text/javascript">
    function InputBoolean (el) {
        var self = this;
        self.el = el;
        self.el.type = 'hidden';
        self.el.removeAttribute('boolean');
        self.checkbox = document.createElement('INPUT');
        self.checkbox.classList.add('form-check-input')
        self.checkbox.type = 'checkbox';
        self.checkbox.addEventListener('click', function (e) {
            self.toggle();
        });
        self.el.parentNode.append(self.checkbox);
        self.check = function () {
            self.el.value = 1;
            self.checkbox.setAttribute('checked', 'checked');
        }

        self.uncheck = function () {
            self.el.value = 0;
            self.checkbox.removeAttribute('checked');
        }

        self.toggle = function () {
            (self.el.value == '1')
                ? self.uncheck()
                : self.check();
        }


        if (self.el.value === '1') {
            self.check();
        } else {
            self.uncheck();
        }
    }

    InputBoolean.load = function () {
        var inputs = document.querySelectorAll('input[boolean]');
        for (var i = 0; i < inputs.length; i++) {
            inputs[i].removeAttribute('boolean');
            inputs[i].InputBoolean = new InputBoolean(inputs[i]);
        }
    }

    InputBoolean.load();
</script>