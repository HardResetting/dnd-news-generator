    function addVar(event) {
        document.getElementById("value").value += '[@var_X(Min,Max)]';
    }

    function addType(event) {
        var e = document.getElementById("type_name");
        var type_name = e.value;
        document.getElementById("value").value += '[' + type_name + ']';
    }

    function addType_mult(event) {
        var e = document.getElementById("type_name");
        var type_name = e.value;
        document.getElementById("value").value += '[@var_X,' + type_name + ']';
    }

    function addChoice(event) {
        document.getElementById("value").value += '[?@var_X,\'Wahl1\',\'Wahl2\']';
    }