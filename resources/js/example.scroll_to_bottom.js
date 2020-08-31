import scrollToTopBottom from "./components/back_to_top";

$('#back-to-top').remove();
$('#to-bottom').remove();

let i = new scrollToTopBottom();
i.init();
i.setBtnScroolToTop($('#examle-back-to-top'));
i.setBtnScrollToBottom($('#examle-to-bottom'));
