"use strict";

CodeMirror.defineMode("templateEngine", function (config, parserConfig) {
    function tokenBase(stream, state) {
        const ret = function (prev, style) {
            state.prev = prev;
            return "template-" + (style != undefined ? style : prev);
        }

        var ch = stream.next();
        if (ch === "[") {
            state.openBracktes++;
            return ret("brackets");
        }
        else if (ch === "]") {
            if (state.openBracktes < 1) {
                state.tokenize = tokenError;
                return state.tokenize(stream, state);
            }

            state.openBracktes--;
            return ret("brackets");
        }
        else if (state.openBracktes > 0) {
            if (ch === "@" && state.prev != "variable") {
                stream.eatWhile(/[a-zA-Z0-9_]+/);
                return ret("variable");
            } else if (ch == "=") {
                var next = stream.peek();
                if (next.match(/\[|[0-9]|"|'|@/)) {
                    return ret("equals", "variable");
                } else {
                    state.tokenize = tokenError;
                    return state.tokenize(stream, state);
                }
            } else if ((ch == '"' || ch == "'") && state.prev != "string") {
                state.tokenize = tokenString(ch);
                state.prev = "string";
                return state.tokenize(stream, state);
            } else if (ch == "r" && stream.match(/an\([0-9]+,[0-9]+\)/)) {     // TODO: Should change.. rather dumb lookahead for ran() function.
                return ret("method");
            } else if (ch == "?") {
                if (stream.match(/((\[@[A-Za-z0-9_]+\])|[0-9]),((".*"|'.*'),(".*"|'.*')|[A-Za-z]+)/, false)) { // TODO: Pretty expensive.. Is there a better way?
                    return ret("method");
                } else {
                    state.tokenize = tokenError;
                    return state.tokenize(stream, state);
                }
            } else if (ch == ",") {     // TODO: Should change.. rather dumb lookahead for ran() function.
                return ret("method");
            } else if (ch.match(/[A-Za-z0-9]/)) {
                state.tokenize = tokenTable;
                return state.tokenize(stream, state);
            } else {
                state.tokenize = tokenError;
                return state.tokenize(stream, state);
            }
        }
        return ret("none");
    }


    function tokenError(stream, state) {
        var indent = 1;
        do {
            if (stream.peek() == "[") {
                indent++;
            }
            else if (stream.peek() == "]") {
                indent--;
                if (indent == 0) break;
            }
        } while (stream.next() != null)
        state.tokenize = tokenBase;
        return "template-error";
    }

    function tokenString(quote) {
        return function (stream, state) {
            var next;
            while ((next = stream.next()) != null) {
                if (next == quote) break;
            }
            state.tokenize = tokenBase;
            return "template-string";
        };
    }

    function tokenTable(stream, state) {
        var next;
        while ((next = stream.next()) != null) {
            if (stream.peek() == "]") {
                state.tokenize = tokenBase;
                return "template-string";
            }
            if (stream.peek().match(/[^A-Za-z0-9]/)) {
                state.tokenize = tokenError;
                return state.tokenize(stream, state);
            }
        }
    }

    return {
        startState: function (basecolumn) {
            var state = {
                tokenize: tokenBase,
                prev: null,
                openBracktes: 0,
                commandError: false,
            };
            return state;
        },

        token: function (stream, state) {
            if (stream.eatSpace()) return null;
            var style = state.tokenize(stream, state);
            return style;
        },
    };
});