/*
 *   (c) Semen Alekseev
 *
 *  For the full copyright and license information, please view the LICENSE
 *   file that was distributed with this source code.
 *
 */

jQuery.uiTableFilter = function (jq, phrase, column, ifHidden) {
    var new_hidden = false;
    if (this.last_phrase === phrase) return false;

    var phrase_length = phrase.length;
    var words = phrase.toLowerCase().split(" ");

    var matches = function (elem) {
        elem.show()
    }
    var noMatch = function (elem) {
        elem.hide();
        new_hidden = true
    }
    var getText = function (elem) {
        return elem.text()
    }

    if (column) {
        var index = null;
        jq.find("thead > tr:last > th").each(function (i) {
            if ($(this).text() == column) {
                index = i;
                return false;
            }
        });
        if (index == null) throw ("given column: " + column + " not found")

        getText = function (elem) {
            return jQuery(elem.find(("td:eq(" + index + ")"))).text()
        }
    }

    if ((words.size > 1) && (phrase.substr(0, phrase_length - 1) === this.last_phrase)) {

        if (phrase[-1] === " ") {
            this.last_phrase = phrase;
            return false;
        }

        var words = words[-1];

        matches = function (elem) {
            ;
        }
        var elems = jq.find("tbody > tr:visible")
    } else {
        new_hidden = true;
        var elems = jq.find("tbody > tr")
    }

    elems.each(function () {
        var elem = jQuery(this);
        jQuery.uiTableFilter.has_words(getText(elem), words, false) ? matches(elem) : noMatch(elem);
    });

    last_phrase = phrase;
    if (ifHidden && new_hidden) ifHidden();
    return jq;
};
jQuery.uiTableFilter.last_phrase = ""
jQuery.uiTableFilter.has_words = function (str, words, caseSensitive) {
    var text = caseSensitive ? str : str.toLowerCase();
    for (var i = 0; i < words.length; i++) {
        if (text.indexOf(words[i]) === -1) return false;
    }
    return true;
}
$(function () {
    var theTable = $('table.food_planner');
    theTable.find("tbody > tr").find("td:eq(1)").mousedown(function () {
        $(this).prev().find(":checkbox").click();
    });
    $("#filter").keyup(function () {
        $.uiTableFilter(theTable, this.value);
    })
});