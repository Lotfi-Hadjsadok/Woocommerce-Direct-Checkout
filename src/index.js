import '../css/style.scss'
jQuery(document).ready(function ($) {
    function isDark(color) {
        var match = /rgb\((\d+).*?(\d+).*?(\d+)\)/.exec(color);
        return parseFloat(match[1])
            + parseFloat(match[2])
            + parseFloat(match[3])
            < 3 * 256 / 2; // r+g+b should be less than half of max (3 * 256)
    }
    const firstContentColor = $('.first-content').css('background-color')
    const secondContentColor = $('.second-content').css('background-color')
    const submitContentColor = $('.rc_submit').css('background-color')
    if (isDark(firstContentColor)) {
        $('.first-content').css('color', 'white')
    }
    if (isDark(secondContentColor)) {
        $('.second-content').css('color', 'white')
    }
    if (isDark(submitContentColor)) {
        $('.rc_submit').css('color', 'white')
    }
})